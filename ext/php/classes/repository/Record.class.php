<?php
/**
 * @package repository
 */
require_once 'DependentRelation.class.php';
require_once 'store/StorageAdaptor.class.php';
require_once 'language/en/Inflect.class.php';

/**
 * The infamous active record implementation from textme.co.nz
 * 
 * Most ideas from Rails ActiveRecord and the Relational Aspect Library,
 * but with tradeoffs for idiomatic PHP.
 * 
 * It is slowly gaining features and growing wings.
 */
class Record {
	var $_table;
	var $_record;
	var $_storage;
	var $_fields;
	var $_relations;
	var $_rules;
	var $_errors;
	private $_clean;

	function __construct($record = false) {
		$this->_clean = true;
		$this->_storage = StorageAdaptor::instance();
		$this->_table = strtolower(Inflect::tableize(get_class($this)));
		$this->_fields = array();
		$this->_relations = array();
		$this->_rules = array();
		$this->_errors = array();
		$this->_record = new stdClass();
		if (method_exists($this, '__define')) {
			$this->__define();
		}
		if ($record) {
			foreach($record as $field=>$value) {
				if ($field == 'id') {
					$this->_record->id = $value;
				} else {
					$property = Inflect::columnToProperty($field);
					if (array_key_exists($property, $this->_fields))
						$this->_record->$property = $value;
					}
				}
			}
	}
 
	/**
	 * Add a dependent association to this record.
	 * 
	 * This definition will map a foreign key linking to the defined
	 * record type.
	 */
	function belongsTo($entity) {
		$this->property(strtolower($entity)."Id", "integer");
		$this->hasRelation(new DependentRelation($this, $entity));
	}

	function hasRelation($entity) {
		$this->_relations[get_class($entity)] = $entity;
	}	
	
	/**
	 * Add a collection assocation to this record.
	 * 
	 * Collections are currently implemented as flat arrays, which
	 * makes the tests pass, but is not a particularly pleasing
	 * approach.
	 * 
	 * @todo clean up the hasRelation method
	 * @todo provide a more complete set of relationship aspects
	 */
	function hasMany($ofCollection) {
		$this->_relations[$ofCollection] = array();
	}

	/**
	 * Define a property mapping.
	 * 
	 * Allowed types are:
	 * 	- string
	 *  - integer
	 *  - float
	 *  - datetime 
	 */
	function property($name, $type) {
		$this->_record->$name = null;
		$this->_fields[$name] = $type;
	}
	
	/**
	 * Return a list of the property mappings
	 * defined for this record.
	 * 
	 * (a virtual version of get_class_vars)
	 */
	function properties() {
		return $this->_fields;
	}

	/**
	 * Add a validation rule to this record.
	 * 
	 */
	function rule($field, $rule) {
		if (!isset($this->_rules[$field])) {
			$this->_rules[$field] = array();
		}
		if (is_string($rule)) {
			$rule = new $rule;
		}
		$this->_rules[$field][] = $rule;
	}

	function __get($key) {
		if (array_key_exists($key, $this->_relations)) {
			if (is_array($this->_relations[$key])) {
				if (empty($this->_relations[$key])) {
					$this->_storage->selectById($key, $this->id);
					return $this->_storage->getRecords();
				} else {
					return $this->_relations[$key];
				}
			}
		} elseif (array_key_exists($key, $this->_fields)) {
			switch($this->_fields[$key]) {
				case 'string':
				case 'text':
					return $this->_getString($key);
					break;
				case 'int':
				case 'integer':
					return $this->_getInteger($key);
					break;
				case 'float':
					return $this->_getFloat($key);
					break;
				case 'datetime':
					return $this->_getValue($key, 'DateTime');
					break;
			}
		} elseif ($key == 'id') {
			return (isset($this->_record->id)) ? $this->_record->id : 0;
		}
	}
	
	/**
	 * Has the data changed since first load?
	 */
	function isDirty() {
		return (!$this->_clean);
	}
	
	/**
	 * Is the data clean? (in the same state as first load)
	 */
	function isClean() {
		return $this->_clean;
	}

	function __set($key, $value) {
		if (array_key_exists($key, $this->_relations)) {
			if (is_array($this->_relations[$key])) {
				$this->_relations[$key][] = $value;
			}
		} elseif (array_key_exists($key, $this->_fields)) {
			$this->_record->$key = $value;
			$this->_clean = false;
		}
	}

	function _getId() {
		return ($this->_record) ? $this->_record->id : 0;
	}

	function _getString($property) {
		return ($this->_record) ? $this->_record->$property : null;
	}

	function _getInteger($property) {
		return ($this->_record) ? (int)$this->_record->$property : null;
	}

	function _getFloat($property) {
		return ($this->_record) ? (float)$this->_record->$property : null;
	}

	function _getValue($property, $type) {
		if ($this->_record) {
			return new $type($this->_record->$property);
		} else {
			return new $Type();
		}
	}
	
	/**
	 * Validate the data stored for this record against
	 * the defined rules.
	 * 
	 * Validation is shallow - relationships are not included,
	 * only the defined properties of the record.
	 */
	function validate() {
		foreach(get_object_vars($this->_record) as $field=>$value) {
			if (isset($this->_rules[$field])) {
				foreach($this->_rules[$field] as $rule) {
					if (!$rule->validate($value, $this)) {
						$this->_errors[$field] = $rule->message;
					}
				}
			}
		}
	}
	
	/**
	 * Is the data currently set for this record valid according
	 * to its defined rules?
	 */
	function isValid() {
		$this->validate();
		return (count($this->_errors) == 0);
	}

	/**
	 * Returns the list of errors caught by validation rules.
	 */
	function getErrors() {
		return $this->_errors;
	}

	/**
	 * Save this record to the persistent store.
	 * 
	 * Runs validation by default, unless overriden in the
	 * method call. If the validation rules fail, then the object
	 * is not saved.
	 * 
	 * @todo smarter exception handling
	 */
	function save($validate=true) {
		if (!$this->saveRelations()) return false;
		if (!$validate || $this->isValid()) {
			$record = array();
			foreach(get_object_vars($this->_record) as $key=>$value) {
				$record[Inflect::propertyToColumn($key)] = $value;
			}
			if ($this->id != 0) {
				$this->_storage->update($this->_table, array('id'=>$this->id), $record);
				return true;
			} else {
				$this->_storage->insert($this->_table, $record);
				$this->_record->id = $this->_storage->insertId();
				return true;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Recursively save each record implemented in a relationship.
	 * 
	 * Probably also need to use transactions here to ensure referential integrity.
	 */
	private function saveRelations() {
		foreach($this->_relations as $association) {
			if (is_array($association)) {
				foreach($association as $model) {
					if ($model->isDirty) {
						if (!$model->save()) return false;
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Remove a record from the persistent store.
	 */
	function delete($id=false) {
		if (!$id) {
			$id = $this->_storage->insertId();
		}
		$this->_storage->delete($this->_table, array('id'=>$id));
	}

}

?>
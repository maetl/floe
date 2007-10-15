<?php
/**
 * @package repository
 */
require_once 'DependentRelation.class.php';
require_once 'store/StorageAdaptor.class.php';
require_once dirname(__FILE__) .'/../language/en/Inflect.class.php';

/**
 * The infamous active record implementation from textme.co.nz
 * 
 * Most ideas from Rails ActiveRecord and the Relational Aspect Library,
 * but with tradeoffs for idiomatic PHP.
 * 
 * It is slowly gaining features and growing wings.
 */
class Record {
	private $_table;
	private $_record;
	protected $_storage;
	private $_properties;
	private $_joins;
	private $_associations;
	private $_relations;
	private $_rules;
	private $_errors;
	private $_clean;
	
	private $_dependent_relations;
	private $_associated_relations;
	private $_parent_relations;

	function __construct($record = false) {
		$this->_dependent_relations = array();
		$this->_associated_relations = array();
		$this->_clean = true;
		$this->_storage = StorageAdaptor::instance();
		$this->_table = strtolower(Inflect::toTableName(get_class($this)));
		$this->_properties = array();
		$this->_joins = array();
		$this->_associations = array();
		$this->_relations = array();
		$this->_rules = array();
		$this->_errors = array();
		$this->_record = new stdClass();
		if (method_exists($this, '__define')) $this->__define();
		if ($record) {
			if (is_numeric($record)) {
				$record = $this->findObjectById($record);
			}
			foreach($record as $field=>$value) {
				if ($field == 'id') {
					$this->_record->id = $value;
				} else {
					$property = Inflect::columnToProperty($field);
					if (array_key_exists($property, $this->_properties))
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
	function belongsTo($type) {
		$this->property(strtolower($type)."Id", "integer");
		$this->_parent_relations[$type] = 0;
	}
	
	/**
	 * Add a collection assocation to this record.
	 * 
	 * Collections are currently implemented as flat arrays, which
	 * makes the tests pass, but is not a particularly pleasing
	 * approach.
	 * 
	 * @todo clean up the relations and joins attributes
	 */
	function hasMany($ofType) {
		$this->_dependent_relations[$ofType] = array();
	}

	/**
	 * Adds a many to many relationship to the model.
	 * 
	 * This requires a join table to be set up, using the Rails idiom,
	 * with the caveat that the tables referenced must be in alphabetical
	 * order (eg: entries_tags, not tags_entries).
	 * 
	 * @todo provide a more complete set of relationship aspects
	 */
	function hasManyRelations($relatedTo) {
		$joins = array(strtolower(Inflect::toPlural(get_class($this))), $relatedTo);
		sort($joins);
		$this->_joins[$relatedTo] = $joins[0] . "_" . $joins[1];
		$this->_associations[$relatedTo] = array();
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
		$this->_properties[$name] = $type;
	}
	
	/**
	 * Return a list of the property mappings
	 * defined for this record.
	 * 
	 * (a virtual version of get_class_vars)
	 */
	function properties() {
		return $this->_properties;
	}

	/**
	 * Add a validation rule to this record.
	 * 
	 * Validation rules should implement the ValidationRule interface,
	 * but this is not enforced by the current code, to allow for
	 * several legacy applications that overload parameters to
	 * the ValidationRule::validate method.
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

	/**
	 * Virtual property accessor
	 */
	function __get($key) {
		if (array_key_exists($key, $this->_joins)) {
			$this->_storage->selectByAssociation($key, $this->_joins[$key]);
			return $this->_storage->getRecords();
		} elseif (array_key_exists($key, $this->_dependent_relations)) {
			if (is_array($this->_dependent_relations[$key])) {
				if (empty($this->_dependent_relations[$key])) {
					$field = strtolower(Inflect::toSingular(get_class($this)))."_id";
					$this->_storage->selectByKey($key, array($field=>$this->id));
					$this->_dependent_relations[$key] = $this->_storage->getRecords();
					return $this->_dependent_relations[$key];
				} else {
					return $this->_dependent_relations[$key];
				}
			}
		} elseif (array_key_exists($key, $this->_properties)) {
			switch($this->_properties[$key]) {
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
	 * Virtual property writer.
	 */
	function __set($key, $value) {
		if (array_key_exists($key, $this->_associations)) {
			$this->_associations[$key][] = $value;
		} elseif (array_key_exists($key, $this->_dependent_relations)) {
			if (is_array($this->_dependent_relations[$key])) {
				$this->_dependent_relations[$key][] = $value;
			}
		} elseif (array_key_exists($key, $this->_properties)) {
			$this->setProperty($key, $value);
		} elseif($key == "id") {
			$this->_record->id = $value;
			$foreignKey = strtolower(get_class($this))."Id";
			foreach($this->_dependent_relations as $relation) {
				if (is_array($relation)) {
					foreach($relation as $model) {
						$model->setProperty($foreignKey, $value);
					}
				}
			}
		}
	}

	/**
	 * Has the data changed since first load?
	 */
	function isDirty() {
		return (!$this->_clean);
	}
	
	/**
	 * Set a property value.
	 */
	function setProperty($property, $value) {
		$this->_record->$property = $value;
		$this->_clean = false;
	}
	
	/**
	 * Is the data clean? (in the same state as first load)
	 */
	function isClean() {
		return $this->_clean;
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
	function save($validate=true, $recursive=true) {
		if (!$validate || $this->isValid()) {
			$record = array();
			foreach(get_object_vars($this->_record) as $key=>$value) {
				$record[Inflect::propertyToColumn($key)] = $value;
			}
			if ($this->id != 0) {
				$this->_storage->update($this->_table, array('id'=>$this->id), $record);
			} else {
				$this->_storage->insert($this->_table, $record);
				$this->id = $this->_storage->insertId();
			}
			if ($recursive) {
				if (!$this->saveAssociations()) return false;
				if (!$this->saveRelations()) return false;
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Recursively save associations
	 */
	private function saveAssociations() {
		foreach($this->_associations as $key=>$association) {
			if (is_array($association)) {
				$table = $this->_joins[$key];
				$self_id = $this->id;
				$self_join = strtolower(get_class($this)) . "_id";
				$this->_storage->delete($table, array($self_join=>$self_id));		
				foreach($association as $model) {
					if ($model->save(true, false)) {
						$model_id = $model->id;
						$model_join = strtolower(get_class($model)) . "_id";
						$this->_storage->insert($table, array($self_join=>$self_id, $model_join=>$model_id));
					} else {
						return false;
					}
				}
			}
		}
		return true;
	}	
	
	/**
	 * Recursively save each record implemented in a relationship.
	 * 
	 * Probably also need to use transactions here to ensure referential integrity.
	 */
	private function saveRelations() {
		foreach($this->_dependent_relations as $relation) {
			if (is_array($relation)) {
				foreach($relation as $model) {
					if ($model->isDirty()) {
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
	
	function remove() {
		$this->_storage->delete($this->_table, array('id'=>$this->id));
	}

	/**
	 * collection method
	 */
	function findAll() {
		$this->_storage->selectAll(Inflect::toPlural(strtolower(get_class($this))));
		return $this->_storage->getRecords();
	}
	
	function findById($id) {
		$this->_storage->selectById(Inflect::toPlural(strtolower(get_class($this))), $id);
		return $this->_storage->getRecord();
	}
	
	function findObjectById($id) {
		$this->_storage->selectById(Inflect::toPlural(strtolower(get_class($this))), $id);
		return $this->_storage->getObject();
	}

	function findByKey($key, $value) {
		$this->_storage->selectByKey(Inflect::toPlural(strtolower(get_class($this))), array("name"=>$value));
		$record = $this->_storage->getRecords();
		return $record[0];
	}

	function findAllByKey($key, $value) {
		$this->_storage->selectByKey(Inflect::toPlural(strtolower(get_class($this))), array($key=>$value));
		return $this->_storage->getRecords();
	}
	
}

?>
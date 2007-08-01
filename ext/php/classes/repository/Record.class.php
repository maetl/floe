<?php
// $Id: Entity.class.php 64 2007-07-02 15:48:24Z maetl_ $
/**
 * @package repository
 */
require_once 'DependentRelation.class.php';
require_once 'store/StorageAdaptor.class.php';

class Record {
	var $_table;
	var $_record;
	var $_storage;
	var $_fields;
	var $_relations;
	var $_rules;
	var $_errors;

	function __construct($record = false) {
		$this->_storage = StorageAdaptor::instance();
		$this->_table = strtolower(Inflector::tableize(get_class($this)));
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
					$property = Inflector::columnToProperty($field);
					if (array_key_exists($property, $this->_fields))
						$this->_record->$property = $value;
					}
				}
			}
	}
 
	
	function hasRelation($entity) {
		$this->_relations[get_class($entity)] = $entity;
	}

	function belongsTo($entity) {
		$this->property(strtolower($entity)."Id", "integer");
		$this->hasRelation(new DependentRelation($this, $entity));
	}

	function hasMany($ofCollection) {
		$this->_relations[$ofCollection] = new $ofCollection();
	}

	function property($name, $type) {
		$this->_record->$name = null;
		$this->_fields[$name] = $type;
	}

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
		if (array_key_exists($key, $this->_fields)) {
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

	function __set($key, $value) {
		if (array_key_exists($key, $this->_fields)) {
			$this->_record->$key = $value;
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

	function _getLazyAssociation($foreignKey, $adaptor) {
		if ($this->_record) {
			$table = Inflector::toTableName($foreignKey);
			$EntityType = Inflector::toClassName($foreignKey);
			$adaptor->selectById($table, $this->_record->$foreignKey);
			return new $EntityType($adaptor->getObject(), $this->_scope);
		} else {
			return new $EntityType();
		}
	}

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

	function isValid() {
		$this->validate();
		return (count($this->_errors) == 0);
	}

	function getErrors() {
		return $this->_errors;
	}

	function save($validate=true) {
		if (!$validate || $this->isValid()) {
			$record = array();
			foreach(get_object_vars($this->_record) as $key=>$value) {
				$record[Inflector::propertyToColumn($key)] = $value;
			}
			if ($this->id != 0) {
				$this->_storage->update($this->_table, array('id'=>$this->id), $record);
				return true;
			} else {
				$this->_storage->insert($this->_table, $record);
				return true;
			}
		} else {
			return false;
		}
	}

	function delete($id=false) {
		if (!$id) {
			$id = $this->_storage->insertId();
		}
		$this->_storage->delete($this->_table, array('id'=>$id));
	}

}

?>
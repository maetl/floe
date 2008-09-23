<?php
/**
 * @package repository
 */
require_once 'DependentRelation.class.php';
require_once 'store/StorageAdaptor.class.php';
require_once dirname(__FILE__) .'/../language/en/Inflect.class.php';

/**
 * Active record base class.
 * 
 * @package repository
 */
class Record {
	protected $storage;
	private $tableName;
	private $recordInstance;
	private $properties;
	private $joins;
	private $associations;
	private $rules;
	private $errors;
	private $clean;
	private $dependentRelations;
	private $parentRelations;

	function __construct($record = false) {
		$this->dependentRelations = array();
		$this->parentRelations = array();
		$this->clean = true;
		$this->storage = StorageAdaptor::instance();
		$this->properties = array();
		$this->joins = array();
		$this->associations = array();
		$this->rules = array();
		$this->errors = array();
		$this->recordInstance = new stdClass();
		if (get_parent_class($this) == 'Record') {
			if (method_exists($this, '__define')) $this->__define();
			$this->tableName = Inflect::toTableName(get_class($this));
		} else {
			$this->initializeDefinedAncestors();
			$this->property("type", "string");
			$this->setProperty("type", get_class($this));
		}
		if ($record) {
			if (is_numeric($record)) {
				$record = $this->findObjectById($record);
				foreach ($this->parentRelations as $key => $val) {
					$nameProperty = $key."Id";
					$this->storage->selectById(Inflect::toTableName($key), $record->$nameProperty);
					$this->parentRelations[$key] = $this->storage->getRecord();
				}
			}
			foreach($record as $field=>$value) {
				if ($field == 'id') {
					$this->recordInstance->id = $value;
				} else {
					$this->setProperty($field, $value);
				}
			}
		}
	}
	
	/**
	 * Update the record with values given from supplied object or hash.
	 *
	 * @return void
	 */
	public function populate($record) {
		foreach($record as $field=>$value) {
			if ($field == 'id') {
				$this->recordInstance->id = $value;
			} else {
				$this->$field = $value;
			}
		}		
	}
	
	/**
	 * Traverse the inheritance chain to define parent properties
	 * of a single table inheritance mapping.
	 */
	private function initializeDefinedAncestors() {
		$class = get_class($this);
		while ($class != 'Record') {
			$method = new ReflectionMethod($class, '__define');
			$method->invoke($this);
			$this->tableName = Inflect::toTableName($class);
			$class = get_parent_class($class);
		}
	}
	
	/**
	 * Returns the name of the storage table that this record maps to.
	 * 
	 * @return string
	 */
	function getTable() {
		return $this->tableName;
	}
	
	/**
	 * Add a dependent association to this record.
	 * 
	 * This definition will map a foreign key linking to the defined
	 * record type.
	 */
	function belongsTo($type) {
		$this->property(strtolower($type)."Id", "integer");
		$this->parentRelations[$type] = null;
	}
	
	/**
	 * @return boolean
	 */
	private function hasParentRelation($key) {
		return array_key_exists($key, $this->parentRelations);
	}
	
	/**
	 * Add a collection association to this record.
	 * 
	 * Collections are currently implemented as flat arrays, which
	 * makes the tests pass, but is not a particularly pleasing
	 * approach.
	 * 
	 * @todo clean up the relations and joins attributes
	 */
	function hasMany($ofType) {
		$this->dependentRelations[$ofType] = array();
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
		$this->joins[$relatedTo] = $joins[0] . "_" . $joins[1];
		$this->associations[$relatedTo] = array();
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
		$this->recordInstance->$name = null;
		$this->properties[$name] = $type;
	}
	
	/**
	 * Return a list of the property mappings
	 * defined for this record.
	 * 
	 * (a virtual version of get_class_vars)
	 */
	function properties() {
		return $this->properties;
	}
	
	/**
	 * Return a list of the many to many
	 * relationship mappings for this record.
	 */
	function relations() {
		return $this->joins;
	}
	
	/**
	 * Return a list of the owner
	 * mappings for this record (belongsTo)
	 */
	function parents() {
		return $this->parentRelations;
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
		if (!isset($this->rules[$field])) {
			$this->rules[$field] = array();
		}
		if (is_string($rule)) {
			$rule = new $rule;
		}
		$this->rules[$field][] = $rule;
	}
	
	/**
	 * Accesses a parent object, going to the database if
	 * it doesn't exist in the raw property map.
	 * 
	 * @todo use identity map to hold objects
	 */
	private function getParentRelation($key) {
		if ($this->parentRelations[$key] == null) {
			$recordType = Inflect::toClassName($key);
			$recordKey = $key . "Id";
			$this->parentRelations[$key] = new $recordType($this->$recordKey);
		}
		return $this->parentRelations[$key];
	}

	/**
	 * Virtual property accessor
	 * 
	 * @todo separate Date and DateTime types and wrap with value object that supports __toString
	 */
	function __get($key) {
		$getter =  'get'. Inflect::toClassName($key);
		if (method_exists($this, $getter)) {
			return $this->$getter();
		}
		if ($this->hasParentRelation($key)) {
			return $this->getParentRelation($key);
		} elseif (array_key_exists($key, $this->joins)) {
			$this->storage->selectByAssociation($key, $this->joins[$key]);
			return $this->storage->getRecords();
		} elseif (array_key_exists($key, $this->dependentRelations)) {
			if (is_array($this->dependentRelations[$key])) {
				if (empty($this->dependentRelations[$key])) {
					$field = strtolower(Inflect::toSingular(get_class($this)))."_id";
					$this->storage->selectByKey(Inflect::underscore($key), array($field=>$this->id));
					$this->dependentRelations[$key] = $this->storage->getRecords();
					return $this->dependentRelations[$key];
				} else {
					return $this->dependentRelations[$key];
				}
			}
		} elseif ($this->hasProperty($key)) {
			return $this->_castPropertyType($key);
		} elseif ($key == 'id') {
			return (isset($this->recordInstance->id)) ? $this->recordInstance->id : 0;
		}
	}
	
	/**
	 * Does this record have the given property
	 * 
	 * @return boolean
	 */
	private function hasProperty($key) {
		return array_key_exists($key, $this->properties);
	}
	
	/**
	 * Cast string from storage source to native type in accessor
	 * 
	 * @return mixed
	 */
	private function _castPropertyType($key) {
		$type = $this->properties[$key];
		switch($type) {
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
			case 'boolean':
				return $this->_getBoolean($key);
				break;
			case 'date':
			case 'datetime':
				return $this->_getValue($key, 'DateTime');
				break;
			default:
				if (class_exists($type)) return $this->_getValue($key, $type);
				break;
		}
	}
	
	/**
	 * Virtual property writer.
	 */
	function __set($key, $value) {
		$setter =  'set'. Inflect::toClassName($key);
		if (method_exists($this, $setter)) {
			$this->$setter($value);
			return;
		}
		if ($this->hasParentRelation($key)) {
			$this->parentRelations[$key] = $value;
		} elseif (array_key_exists($key, $this->associations)) {
			$this->associations[$key][] = $value;
		} elseif (array_key_exists($key, $this->dependentRelations)) {
			if (is_array($this->dependentRelations[$key])) {
				$this->dependentRelations[$key][] = $value;
			}
		} elseif($key == "id") {
			$this->recordInstance->id = $value;
			$foreignKey = strtolower(get_class($this))."Id";
			foreach($this->dependentRelations as $relation) {
				if (is_array($relation)) {
					foreach($relation as $model) {
						$model->setProperty($foreignKey, $value);
					}
				}
			}
		} else {
			$this->setProperty($key, $value);
		}
	}

	/**
	 * Has the data changed since first load?
	 */
	function isDirty() {
		return (!$this->clean);
	}
	
	/**
	 * Set a property value.
	 */
	function setProperty($property, $value) {
		$property = Inflect::columnToProperty($property);
		if (array_key_exists($property, $this->properties)) {
			if (is_a($value, 'DateTime')) {
				$value = $value->format('Y-n-d H:i:s');
			}
			if ($this->properties[$property] == 'date') {
				$value = date('Y-n-d', strtotime($value));
			}
			$this->recordInstance->$property = $value;
			$this->clean = false;
		}
	}
	
	function getProperty($property) {
		return $this->recordInstance->$property;
	}
	
	/**
	 * Is the data clean? (in the same state as first load)
	 */
	function isClean() {
		return $this->clean;
	}	
	
	function _getId() {
		return ($this->recordInstance) ? $this->recordInstance->id : 0;
	}

	function _getString($property) {
		return ($this->recordInstance) ? $this->recordInstance->$property : null;
	}

	function _getInteger($property) {
		return ($this->recordInstance) ? (int)$this->recordInstance->$property : null;
	}

	function _getFloat($property) {
		return ($this->recordInstance) ? (float)$this->recordInstance->$property : null;
	}
	
	function _getBoolean($property) {
		return ($this->recordInstance) ? (boolean)$this->recordInstance->$property : null;
	}

	function _getValue($property, $type) {
		if ($this->recordInstance) {
			return new $type($this->recordInstance->$property);
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
		foreach(get_object_vars($this->recordInstance) as $field=>$value) {
			if (isset($this->rules[$field])) {
				foreach($this->rules[$field] as $rule) {
					if (!$rule->validate($value, $this)) {
						$this->errors[$field] = $rule->message;
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
		return (count($this->errors) == 0);
	}

	/**
	 * Returns the list of errors caught by validation rules.
	 */
	function getErrors() {
		return $this->errors;
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
			foreach($this->parentRelations as $key => $owner) {
				if (is_object($owner)) {
					if ($owner->id == 0) $owner->save();
					$this->setProperty($key."Id", $owner->id);
				}
			}
			$record = array();
			foreach(get_object_vars($this->recordInstance) as $key=>$value) {
				$record[Inflect::propertyToColumn($key)] = $value;
			}
			if ($this->id != 0) {
				$this->storage->update($this->tableName, array('id'=>$this->id), $record);
			} else {
				$this->storage->insert($this->tableName, $record);
				$this->id = $this->storage->insertId();
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
		foreach($this->associations as $key=>$association) {
			if (is_array($association)) {
				$table = $this->joins[$key];
				$self_id = $this->id;
				$self_join = strtolower(get_class($this)) . "_id";
				$this->storage->delete($table, array($self_join=>$self_id));		
				foreach($association as $model) {
					if ($model->save(true, false)) {
						$model_id = $model->id;
						$model_join = strtolower(get_class($model)) . "_id";
						$this->storage->insert($table, array($self_join=>$self_id, $model_join=>$model_id));
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
		foreach($this->dependentRelations as $relation) {
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
			$id = $this->storage->insertId();
		}
		$this->storage->delete($this->tableName, array('id'=>$id));
	}
	
	function remove() {
		$this->storage->delete($this->tableName, array('id'=>$this->id));
	}

	/**
	 * collection method
	 */
	function findAll() {
		$this->storage->selectAll($this->tableName);
		return $this->storage->getRecords();
	}
	
	function findById($id) {
		$this->storage->selectById($this->tableName, $id);
		return $this->storage->getRecord();
	}
	
	function findObjectById($id) {
		$this->storage->selectById($this->tableName, $id);
		return $this->storage->getObject();
	}

	function findByKey($key, $value) {
		$this->storage->selectByKey($this->tableName, array("name"=>$value));
		$record = $this->storage->getRecords();
		return $record[0];
	}

	function findAllByKey($key, $value) {
		$this->storage->selectByKey($this->tableName, array($key=>$value));
		return $this->storage->getRecords();
	}
	
}

?>
<?php
require_once 'Record.class.php';

/**
 * Base class for collection mappers.
 */
class Finder {
	
	private $storage;
	private $tableName;
	
	protected $returnRecords = true;
	
	function __construct() {
		$this->storage = StorageAdaptor::instance();
		$this->tableName = strtolower(str_replace('Finder', '', get_class($this)));
	}
	
	function findById($id) {
		$this->storage->selectById($this->tableName, $id);
		return $this->storage->getRecord();
	}
	
	function findByKey($key, $value) {
		$this->storage->selectByKey($this->tableName, array($key=>$value));
		return $this->storage->getRecords();
	}
	
	function findAll() {
		$this->storage->selectAll($this->tableName);
		return $this->storage->getRecords();
	}
	
	function findByCriteria($query) {
		$this->storage->selectAll($this->tableName, $query);
		return $this->storage->getRecords();	
	}
	
}

?>
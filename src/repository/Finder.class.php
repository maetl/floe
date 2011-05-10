<?php
/**
* This file is part of Floe, a graceful web framework.
* Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
*
* See the LICENSE file distributed with this software for full copyright, disclaimer
* of liability, and the specific limitations that govern the use of this software.
*
* @package repository
 */
require_once 'Record.class.php';

/**
 * Base class for collection finders.
 *
 * @package repository
 */
class Finder {
	
	protected $storage;
	protected $cache;
	protected $record;
	protected $tableName;
	
	function __construct($store=false, $cache=false) {
		$this->storage = Storage::init();
		$collectionName = str_replace('Finder', '', get_class($this));
		$recordName = Inflect::toSingular($collectionName);
		$this->record = new $recordName;
		$this->tableName = Inflect::toTableName($recordName);
	}

	/**
	 * Find a single record based on given primary key id.
	 *
	 * @param integer $id
	 * @return Record
	 */	
	function findById($id) {
		$this->storage->selectById($this->tableName, $id);
		return $this->storage->getRecord();
	}
	
	/**
	 * Find records by a key=value comparison.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return array<Record>
	 */
	function findByKey($key, $value) {
		$this->storage->selectByKey($this->tableName, array($key=>$value));
		return $this->storage->getRecords();
	}
	
	/**
	 * Return all existing records.
	 *
	 * @return array<Record>
	 */
	function findAll() {
		$this->storage->selectAll($this->tableName);
		return $this->storage->getRecords();
	}
	
	/**
	 * Return a list of records filtered by given criteria.
	 *
	 * @param Query $query
	 * @return array<Record>
	 */
	function findByCriteria($query) {
		$this->storage->select($this->tableName, $query->toSql());
		return $this->storage->getRecords();
	}

	/**
	 * Return a single record based on given criteria.
	 *
	 * @param Query $query
	 * @return array<Record>
	 */
	function findOneByCriteria($query) {
		$this->storage->select($this->tableName, $query->toSql());
		return $this->storage->getRecord();
	}
	
	/**
	 * Meta-find method.
	 *
	 * This will generate a dynamic query based on properties of the wrapped
	 * record schema.
	 *
	 * Not recommended to rely on this for production usage, as it involves a much slower
	 * method dispatch than simply implementing a concrete find method.
	 *
	 * Select on multiple fields by using 'And' in the camelCase format.
	 * 
	 * eg: 
	 *     $finder->findByTitle($title);
	 *     $finder->findByFirstNameAndLastName($firstName);
	 */
	function __call($method, $params) {
		if (!strstr($method, 'findBy')) throw new Exception("Method $method doesn't exist");
		$properties = $this->record->properties();
		$propertyName = str_replace('findBy', '', $method);
		$requestedProperties = explode('And', $propertyName);
		if (count($requestedProperties) != count($params)) throw new Exception("Wrong parameter count for $method");
		$query = new MysqlQuery();
		$count = 0;
		foreach($requestedProperties as $property) {
			$query->whereEquals($property, $params[$count]);
			$count++;
		}
		$this->storage->select($this->tableName, $query->toSql());
		return $this->storage->getRecord();
	}
	
}


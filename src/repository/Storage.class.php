<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id: Storage.class.php 421 2010-05-23 20:28:19Z coretxt $
 * @package repository
 */

if (!defined('Storage_DefaultInstance')) define('Storage_DefaultInstance', 'Mysql');

/**
 * Wrapper around the adaptor to a specific storage engine.
 *
 * @package repository
 */
class Storage {

		private static $storageAdaptor = null;
		private static $implementation = null;
		private $recordType;
		private $adaptor;
		
		/**
		 * Access a global instance of the Storage wrapper.
		 */
        static function init($adaptor = false) {
        	if (!self::$storageAdaptor) self::$storageAdaptor = new Storage(self::adaptor());
        	return self::$storageAdaptor;
        }

		/**
		 * Access a global instance of a service adaptor.
		 */
        static function adaptor($service = false) {
			$adaptor = ($service) ? $service : Storage_DefaultInstance;
			$gateway = $adaptor.'Adaptor';
			$connection = $adaptor.'Connection';
			require_once 'services/'. strtolower($adaptor) .'/'. $gateway .'.class.php';
        	if (!self::$implementation && !$service) {
				self::$implementation = new $gateway(new $connection());
        		return self::$implementation;
			} else{
				return new $gateway(new $connection());
			}
        }
		
		/**
		 * @ignore
		 */
		function __construct($adaptor) {
			$this->adaptor = $adaptor;
		}
		
		/**
		 * Converts a passed in value to a record class name.
		 */
		function setRecordType($type) {
			$this->recordType = Inflect::toClassName(Inflect::toSingular($type));
		}
		
		/**
		 * Provides a single row query result as an active record object.
		 * @return stdClass
		 */
		function getRecord() {
			$object = $this->getObject();
			if ($object) {
				if (isset($object->type)) {
					$record = $object->type;
				} else {
					$record = $this->recordType;				
				}
				return new $record($object);
			} else {
				return null;
			}
		}
		
		/**
		 * Provides a multi row result set as a list of active record objects.
		 * @todo fix the suck of double looping through the list
		 * @return array<Record>
		 */
		function getRecords() {
			$type = $this->recordType;
			$objects = $this->adaptor->getObjects();
			$records = array();
			foreach($objects as $object) {
				$records[] = new $type($object);
			}
			return $records;
		}

		/**
		 * Provides a single row query result as an untyped object.
		 * @return stdClass
		 */
		function getObject() {
			return $this->adaptor->getObject();
		}
		
		/**
		 * Provides a multi row result set as a list of untyped objects.
		 * @return array<strClass>
		 */
		function getObjects() {
			return $this->adaptor->getObjects();
		}
		
		/**
		 * Provides a single value from a query result.
		 */
		function getValue() {
			return $this->adaptor->getValue();
		}
		
		/**
		 * Provides an iterator over a result set.
		 */
		function getIterator() {
			return $this->adaptor->getIterator();
		}		

		/**
		 * @deprecated
		 */
		function selectById($table, $id) {
			$this->setRecordType($table);
			return $this->adaptor->selectById($table, $id);
		}
		
		/**
		 * @deprecated
		 */
		function selectByKey($table, $key) {
			$this->setRecordType($table);
			return $this->adaptor->selectByKey($table, $key);
		}
		
		/**
		 * @deprecated
		 */
		function selectAll($table) {
			$this->setRecordType($table);
			return $this->adaptor->selectAll($table);
		}
		
		/**
		 * @deprecated
		 */
		function select($table, $target) {
			$this->setRecordType($table);
			return $this->adaptor->select($table, $target);
		}
		
		/**
		 * @deprecated
		 */
		function selectByAssociation($table, $join_table, $target=false) {
			$this->setRecordType($table);
			return $this->adaptor->selectByAssociation($table, $join_table, $target);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function insertId() {
			return $this->adaptor->insertId();
		}		

		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function insert($table, $properties) {
			$this->setRecordType($table);
			return $this->adaptor->insert($table, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function update($table, $target, $properties) {
			$this->setRecordType($table);
			return $this->adaptor->update($table, $target, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function delete($table, $matching) {
			$this->setRecordType($table);
			return $this->adaptor->delete($table, $matching);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 * @todo better object check
		 */
		function query($statement) {
			if (is_object($statement)) $this->setRecordType($statement->tableName);
			return $this->adaptor->query($statement);
		}

		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function createTable($name, $properties) {
			$this->setRecordType($name);
			return $this->adaptor->createTable($name, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropTable($name) {
			$this->setRecordType($name);
			return $this->adaptor->dropTable($name);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function addColumn($table, $column, $type) {
			$this->setRecordType($table);
			return $this->adaptor->addColumn($table, $column, $type);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropColumn($table, $column) {
			$this->setRecordType($table);
			return $this->adaptor->dropColumn($table, $column);
		}
		
}

?>
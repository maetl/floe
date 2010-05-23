<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package repository
 * @subpackage store
 */

if (!defined('StorageAdaptor_DefaultInstance')) define('StorageAdaptor_DefaultInstance', 'Mysql');

/**
 * High level wrapper around the gateway to a specific storage engine.
 *
 * @package repository
 * @subpackage store
 */
class StorageAdaptor {

		private static $adaptor = null;
		private static $implementation = null;
		private $currentRecordType;
		
		/**
		 * Access a singleton instance of the StorageAdaptor
		 */
        static function instance($adaptor = 'Mysql') {
        	if (!self::$adaptor) self::$adaptor = new StorageAdaptor(self::gateway());
        	return self::$adaptor;
        }

		/**
		 * Returns a default instance
		 */
        static function gateway($adaptor = 'Mysql') {
			$adaptor = (($adaptor) ? $adaptor : StorageAdaptor_DefaultInstance;
			$queryAdaptor = $adaptor.'Gateway';
			$queryConnection = $adaptor."Connection";
			require_once 'store/'. strtolower($adaptor) .'/'. $adaptor .'.class.php';
			require_once 'store/'. strtolower($adaptor) .'/'. $adaptor .'.class.php';
        	if (!self::$implementation) self::$implementation = new $queryAdaptor(new $queryConnection());
        	return self::$implementation;
        }
		
		private $gateway;
		
		/**
		 * @ignore
		 */
		function __construct($gateway) {
			$this->gateway = $gateway;
		}
		
		/**
		 * Converts a passed in value to a record class name.
		 */
		function setRecordType($type) {
			$this->currentRecordType = Inflect::toClassName(Inflect::toSingular($type));
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
					$record = $this->currentRecordType;				
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
			$type = $this->currentRecordType;
			$objects = $this->gateway->getObjects();
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
			return $this->gateway->getObject();
		}
		
		/**
		 * Provides a multi row result set as a list of untyped objects.
		 * @return array<strClass>
		 */
		function getObjects() {
			return $this->gateway->getObjects();
		}
		
		/**
		 * Provides a single value from a query result.
		 */
		function getValue() {
			return $this->gateway->getValue();
		}
		
		/**
		 * Provides an iterator over a result set.
		 */
		function getIterator() {
			return $this->gateway->getIterator();
		}		

		/**
		 * @deprecated
		 */
		function selectById($table, $id) {
			$this->setRecordType($table);
			return $this->gateway->selectById($table, $id);
		}
		
		/**
		 * @deprecated
		 */
		function selectByKey($table, $key) {
			$this->setRecordType($table);
			return $this->gateway->selectByKey($table, $key);
		}
		
		/**
		 * @deprecated
		 */
		function selectAll($table) {
			$this->setRecordType($table);
			return $this->gateway->selectAll($table);
		}
		
		/**
		 * @deprecated
		 */
		function select($table, $target) {
			$this->setRecordType($table);
			return $this->gateway->select($table, $target);
		}
		
		/**
		 * @deprecated
		 */
		function selectByAssociation($table, $join_table, $target=false) {
			$this->setRecordType($table);
			return $this->gateway->selectByAssociation($table, $join_table, $target);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function insertId() {
			return $this->gateway->insertId();
		}		

		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function insert($table, $properties) {
			$this->setRecordType($table);
			return $this->gateway->insert($table, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function update($table, $target, $properties) {
			$this->setRecordType($table);
			return $this->gateway->update($table, $target, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function delete($table, $matching) {
			$this->setRecordType($table);
			return $this->gateway->delete($table, $matching);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 * @todo better object check
		 */
		function query($statement) {
			if (is_object($statement)) $this->setRecordType($statement->tableName);
			return $this->gateway->query($statement);
		}

		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function createTable($name, $properties) {
			$this->setRecordType($name);
			return $this->gateway->createTable($name, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropTable($name) {
			$this->setRecordType($name);
			return $this->gateway->dropTable($name);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function addColumn($table, $column, $type) {
			$this->setRecordType($table);
			return $this->gateway->addColumn($table, $column, $type);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropColumn($table, $column) {
			$this->setRecordType($table);
			return $this->gateway->dropColumn($table, $column);
		}
		
}

?>
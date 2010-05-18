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

require_once 'mysql/MysqlConnection.class.php';
require_once 'mysql/MysqlGateway.class.php';
require_once 'mysql/MysqlQuery.class.php';

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
        static function instance($plugin = false) {
        	if (!self::$adaptor) self::$adaptor = new StorageAdaptor(self::gateway());
        	return self::$adaptor;
        }

		/**
		 * Returns a MysqlAdaptor instance
		 */
        static function gateway($plugin = false) {
        	if (!self::$implementation) self::$implementation = new MysqlGateway(new MysqlConnection());
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
		 * Returns an object as the result of a select query.
		 * 
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
		 * Returns an array of record objects as the result of a select query.
		 *
		 * @return array<Record>
		 */
		function getRecords() {
			$i=0; $records = array();
			while ($row = $this->getObject()) {
				$record = (isset($row->type)) ? $row->type : $this->currentRecordType;
				$records[$i] = new $record($row); $i++;
			}
			return $records;
		}

		/**
		 * @todo document
		 */
		function getObject() {
			return $this->gateway->getObject();
		}
		
		/**
		 * @todo document
		 */
		function getObjects() {
			return $this->gateway->getObjects();
		}
		
		/**
		 * @todo document
		 */
		function getValue() {
			return $this->gateway->getValue();
		}
		
		/**
		 * @todo document
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
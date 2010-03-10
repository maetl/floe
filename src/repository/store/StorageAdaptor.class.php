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
		 * @todo document
		 */
		function getRecord() {
			return $this->gateway->getRecord();
		}
		
		/**
		 * @todo document
		 */
		function getRecords() {
			return $this->gateway->getRecords();
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
			return $this->gateway->getObject();
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
			return $this->gateway->selectById($table, $id);
		}
		
		/**
		 * @deprecated
		 */
		function selectByKey($table, $key) {
			return $this->gateway->selectByKey($table, $key);
		}
		
		/**
		 * @deprecated
		 */
		function selectAll($table) {
			return $this->gateway->selectAll($table);
		}
		
		/**
		 * @deprecated
		 */
		function select($table, $target) {
			return $this->gateway->select($table, $target);
		}
		
		/**
		 * @deprecated
		 */
		function selectByAssociation($table, $join_table, $target=false) {
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
			return $this->gateway->insert($table, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function update($table, $target, $properties) {
			return $this->gateway->update($table, $target, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function delete($table, $matching) {
			return $this->gateway->delete($table, $matching);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 * @todo better object check
		 */
		function query($statement) {
			if (is_object($statement)) $this->gateway->tableName = $statement->tableName;
			return $this->gateway->query($statement);
		}

		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function createTable($name, $properties) {
			return $this->gateway->createTable($name, $properties);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropTable($name) {
			return $this->gateway->dropTable($name);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function addColumn($table, $column, $type) {
			return $this->gateway->addColumn($table, $column, $type);
		}
		
		/**
		 * @todo extract to SqlAdaptor interface
		 */
		function dropColumn($table, $column) {
			return $this->gateway->dropColumn($table, $column);
		}
		
}

?>
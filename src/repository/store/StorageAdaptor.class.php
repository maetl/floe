<?php
/**
 * $Id$
 * @package repository
 * @subpackage store
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
		 */
		function query($statement) {
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
		
}

?>
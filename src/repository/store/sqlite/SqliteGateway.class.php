<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package repository
 * @subpackage store.sqlite
 */
require_once 'SqliteConnection.class.php';
require_once dirname(__FILE__) .'/../SqlGateway.class.php';
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../ResourceError.class.php';

/**
 * @package repository
 * @subpackage store.sqlite
 */
class SqliteGateway implements SqlGateway {
	private $connection;
	private $result;
	
	function __construct($connection) {
		$this->connection = $connection;
	}	
	
	function getRecord() {
		
	}
	
	function getObject() {
		return $this->result->fetchObject();
	}
	
	function getValue() {
		
	}
	
	function getRecords() {
		
	}
	
	function getObjects() {
		
	}
	
	function getIterator() {
		
	}
	
	function query($statement) {
		$this->result = $this->connection->execute($statement);
	}
	
	function insert($table, $columns) {
		$this->connection->connect();
		$this->currentTable = $table;
		$keys = array_keys($columns);
		$values = array_values($columns);
		$colnum = count($columns);
		$sql = 'INSERT INTO '.$table.' (';
		for($i=0;$i<$colnum;$i++) {
			$sql .= Inflect::propertyToColumn($keys[$i]);
			$i==($colnum-1) ? $sql .= ')' : $sql .= ',';
		}
		$sql .= ' VALUES (';
		for($i=0;$i<$colnum;$i++) {
			$sql .= '"'.(string)$values[$i].'"';
			$i==($colnum-1) ? $sql .= ')' : $sql .= ',';
		}
		$this->connection->execute($sql);		
	}
	
	function update($table, $target, $columns) {
		$this->connection->connect();
		$colnum = count($columns);
		$i = 1;
		$sql = 'UPDATE '.$table.' SET ';
		foreach($columns as $field=>$val) {
			$sql .= Inflect::propertyToColumn($field).'="'.$val.'"';
			$i==$colnum ? $sql .= ' ' : $sql .= ',';
			$i++;
		}
		$sql .= 'WHERE '.key($target).'="'.current($target).'"';
		$this->connection->execute($sql);
	}
	
	function delete($table, $target) {
		
	}

	function createTable($table, $rows) {
		$sql = "\nCREATE TABLE $table (";
		$sql .= "\nid INTEGER PRIMARY KEY";
		foreach($rows as $key=>$val) {
			$sql .= ',';
			$key = Inflect::propertyToColumn($key);
			$sql .= "\n $key ";
			$sql .= $this->defineType($val);
		}
		$sql .= ")";
		$this->connection->execute($sql);
	}

	 /**
	  * Gets native SQL definition for a column type.
	  *
	  * @return string SQL definition
	  */
	 private function defineType($type) {
		 switch($type) {
				case 'int':
				case 'integer':
				case 'number':
					return "INT(11)";
				case 'bool':
				case 'boolean':
					return "TINYINT(1)";
				case 'decimal':
					return "DOUBLE(16,2)";
				case 'float':
					return "DOUBLE(16,8)";
				case 'text':
					return"TEXT";
				case 'date': 
					return "DATE";
				case 'datetime':
					return "DATETIME";
				case 'raw':
					return "BLOB";
				default:
					return "CHAR(255)";
			}
	 }
	
}

?>
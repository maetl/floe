<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id: SqliteGateway.class.php 413 2010-05-23 18:59:36Z coretxt $
 * @package repository
 * @subpackage services.sqlite
 */
require_once 'SqliteConnection.class.php';
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';

/**
 * @package repository
 * @subpackage services.sqlite
 */
class SqliteAdaptor {
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
		$objects = array();
		while ($object = $this->result->fetchObject()) {
			$objects[] = $object;
		}
		return $objects;
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
		if (!is_array($target)) return;
		$this->connection->connect();
		$sql = 'DELETE FROM '.$table.' WHERE ';
		$where = '';
		foreach ($target as $key => $value) {
			if($where != "") {
				$where .= "AND ";
			}
			$where .= Inflect::propertyToColumn($key) .'="'. $value .'" ';
		}
		$this->connection->execute($sql . $where);		
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
	 * Destroys an existing table and all its data
	 */
	 function dropTable($table) {
		 $this->connection->execute("DROP TABLE `$table`");
	 }

	/**
	 * Renames a table
	 */
	function renameTable($tableFrom, $tableTo) {
		$this->connection->execute("ALTER TABLE $tableFrom RENAME TO $tableTo");
	}

 	/**
	 * Checks if a table exists
	 */
	 function tableExists($table) {
		 throw new Exception("Unsupported by SQLite");
	 }

	/**
	 * Rename a table column without altering it's structure
	 */
	function changeColumn($table, $oldCol, $newCol, $type=false) {
		throw new Exception("Unsupported by SQLite");
	}

	/**
	 * Add a new table column
	 */
	 function addColumn($table, $name, $type) {
	 	$name = Inflect::propertyToColumn($name);
		$sql = "ALTER TABLE $table ADD $name " . $this->defineType($type);
		$this->connection->execute($sql);
	 }

	/**
	 * Add a new table column
	 */
	 function dropColumn($table, $name) {
	 	$name = Inflect::propertyToColumn($name);
		$sql = "ALTER TABLE $table DROP COLUMN $name";
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
				case 'datetime':
					return "CHAR(30)";
				case 'raw':
					return "BLOB";
				default:
					return "VARCHAR(255)";
			}
	 }
}

?>
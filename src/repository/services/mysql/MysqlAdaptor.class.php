<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage services.mysql
 */

require_once 'MysqlConnection.class.php';
require_once 'MysqlQuery.class.php';
require_once 'MysqlIterator.class.php';

/**
 * Gateway for managing common database operations.
 *
 * @package repository
 * @subpackage services.mysql
 */
class MysqlAdaptor {

	var $connection;
	var $result;
	var $currentTable;

	function __construct($connection) {
		$this->connection = $connection;
	}
	
	function affectedRows() {
		return mysql_affected_rows($this->result);
	}
	
	/**
	 * Returns the last insert id.
	 */
	function insertId() {
		return mysql_insert_id();
	}
	
	/**
	 * Returns a Record as the result of a select query.
	 * 
	 * @return stdClass
	 */
	function getObject() {
		return mysql_fetch_object($this->result);
	}
	
	/**
	 * Returns a single value from the select query result.
	 *
	 * @return mixed value
	 */
	function getValue() {
		$result = mysql_fetch_array($this->result);
		return $result[0];
	}

	/**
	 * Returns an array of objects as the result of a select query.
	 *
	 * @return array<stdClass>
	 */
	function getObjects() {
		$i=0; $objects = array();
		while ($row = mysql_fetch_object($this->result)) {
			$objects[$i] = $row; $i++;
		}
		return $objects;
	}
	
	/**
	 * Returns an iterator for traversing the result of a select query.
	 *
	 * @return Iterator<stdClass>
	 */
	function getIterator() {
		return new MysqlIterator($this->result);
	}
	
	/**
	 *
	 */
	function selectById($table, $id, $target=false) {
		$this->currentTable = $table;
		$fields = (!$target) ? '*' : implode(',', array_keys($target));
		$sql = 'SELECT '.$fields.' FROM `'.$table.'` WHERE id="'.$id.'"';
		$this->result = $this->connection->execute($sql);
	}
	
	/**
	 *
	 */
	function selectAll($table) {
		$this->currentTable = $table;
		$sql = 'SELECT * FROM `'.$table.'`';
		$this->result = $this->connection->execute($sql);
	}

	/**
	 *
	 */	
	function selectByKey($table, $target) {
		$this->connection->connect();
		$this->currentTable = $table;
		$sql = 'SELECT * FROM `'.$table.'` WHERE ';
		$where = '';
		foreach ($target as $key => $value) {
			if($where != "") {
				$where .= "AND ";
			}
			$where .= mysql_real_escape_string($key) .'="'. mysql_real_escape_string($value) .'" ';
		}
		$sql .= $where;
		$this->result = $this->connection->execute($sql);
	}
	
	/**
	 *
	 */
	 function selectByAssociation($table, $join_table, $target=false) {
	 	 $this->currentTable = $table;
		 $sql = "SELECT * FROM `$table`,`$join_table` ";
		 $sql .= "WHERE $table.id=$join_table.".Inflect::toSingular($table)."_id";
		 if ($target) $sql .= " AND $join_table.".key($target)."='".current($target)."'";
		 $this->result = $this->connection->execute($sql);
	 }
	
	/**
	 * Inserts a row into specified table.
	 * 
	 * @param $table string name of the table to insert into
	 * @param $columns associative array of column=>value pairs to create
	 */
	function insert($table, $columns) {
		$this->connection->connect();
		$this->currentTable = $table;
		$keys = array_keys($columns);
		$values = array_values($columns);
		$colnum = count($columns);
		$sql = 'INSERT INTO `'.mysql_real_escape_string($table).'` (';
		for($i=0;$i<$colnum;$i++) {
			$sql .= Inflect::propertyToColumn($keys[$i]);
			$i==($colnum-1) ? $sql .= ')' : $sql .= ',';
		}
		$sql .= ' VALUES (';
		for($i=0;$i<$colnum;$i++) {
			$sql .= '"'.mysql_real_escape_string((string)$values[$i]).'"';
			$i==($colnum-1) ? $sql .= ')' : $sql .= ',';
		}
		$this->connection->execute($sql);
	}
	
	/**
	 * Creates a new row in specified table. Synonym for insert.
	 */
	function create($table, $columns) {
		$this->insert($table, $columns);
	}
	
	/**
	 * Updates a row in specified table
	 */
	function update($table, $target, $columns) {
		$this->connection->connect();
		$colnum = count($columns);
		$i = 1;
		$sql = 'UPDATE `'.mysql_real_escape_string($table).'` SET ';
		foreach($columns as $field=>$val) {
			$sql .= $field.'="'.mysql_real_escape_string($val).'"';
			$i==$colnum ? $sql .= ' ' : $sql .= ',';
			$i++;
		}
		$sql .= 'WHERE '.mysql_real_escape_string(key($target)).'="'.mysql_real_escape_string(current($target)).'"';
		$this->connection->execute($sql);
	}
	
	/**
	 * Deletes row(s) specified by column=>value pair
	 *
	 */
	function delete($table, $target) {
		if (!is_array($target)) return;
		$sql = 'DELETE FROM `'.$table.'` WHERE ';
		$where = '';
		foreach ($target as $key => $value) {
			if($where != "") {
				$where .= "AND ";
			}
			$where .= mysql_real_escape_string($key) .'="'. mysql_real_escape_string($value) .'" ';
		}
		$this->connection->execute($sql . $where);
	}
	
	/**
	 * Executes an arbitrary SQL query on the connection.
	 */
	function query($sql) {
		$this->result = $this->connection->execute($sql);
	}
	
	/**
	 * Executes an arbitrary SELECT query on the connection.
	 */
	function select($table, $clauses) {
		$this->currentTable = $table;
		$sql = "SELECT * FROM `$table` $clauses";
		$this->result = $this->connection->execute($sql);
	}	

	/** 
	 * Creates a table with specified columns
	 */
	function createTable($table, $rows, $type='MyISAM') {
		$sql = "\nCREATE TABLE `$table` (";
		$sql .= "\nid int(11) NOT NULL auto_increment,";
		foreach($rows as $key=>$val) {
			$key = Inflect::propertyToColumn($key);
			$sql .= "\n `$key` ";
			$sql .= $this->defineType($val);
			$sql .= ',';
		}
		$sql .= "\nPRIMARY KEY  (`id`)\n";
		$sql .= ") TYPE=$type AUTO_INCREMENT=1 ;";
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
		$this->connection->execute("RENAME TABLE `$tableFrom` TO `$tableTo`");
	}

 	/**
	 * Checks if a table exists
	 */
	 function tableExists($table) {
		 $this->connection->execute("DROP TABLE IF EXISTS `$table``");
	 }
	 
	/**
	 * Rename a table column without altering it's structure
	 */
	function changeColumn($table, $oldCol, $newCol, $type=false) {
		if (!$type) {
			$sql = "SHOW FIELDS FROM `$table` LIKE '$oldCol'";
			$this->result = $this->connection->execute($sql);
			$field = $this->getObject();
			$definition = $field->Type;
		} else {
			$definition = $this->defineType($type);
		}
		$sql = "ALTER TABLE `$table` CHANGE COLUMN $oldCol $newCol ". $definition;
		$this->connection->execute($sql);
	}
	
	/**
	 * Add a new table column
	 */
	 function addColumn($table, $name, $type) {
	 	 $name = Inflect::propertyToColumn($name);
		 $sql = "ALTER TABLE `$table` ADD COLUMN $name " . $this->defineType($type);
		 $this->connection->execute($sql);
	 }
	
	/**
	 * Add a new table column
	 */
	 function dropColumn($table, $name) {
	 	 $name = Inflect::propertyToColumn($name);
		 $sql = "ALTER TABLE `$table` DROP COLUMN $name";
		 $this->connection->execute($sql);
	 }	

	/**
	 * Add an index to a table
	 */
	function addIndex($table, $name, $columns) {
		$name = strtoupper($name);
		$sql = "ALTER TABLE `$table` ADD INDEX `$name` (". array_reduce($columns, array($this,'reduceIndexColumns')) .")";
		$this->connection->execute($sql);
	}
	
	/**
	 * Reduce colunn names into SQL specific format for an alter table statement.
	 */
	private function reduceIndexColumns($accumulate, $column) {
		$column = Inflect::propertyToColumn($column);
		if ($accumulate == NULL) {
			$accumulate = $column;
		} else {
			$accumulate .= ",$column";
		}
		return $accumulate;
	}

	/**
	 * Remove a named index on a table
	 */
	function dropIndex($table, $name) {
		$name = strtoupper($name);
		$sql = "DROP INDEX `$name` ON `$table`";
		$this->connection->execute($sql);
	}
	
	/**
	 * Checks if given table is defined in database
	 * 
	 * @return boolean
	 */
	function hasTable($table) {
		$sql = "SHOW TABLES LIKE '$table'";
		return (boolean)mysql_num_rows($this->connection->execute($sql));
	}	 
	 
	 /**
	  * Returns the mapping to a framework type class
	  */
	 function definePropertyType($type) {
	 	return 'string';
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
					return "DOUBLE(16,2) ZEROFILL";
				case 'float':
					return "DOUBLE(16,8) ZEROFILL";
				case 'text':
					return"TEXT NOT NULL default ''";
				case 'date': 
					return "DATE NOT NULL default '0000-00-00'";
				case 'datetime':
					return "DATETIME NOT NULL default '0000-00-00 00:00:00'";
				case 'raw':
					return "BLOB NOT NULL default ''";
				default:
					return "VARCHAR(255) NOT NULL default ''";
			}
	 }
	
}

?>
<?php
/**
 * @package repository
 * @subpackage store
 */
require_once 'Schema.class.php';

/**
 * The TableSchema wraps mutable operations on a relational database
 * table.
 * 
 * @package repository
 * @subpackage store
 */
interface TableSchema extends Schema {
	
	public function createTable($name, $fields);
	
	public function dropTable($name);
	
	public function changeColumn($table, $oldCol, $newCol, $type=false);
	
	public function addColumn($table, $name, $type);
	
	public function defineType($type);

}

?>
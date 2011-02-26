<?php
/**
 * This file is part of Floe, a minimalist web framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage store
 */

/**
 * @package repository
 * @subpackage store
 */
interface RelationalStore {

	function getRecord();
	
	function getObject();
	
	function getValue();
	
	function getRecords();
	
	function getObjects();
	
	function getIterator();

	/**
	 * Inserts a single row into specified table.
	 * 
	 * @param $table string name of the table to insert into
	 * @param $columns associative array of column=>value pairs to create
	 */	
	function insert($table, $columns);
	
	/**
	 * Updates 1-n rows in specified table.
	 * 
	 * @param $table string name of the table to insert into
	 * @param $target array of WHERE predicates to match against
	 * @param $columns associative array of column=>value pairs to create
	 */
	function update($table, $target, $columns);

	/**
	 * Deletes 1-n rows from specified table.
	 * 
	 * @param $table string name of the table to insert into
	 * @param $target array of WHERE predicates to match against
	 */	
	function delete($table, $target);
	
	/** 
	 * Creates a table with specified columns.
	 *
	 * @param $table string name of the table to insert into
	 * @param $columns associative array of name=>type pairs to create as columns
	 */
	function createTable($name, $rows);
	
	/**
	 * Destroys an existing table and all its data.
	 *
	 * @param $table name of the table
	 */
	 function dropTable($table);
	
	/**
	 * Renames a table
	 */
	function renameTable($tableFrom, $tableTo);

 	/**
	 * Checks if a table exists
	 */
	 function tableExists($table);
	 
	/**
	 * Rename a table column without altering it's structure
	 */
	function changeColumn($table, $oldCol, $newCol, $type=false);
	
	/**
	 * Add a new table column
	 */
	 function addColumn($table, $name, $type);
	
	/**
	 * Add a new table column
	 */
	 function dropColumn($table, $name);

}

?>
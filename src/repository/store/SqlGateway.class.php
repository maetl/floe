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
 * @subpackage store
 */

/**
 * @package repository
 * @subpackage store
 */
interface SqlGateway {

	function getRecord();
	
	function getObject();
	
	function getValue();
	
	function getRecords();
	
	function getObjects();
	
	function getIterator();
	
	function insert($table, $columns);
	
	function update($table, $target, $columns);
	
	function delete($table, $target);
	
	function createTable($name, $rows);

}

?>
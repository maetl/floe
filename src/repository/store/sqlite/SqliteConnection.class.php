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

/**
 * Active connection to Sqlite data source.
 *
 * @package repository
 * @subpackage store.sqlite
 */
class SqliteConnection {
	private $connection;
	private $db_name;
	
	function __construct($environment = false) {
		if ($environment) {
			$this->db_name = $environment->DB_NAME;
		} else {
			$this->db_name = DB_NAME;
		}
	}
	
	/**
     * Idempotent method creates the low level connection to the database.
	 */
	function connect() {
		if (!is_object($this->connection)) {
			$this->connection = sqlite_factory($this->db_name);
		}
	}

	/**
	 * Issue an SQL query against the database
	 */	
	function execute($sql) {
		$this->connect();
		return $this->connection->query($sql);
	}
	
	function fetchObject() {
		return $this->connection->fetchObject();
	}
	
}

?>
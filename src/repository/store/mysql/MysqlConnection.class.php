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
 * @subpackage store.mysql
 */
 
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../ResourceError.class.php';

/**
 * An active connection to a MySql database server.
 *
 * @package repository
 * @subpackage store.mysql
 */
class MysqlConnection {

	private $_connection;
	private $_db_host;
	private $_db_name;
	private $_db_user;
	private $_db_pass;

	/**
	 *  Constructs a Mysql connection that can be obtained upon invocation of the connect method.
	 *
	 * @param $host Address of database server
	 * @param $name Name of database to connect to
	 * @param $user Authorized user name on database
	 * @param $pass Authorized password for user
	 */
	 function __construct($environment = false) {
		 if ($environment) {
			$this->_db_host = $environment->DB_HOST;
			$this->_db_name = $environment->DB_NAME;
			$this->_db_user = $environment->DB_USER;
			$this->_db_pass = $environment->DB_PASS;
		 } else {
			$this->_db_host = DB_HOST;
			$this->_db_name = DB_NAME;
			$this->_db_user = DB_USER;
			$this->_db_pass = DB_PASS;
		 }
	 }
	
	/**
	 * Idempotent method creates the low level connection to the database.
	 *
	 * @public
	 * @return boolean true on success
	 */
	function connect() {
		if (!is_resource($this->_connection)) {
			$this->_connection = @mysql_connect($this->_db_host, $this->_db_user, $this->_db_pass);
			if (!is_resource($this->_connection)) {
				$this->raiseError();
				return false;
			}
			EventLog::info("Connected to [{$this->_db_host}]");
			if (!@mysql_select_db($this->_db_name, $this->_connection)) {
				$this->raiseError();
				return false;
			}
			EventLog::info("Selected [{$this->_db_name}]");
			if (function_exists('mysql_set_charset')) {
				mysql_set_charset('utf8', $this->_connection);
			}
		}
		return true;
	}

	/**
	 * @private
	 */
	function raiseError() {
		$message = mysql_error();
		throw new ResourceError($message);
	}
	
	/**
	 * Issue an SQL query against the database
	 */
	function execute($sql) {
		$this->connect();
		$query = mysql_query($sql, $this->_connection);
		EventLog::info("Executed [$sql]");
		return (mysql_error() != '') ? $this->raiseError() : $query;
	}
	
}

?>
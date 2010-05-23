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
 * @subpackage services.mysql
 */
 
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../ResourceError.class.php';

/**
 * If a UTF8 connection is needed, this constant should be set to true.
 * Should only be used if the name is not correctly configured for UTF8 connections.
 */
if (!defined('MysqlConnection_ForceUTF8')) define('MysqlConnection_ForceUTF8', false);

/**
 * An active connection to a MySql name server.
 *
 * @package repository
 * @subpackage services.mysql
 */
class MysqlConnection {

	private $connection;
	private $host;
	private $name;
	private $user;
	private $pass;

	/**
	 *  Constructs a Mysql connection that can be obtained upon invocation of the connect method.
	 *
	 * @param $host Address of name server
	 * @param $name Name of name to connect to
	 * @param $user Authorized user name on name
	 * @param $pass Authorized pass for user
	 */
	 function __construct($environment = false) {
		 if ($environment) {
			$this->host = $environment->DB_HOST;
			$this->name = $environment->DB_NAME;
			$this->user = $environment->DB_USER;
			$this->pass = $environment->DB_PASS;
		 } else {
			$this->host = DB_HOST;
			$this->name = DB_NAME;
			$this->user = DB_USER;
			$this->pass = DB_PASS;
		 }
	 }
	
	/**
	 * Idempotent method creates the low level connection to the name.
	 *
	 * @public
	 * @return boolean true on success
	 */
	function connect() {
		if (!is_resource($this->connection)) {
			$this->connection = @mysql_connect($this->host, $this->user, $this->pass);
			if (!is_resource($this->connection)) {
				$this->raiseError();
				return false;
			}
			EventLog::info("Connected to [{$this->host}]");
			if (!@mysql_select_db($this->name, $this->connection)) {
				$this->raiseError();
				return false;
			}
			EventLog::info("Selected [{$this->name}]");          
			if (MysqlConnection_ForceUTF8) mysql_set_charset('utf8', $this->connection);
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
	 * Issue an SQL query against the name
	 */
	function execute($sql) {
		$this->connect();
		$query = mysql_query($sql, $this->connection);
		EventLog::info("Executed [$sql]");
		return (mysql_error() != '') ? $this->raiseError() : $query;
	}
	
}

?>
<?php
// $Id: MysqlConnection.class.php 53 2007-05-06 09:54:15Z maetl_ $
/**
 * @package repository
 * @subpackage store
 */
require_once 'EventLogger.class.php';
require_once 'MysqlResourceError.class.php';

/**
 * An active connection to a MySql database server.
 *
 * @package repository
 * @subpackage store
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
	 function MysqlConnection($host=false, $name=false, $user=false, $pass=false) {
		 if ($host && $name && $user && $pass) {
			$this->_db_host = $host;
			$this->_db_name = $name;
			$this->_db_user = $user;
			$this->_db_pass = $pass;
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
			EventLogger::info("Connected to [{$this->_db_host}]");
			if (!@mysql_select_db($this->_db_name, $this->_connection)) {
				$this->raiseError();
				return false;
			}
			EventLogger::info("Selected [{$this->_db_name}]");
		}
		return true;
	}

	/**
	 * @private
	 */
	function raiseError() {
		$message = mysql_error();
		throw new MysqlResourceError($message);
	}
	
	/**
	 * Issue an SQL query against the database
	 */
	function execute($sql) {
		$this->connect();
		$query = mysql_query($sql, $this->_connection);
		EventLogger::info("Executed [$sql]");
		return (mysql_error() != '') ? $this->raiseError() : $query;
	}
	
}

?>
<?php
// $Id: MysqlConnection.class.php 53 2007-05-06 09:54:15Z maetl_ $
/**
 * @package repository
 * @subpackage store
 */
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';
require_once dirname(__FILE__).'/../ResourceError.class.php';

class MemcachedConnection {
	
	private $_connection;
	private $_host;
	private $_port;
	private $_user;
	private $_pass;
	
	/**
	 * Constructs a connection to the specified Memcached resource.
	 *
	 * Builds the connection from passed in environment configuration, or else
	 * defaults to specified MEMCACHED_ constants.
	 *
	 * @param $environment configuration settings for application.
	 */
	 function __construct($environment = false) {
		 if ($environment) {
			$this->_host = $environment->MEMCACHED_HOST;
			$this->_port = $environment->MEMCACHED_PORT;
			//$this->_user = $environment->MEMCACHED_USER;
			//$this->_pass = $environment->MEMCACHED_PASS;
		 } else {
			$this->_host = MEMCACHED_HOST;
			$this->_port = MEMCACHED_PORT;
			//$this->_user = MEMCACHED_USER;
			//$this->_pass = MEMCACHED_PASS;
		 }
	 }
	
	/**
	 * Idempotent method initializes connection to the Memcached server.
	 *
	 * @public
	 * @return boolean true on success (why?)
	 */
	function connect() {
		if ($this->_connection) return true;
		$memcache = new Memcache();
		if ($memcache->connect($this->_host, $this->_port)) {
			$this->_connection = $memcache;
			return true;
		} else {
			throw new ResourceError("Memcached:".$this->_host);
		}
	}
	
	/**
	 * Send a query to the Memcached server.
	 *
	 * @param $method name of the method to execute - add, set, get, or delete.
	 */
	function execute($method, $key='', $value=false) {
		$this->connect();
		return $this->_connection->$method($key, $value);
	}
	
	/**
	 * Close the connection.
	 */
	function close() {
		if ($this->_connection) $this->_connection->close();
	}
	
}

?>
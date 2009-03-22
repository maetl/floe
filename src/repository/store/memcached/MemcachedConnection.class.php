<?php
/**
 * $Id$
 * @package repository
 * @subpackage store.memcached
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
require_once dirname(__FILE__) .'/../../../framework/EventLog.class.php';
require_once dirname(__FILE__).'/../ResourceError.class.php';

/**
 * @package repository
 * @subpackage store.memcached
 */
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
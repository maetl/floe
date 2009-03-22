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
require_once dirname(__FILE__).'/MemcachedConnection.class.php';

/**
 * Gateway to Memcached server.
 *
 * @package repository
 * @subpackage store.memcached
 */
class MemcachedAdaptor {
	private $connection;
	
	function __construct($connection) {
		$this->connection = $connection;
	}
	
	/**
	 * Compresses a namespace and key into a single string for referencing as a cache identifier.
	 */
	private function generateCacheKey($namespace, $key) {
		return md5($namespace."--|--".$key);
	}

	/**
	 * Read an object from the data store.
	 */
	function read($namespace, $key) {
		return $this->connection->execute("get", $this->generateCacheKey($namespace, $key));
	}
	
	/**
	 * Add a record to the cache based on given key.
	 */
	function add($namespace, $key, $data) {
		return $this->connection->execute("add", $this->generateCacheKey($namespace, $key), $data);
	}
	
	/**
	 * Write an object to the data store.
	 */
	function write($namespace, $key, $data, $expire=30) {
		return $this->connection->execute("set", $this->generateCacheKey($namespace, $key), $data, $expire);
	}
	
	/**
	 * Clear a record from the cache.
	 */
	function delete($namespace, $key) {
		return $this->connection->execute("delete", $this->generateCacheKey($namespace, $key));
	}
	
	/**
	 * Return current profile stats from the memcached instance.
	 */
	function getStats($type) {
		return $this->connection->execute("getStats", $type);
	}

	/**
	 * Close down the connection on shutdown.
	 */
	function __destruct() {
		$this->connection->close();
	}
	
}

?>
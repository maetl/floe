<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage store.memcached
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

<?php
// $Id: MysqlConnection.class.php 53 2007-05-06 09:54:15Z maetl_ $
/**
 * @package repository
 * @subpackage store
 */
require_once dirname(__FILE__).'/MemcachedConnection.class.php';

/**
 * Gateway to Memcached server.
 *
 * @package repository
 * @subpackage store
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
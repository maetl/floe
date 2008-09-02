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
	 * Fetch a record from the data store.
	 */
	function fetch($namespace, $key) {
		return $this->connection->execute("get", $this->generateCacheKey($namespace, $key));
	}
	
	/**
	 * Add a record to the cache based on given key.
	 */
	function add($namespace, $key, $data) {
		return $this->connection->execute("add", $this->generateCacheKey($namespace, $key), $data);
	}
	
	/**
	 * Update a record in the cache based on its key.
	 */
	function store($namespace, $key, $data) {
		return $this->connection->execute("set", $this->generateCacheKey($namespace, $key), $data);
	}
	
	/**
	 * Clear a record from the cache.
	 */
	function delete($namespace, $key) {
		return $this->connection->execute("delete", $this->generateCacheKey($namespace, $key));
	}
	
}

?>
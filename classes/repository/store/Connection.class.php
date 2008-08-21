<?php
/**
 * @package repository
 * @subpackage store
 */

/**
 * Abstract connection to a data source.
 * 
 * The connection should be implemented as lazy loading and idempotent:
 * 
 * 1) Lazy Loading: the low level connection should not be established until a call to
 * execute requests a direct lookup on the data source.
 * 
 * 2) Idempotent: Calling the connect method multiple times should not overwrite the
 * existing connection.
 * 
 * @package repository
 * @subpackage store
 */
interface Connection {

	/**
	 * Establish a connection to data source.
	 * 
	 * This usually requires a username, password,
	 * host, and the name of the resource to connect.
	 */
	public function connect();
	
	/**
	 * Execute a bound query over the established connection.
	 */
	public function execute($query);

}

?>
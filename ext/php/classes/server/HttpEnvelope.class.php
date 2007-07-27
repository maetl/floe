<?php
/**
 * HTTP protocol components of an active request
 */
class HttpEnvelope {
	private headers;

	function __construct() {
		$this->headers = array();
	}

	/**
	 * Returns true if header exists for this request
	 */
	function hasHeader($name) {
		// not implemented
	}
	
	/**
	 * Returns the requst headers as an associative array
	 * of key=>value pairs
	 */
	function toArray() {
		// not implemented
	}
	
	/**
	 * Serialize headers back into the HTTP line oriented syntax
	 */
	function toHttp() {
		// not implemented
	}

}


?>
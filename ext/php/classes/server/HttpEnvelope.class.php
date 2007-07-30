<?php
/**
 * HTTP protocol components of an active request
 */
class HttpEnvelope {
	private $headers;

	function __construct() {
		if (function_exists('apache_request_headers')) {
			$this->headers = apache_request_headers();
		} else {
			echo "error";
		}
	}
	
	/**
	 * Returns true if header exists for this request
	 */
	function hasHeader($name) {
		return (array_key_exists($name, $this->headers));
	}
	
	/**
	 * Returns the HTTP header value for given key
	 */
	function header($key) {
		if ($this->hasHeader($key)) {
			return $this->headers[$key];
		}
	}
	
	/**
	 * Returns the requst headers as an associative array
	 * of key=>value pairs
	 */
	function toArray() {
		return $this->headers;
	}
	
	/**
	 * Serialize headers back into the HTTP line oriented syntax
	 */
	function toHttp() {
		// not implemented
	}

}


?>
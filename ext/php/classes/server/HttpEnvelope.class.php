<?php
/**
 * @package server
 */

/**
 * HTTP protocol components of an active request
 * 
 * @package server
 */
class HttpEnvelope {
	private $headers;

	function __construct() {
		if (function_exists('apache_request_headers')) {
			$this->headers = apache_request_headers();
		} else {
			if (php_sapi_name() != 'cli') {
				throw new Exception("Apache Unsupported");
			} else {
				$this->headers = array();
			}
		}
	}
	
	/**
	 * Returns true if header exists for this request
	 * 
	 * @return boolean
	 */
	function hasHeader($name) {
		return (array_key_exists($name, $this->headers));
	}
	
	/**
	 * Returns the HTTP header value for given key
	 * 
	 * @return string
	 */
	function header($key) {
		if ($this->hasHeader($key)) {
			return $this->headers[$key];
		}
	}
	
	/**
	 * Returns the requst headers as an associative array
	 * of key=>value pairs
	 * 
	 * @return array
	 */
	function toArray() {
		return $this->headers;
	}
	
	/**
	 * Serialize headers back into the HTTP line oriented syntax.
	 * 
	 * @return string
	 */
	function toHttp() {
		$buffer = '';
		foreach($this->headers as $name => $value) {
			$buffer .= $name . ": " . $value . "\r\n";
		}
		return $buffer;
	}

}


?>
<?php
// $Id$
/**
 * @package server
 */
require_once "UriPath.class.php";
require_once "UserAgent.class.php";
require_once "HttpEnvelope.class.php";
 
/**
 * Reads data from an incoming HTTP request.
 *
 * Provides immutable accessors for GET and POST properties 
 * and access to the UriPath and UserAgent for the request instance.
 *
 * @package server
 */ 
class Request {
	var $uri;
	private $method;
	private $envelope;

	function __construct($headers=false) {
		$this->envelope = ($headers) ? $headers : new HttpEnvelope();
		$this->uri = new UriPath($_SERVER['REQUEST_URI']);
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * represents this request in string format
	 */
	function __toString() {
		return $this->method . " ". $this->uri->getUrl();
	}
	
	/**
	 * access GET or POST parameters as instance properties
	 */
	public function __get($param) {
		return ($this->isPost()) ? $this->postParameter($param) : $this->getParameter($param);
	}
	
	/**
	 * Returns the method verb for this request
	 * [ GET, POST, PUT, DELETE, HEAD ]
	 */
	function method() {
		return $this->method;
	}
	
	/**
	 * Returns true if request is a non-destructive method (GET or HEAD)
	 */
	function isIdempotent() {
		return in_array(array('get','head'), $this->method);
	}
	
	/**
	 * Returns true if request method is GET
	 */
	function isGet() {
		return ($this->method == 'GET');
	}

	/**
	 * Returns true if request method is POST
	 */
	function isPost() {
		return ($this->method == 'POST');
	}

	/**
	 * Returns true if request method is PUT
	 */	
	function isPut() {
		return ($this->method == 'PUT');
	}

	/**
	 * Returns true if request method is DELETE
	 */
	function isDelete() {
		return ($this->method == 'DELETE');
	}
	
	/**
	 * Returns true if request method is HEAD
	 */
	function isHead() {
		return ($this->method == 'HEAD');
	}	
	
	/** @private */
	function cleanValue($value) {
		return stripslashes(htmlspecialchars($value));
	}
	
	/**
	 * Accessor for MIME attachment
	 */
	function getAttachment($attachment) {
		if (isset($_FILE[$attachment])) {
			return $_FILE[$attachment];
		}
	}
	
	/**
	 * Accessor for HTTP GET parameters
	 */
	function getParameter($parameter) {
		if (isset($_GET[$parameter])) {
			return $this->cleanValue($_GET[$parameter]);
		}
	}
	
	/**
	 * Accessor for HTTP POST parameters
	 */
	function postParameter($parameter) {
		if (isset($_POST[$parameter])) {
			return $this->cleanValue($_POST[$parameter]);
		}
	}
	
	/**
	 * Accessor for the raw body of the HTTP request
	 */
	function entityBody() {
		$data = "";
		if ($this->envelope->header('Content-Type') == 'application/x-www-form-urlencoded') {
			$length = count($_POST); $current = 0;
			foreach($_POST as $key => $value) {
				$current++;
				$data .= $key . "=" . rawurlencode($value);
				$data .= ($current != $length) ? "&" : "";
			}
		} else {
			$in = fopen("php://input", "r");
			while ($chunk = fread($in, 1024)) $data .= $chunk;
		}
		return $data;
	}
	
	function browser() {
		return new UserAgent($this->envelope->header("User-Agent"));
	}
	
	function language() {
		return $this->envelope->header("Accept-Language");
	}

	function referer() {
		return $this->envelope->header("Referer");
	}
	
	function charset() {
		return $this->envelope->header("Accept-Charset");
	}
	
	
}

?>
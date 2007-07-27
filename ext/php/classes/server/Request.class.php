<?php
// $Id$
/**
 * @package server
 */
require_once "UriPath.class.php";
 
/**
 * Reads data from an incoming HTTP request.
 *
 * Provides immutable accessors for GET and POST properties 
 * and access to the UriPath from the request
 *
 * @package server
 */ 
class Request {
	var $_path;
	var $_method;
	var $_get_parameters;
	var $_post_parameters;
	var $_cookie_parameters;
	var $_uploaded_files;
	var $_http_headers;

	function Request($path=false, $method=false) {
		if (!$path) $path = $_SERVER['REQUEST_URI'];
		$this->_path = new UriPath($path);
		if (!$method) {
			$this->_method = $_SERVER['REQUEST_METHOD'];
		} else {
			$this->_method = $method;
		}
		$this->_get_parameters = array();
		$this->_post_parameters = array();
		$this->_cookie_parameters = array();
		$this->_uploaded_files = array();
		$this->_http_headers = array();
		switch($this->_method) {
			case 'GET': $this->addGetParameters(); break;
			case 'POST': $this->addPostParameters(); break;
		}
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
	
	/** @private */
	function addGetParameters() {
		foreach($_GET as $key=>$value) {
			$this->_get_parameters[$key] = $this->cleanValue($value);
		}
	}

	/** @private */
	function addPostParameters() {
		foreach($_POST as $key=>$value) {
			$this->_post_parameters[$key] = $this->cleanValue($value);
		}
		foreach($_FILES as $name=>$file) {
			$this->_submitted[$name] = $file;
		}
	}
	
	/** @private */
	function addCookieParameter($key, $value) {
		$this->_cookie_parameters[$key] = $value;
	}
	
	function getCookies() {
	}

	function getPosted() {
		return $this->_post_parameters;
	}

	function getUploadedFiles() {
	
	}
	
	function getUri() {
		return $this->_path;
	}
	
	function getParameter($key) {
		if (isset($this->_post_parameters[$key])) {
			return $this->_post_parameters[$key];
		} elseif (isset($this->_get_parameters[$key])) {
			return $this->_get_parameters[$key];
		} else {
			return null;
		}
	}
	
}

?>
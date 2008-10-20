<?php
/**
 * $Id$
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
final class Request {
	/**
	 * @var UriPath
	 */
	public $uri;
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
	 * Shortcut to access GET or POST parameters as instance properties.
	 * 
	 * Eg: for <b>http://example.org/?keyName=value</b>
	 * <code>
	 * $request->keyName // is the same as
	 * $request->getParameter('keyName');
	 * </code>
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
	 * Returns true if request is a non-destructive method that can
	 * be called repeatedly without affecting the server state. 
	 * 
	 * Usually a GET or HEAD method.
	 */
	function isIdempotent() {
		return in_array(array('get','head','options'), $this->method);
	}
	
	/**
	 * Returns true if request method is GET
	 * 
	 * @return boolean
	 */
	function isGet() {
		return ($this->method == 'GET');
	}

	/**
	 * Returns true if request method is POST
	 * 
	 * @return boolean
	 */
	function isPost() {
		return ($this->method == 'POST');
	}

	/**
	 * Returns true if request method is PUT
	 * 
	 * @return boolean
	 */	
	function isPut() {
		return ($this->method == 'PUT');
	}

	/**
	 * Returns true if request method is DELETE
	 * 
	 * @return boolean
	 */
	function isDelete() {
		return ($this->method == 'DELETE');
	}
	
	/**
	 * Returns true if request method is HEAD
	 * 
	 * @return boolean
	 */
	function isHead() {
		return ($this->method == 'HEAD');
	}	
	
	/**
	 * Cleans an input value.
	 * 
	 * Uses htmlspecialchars by default.
	 * 
	 * @todo pluggable input filtering
	 * @return string|array
	 */
	private function cleanValue($value) {
		if (!$value) return $value;
		if (is_array($value)) {
			return array_map(array($this, 'cleanValue'), $value);
		} else {
			return stripslashes(htmlspecialchars($value));
		}
	}
	
	/**
	 * Accessor for MIME attachment
	 * 
	 * <code>
	 * <input type="file" name="docUpload" />
	 * </code>
	 * 
	 * <code>
	 * $file = $request->attachment('docUpload');
	 * </code>
	 */
	function attachment($attachment) {
		if (isset($_FILE[$attachment])) {
			return $_FILE[$attachment];
		}
	}
	
	/**
	 * Accessor for HTTP GET parameters
	 * 
	 * @return mixed
	 */
	function getParameter($parameter) {
		if (isset($_GET[$parameter])) {
			return $this->cleanValue($_GET[$parameter]);
		}
	}
	
	/**
	 * Accessor for HTTP POST parameters
	 * 
	 * @return mixed
	 */
	function postParameter($parameter, $filter=true) {
		if (isset($_POST[$parameter])) {
			if ($filter) {
				return $this->cleanValue($_POST[$parameter]);
			} else {
				return $_POST[$parameter];
			}
		}
	}

	/**
	 * Alias for getParameter
	 *
	 * @deprecated	
	 */
	function g($parameter, $filter=true) {
		return $this->getParameter($parameter, $filter);
	}
	
	/**
	 * Alias for postParameter
	 *
	 * @deprecated
	 */
	function p($parameter, $filter=true) {
		return $this->postParameter($parameter, $filter);
	}
	
	/**
	 * Associative array of POST data fields
	 * 
	 * @return array
	 */
	function posted() {
		return array_map(array($this, 'cleanValue'), $_POST);
	}
	
	/**
	 * Accessor for the raw body of the HTTP request
	 * 
	 * @return string
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
	
	/**
	 * Client browser making this request.
	 * 
	 * @return UserAgent
	 */
	function browser() {
		return new UserAgent($this->envelope->header("User-Agent"));
	}
	
	/**
	 * Language accepted by the client
	 * 
	 * @return string
	 */
	function language() {
		return $this->envelope->header("Accept-Language");
	}

	/**
	 * HTTP referer of the current request
	 * 
	 * @return string
	 */
	function referer() {
		return $this->envelope->header("Referer");
	}
	
	/**
	 * Character encodings accepted by the client
	 * 
	 * @return string
	 */
	function charset() {
		return $this->envelope->header("Accept-Charset");
	}
	
	/**
	 * Accessor for a request header
	 *
	 * @return string
	 */
	function header($name) {
		return ($result = $this->envelope->header($name)) ? $result : false;
	}
	
	/**
	 * Accessor for uploaded files
	 */
	function getUploadedFile($name) {
		if (isset($_FILES[$name])) {
			return $_FILES[$name];
		}
	}
	
	
}

?>
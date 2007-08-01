<?php
// $Id$
/**
 * @package server
 */
require_once "UriPath.class.php";
require_once "UserAgent.class.php";
require_once "HttpEnvelope.class.php";
 
/**
 * Renders data output from server process.
 *
 * Provides the 'write' part of a web request->response process. Manages HTTP
 * response headers and a template string buffer for generating page views.
 * 
 * Uses PHP's built-in capability for managing HTTP headers.
 *
 * @package server
 */ 
class Response {

	private $buffer;
	private $headers;

	public function __construct() {
		ob_start();
		$this->buffer = '';
		$this->headers = array();
	}
	
	/**
	 * Writes the given HTTP Header to the response. If a
	 * header of the same type already exists, this
	 * header will overwrite the existing line.
	 */
	public function header($type, $value) {
		$this->headers[$type] = $value;
	}
	
	/**
	 * Writes the given string to the response
	 */
	public function write($output) {
		$this->buffer .= $output;
	}
	
	/**
	 * Dumps a variable to HTML format
	 */
	public function dump($variable) {
		$this->write('<pre>');
		$this->write(var_export($variable));
		$this->write('</pre>');
	}
	
	/**
	 * Renders a template object to the response buffer.
	 */
	public function render($template) {
		// not implemented
	}
	
	/**
	 * Send cookie headers.
	 */
	public function setCookie($name, $value, $expire=false) {
		if (!$expire) $expire = time()+3600;
		setcookie($name, $value, $expire);
	}
	
	/**
	 * Maps the registered HTTP Headers to the PHP
	 * server handler.
	 */
	private function sendHeaders() {
		if (!headers_sent()) {
			foreach($this->headers as $type => $value) {
				header($type . ': ' . $value);
			}
		} else {
			throw new Exception("Unexpected output sent");
		}
	}
	
	/**
	 * Renders the final response from server.
	 * 
	 * This method simply cleans the runtime output buffer
	 * and flushes the collected response buffer.
	 */
	public function out() {
		$this->sendHeaders();
		echo $this->buffer;
		ob_flush();
	}

}

?>
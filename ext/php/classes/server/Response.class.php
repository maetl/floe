<?php
/**
 * $Id$
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
	private $status;
	private $variables;
	
	public function __construct() {
		ob_start();
		$this->buffer = '';
		$this->headers = array();
		$this->status = 200;
		$this->variables = array();
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
	 * Assign a variable to the template
	 */
	public function assign($key, $value) {
		$this->variables[$key] = $value;
	}
	
	/**
	 * Renders a template object to the response buffer.
	 * 
	 * @throws Exception
	 */
	public function render($template) {
		extract($this->variables);
		ob_start();
		$templatePath = TPL_DIR . $template . ".php"; 
		if (file_exists($templatePath)) {
			include $templatePath;                  
		} else {
			throw new Exception("Response template not found");
		}
		$this->write(ob_get_contents());
		ob_clean();
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
		if (isset($this->status)) {
			header("HTTP/1.1 {$this->status}");
		}
		if (!headers_sent()) {
			foreach($this->headers as $type => $value) {
				header($type . ': ' . $value);
			}
		} else {
			throw new Exception("Unexpected output sent");
		}
	}
	
	/**
	 * Set the HTTP response status.
	 */
	public function status($code, $message=false) {
		if (!$message) {
			switch($code) {
				case 404: $message = "Not Found"; break;
			}
		}
		$this->status = $code . " " . $message;
	}
	
	/**
	 * Raise a runtime exception and handle the appropriate error
	 * response.
	 */
	public function raise(Exception $error) {
		if (isset($error->status)) $this->status($error->status, $error->getMessage());
		$this->write("<h1>".$error->getMessage()."</h1>");
		$this->write("<p>".$error->resource." (".$error->include.")</p>");
		$this->write("<ul>");
		foreach($error->getTrace() as $trace) {
			$method = $trace['class'].$trace['type'].$trace['function'];
			$fileref = "Line ".$trace['line']." of ".$trace['file'];
			$this->write("<li>$method ($fileref)</li>");
		}
		$this->write("</ul>");
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
<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package server
 */

require_once dirname(__FILE__)."/UriPath.class.php";
require_once dirname(__FILE__)."/UserAgent.class.php";
require_once dirname(__FILE__)."/HttpEnvelope.class.php";
require_once dirname(__FILE__)."/template/PhpTemplate.class.php";
require_once dirname(__FILE__)."/../framework/EventLog.class.php";

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
	private $template;
	
	public function __construct() {
		ob_start();
		$this->buffer = '';
		$this->headers = array();
		$this->status = 200;
		$this->variables = array();
		$this->template = new PhpTemplate();
	}
	
	public function setTemplate($handler) {
		$this->template = $handler;
	}
	
	/**
	 * Writes the given HTTP Header to the response. If a
	 * header of the same type already exists, this
	 * header will overwrite the existing line.
	 *
	 * @param string $type header name
	 * @param string $value header value
	 */
	public function header($type, $value) {
		$this->headers[$type] = $value;
	}
	
	/**
	 * Writes the given string to the response
	 *
	 * @param string $output
	 */
	public function write($output) {
		$this->buffer .= $output;
	}
	
	/**
	 * Dumps a variable to HTML format
	 *
	 * @param mixed $variable
	 */
	public function dump($variable) {
		$this->write('<pre>');
		$this->write(var_export($variable));
		$this->write('</pre>');
	}
	
	/**
	 * Assign a variable to the template
	 *
	 * @param string $key name of the variable
	 * @param mixed  $value the object to assign
	 */
	public function assign($key, $value=true) {
		$this->template->assign($key, $value);
	}
	
	/**
	 * synonym of assign
	 *
	 * @see Response::assign
	 */
	public function set($key, $value) {
		$this->template->set($key, $value);
	}
	
	/**
	 * Handles HTTP location redirect.
	 *
	 * @param string $path path to redirect to
	 * @param int $status optional HTTP status (defaults to 301?)
	 */
	public function redirect($path, $status=false) {
		if (!strstr($path, 'http://')) $path = WEB_HOST . $path;
		$this->header("Location", $path);
	}
	
	/**
	 * Renders a template to the response buffer.
	 * 
	 * @throws Exception
	 * @param string $template path to PHP template
	 */
	public function render($template) {
		$this->write($this->template->render($template));
	}
	
	/**
	 * Embed a template inside this template.
	 *
	 * <p>Can only be called from inside the template HTML scope:</p>
	 * 
	 * <pre>
	 *   &lt;form&gt;
	 *     &lt;?php $this->embed('form-fields'); ?&gt;
	 *   &lt;/form&gt;
	 * </pre>
	 *
	 * @param string $template path to template to include
	 */
	private function embed($template) {
		$this->template->embed($template);
	}
	
	/**
	 * Wraps a main layout template around the render call.
	 *
	 * @throws Exception
	 * @param string $template path to wrapping template
	 */
	function wrap($template) {
		$this->template->wrap($template);
	}
	
	/**
	 * Send cookie headers.
	 */
	public function setCookie($name, $value, $expire=false) {
		if (!$expire) $expire = time()+3600;
		setcookie($name, $value, $expire);
	}
	
	/**
	 * Sends the registered HTTP Headers to the PHP
	 * server handler.
	 *
	 * @throws Exception
	 */
	private function sendHeaders() {
		if (!headers_sent()) {
			if (isset($this->status)) {
				header("HTTP/1.1 {$this->status}");
			}
			foreach($this->headers as $type => $value) {
				header($type . ': ' . $value);
			}
		} else {
			throw new Exception("Unexpected output sent");
		}
	}
	
	/**
	 * Set the HTTP response status. 
	 * (see: http://en.wikipedia.org/wiki/List_of_HTTP_status_codes)
	 *
	 * @param int $code HTTP status code
	 * @param string $message HTTP status message
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
	 * Write a PHP template to the render buffer, applying any
	 * assigned variables to the current scope.
	 * 
	 * @param string $template template name
	 */
	private function writeTemplate($template) {
		extract($this->variables);
		$templatePath = TPL_DIR . "/" . $template . ".php";
		if (file_exists($templatePath)) {
			include $templatePath;
		} else {
			require_once 'ResourceNotFound.class.php';
			throw new ResourceNotFound("Response template not found", $templatePath);
		}
	}
	
	/**
	 * Raise a runtime exception and handle the appropriate error
	 * response.
	 */
	public function raise(Exception $error) {
		EventLog::error("[ERROR] [$error]");
		$this->status = (isset($error->status)) ? $error->status : 500;
		$template = ($this->status == 404) ? 'not-found' : 'internal-error';
		$message = ($error->getMessage()) ? $error->getMessage() : 'Internal Server Error';
		if (defined('ENVIRONMENT') && ENVIRONMENT == 'live') {
			$this->writeTemplate("errors/$template");
			return;
		}
		$this->write("<h1>".$error->getMessage()."</h1>");
		$path = (isset($error->include)) ? "(".$error->include.")" : '';
		$resource = (isset($error->resource)) ? $error->resource : get_class($error)." in line {$error->getLine()} of {$error->getFile()}";
		$this->write("<p>$resource $path</p>");
		$this->write("<ul>");
		foreach($error->getTrace() as $trace) {
			if (isset($trace['class'])) {
				$method = $trace['class'].$trace['type'].$trace['function'];
			} else {
				$method = $trace['function'];
			}
			if (isset($trace['line'])) {
				$method .= " (Line ".$trace['line']." of ".$trace['file'] . ")";	
			}
			$this->write("<li>$method</li>");
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
	
	/**
	 * Access the current output buffer string.
	 * 
	 * @return string
	 */
	public function body() {
		return $this->buffer;
	}
}
<?php
/**
 * $Id$
 * @package server
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
			$this->headers = array();
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
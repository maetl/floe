<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package server
 */

/**
 * HTTP protocol headers from the active request
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
	
	/**
	 * Return the highest priority language in list of accepted languages.
	 *
	 * @todo return Locale object?
	 * @return string ISO language code
	 */
	function language() {
		$languages = $this->languages();
		return key($languages);
	}
	
	/**
	 * Parse the Accept-Language header and return the list of
	 * accepted languages in order of priority.
	 *
	 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 * @return array map of accepted languages
	 */
	function languages() {
		if (!isset($this->languages)) {
			$range = $this->header('Accept-Language');
			$pattern = '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i';
			preg_match_all($pattern, $range, $parts);
			if (count($parts[1])) {
				$langs = array_combine($parts[1], $parts[4]);
				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}
				arsort($langs, SORT_NUMERIC);
				$this->languages = $langs;
			} else {
				$this->languages = array('en' => 1);
			}
		}
		return $this->languages;
	}
}

?>
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
 * Parses the incoming request URI and breaks it into useful chunks.
 *
 * @author maetl
 * @package server
 */
class UriPath {
	private $_raw;
	private $_parsed;
	private $_segments;
	private $_parameters;
	private $_resource;
	private $_identity;
	private $_extension;
	private $_query;
	private $_aspect;

	function UriPath($path) {
		$this->_raw = trim($path);
		$this->_segments = array();
		$this->_parameters = array();
		$this->parse();
	}
	
	/** @private */
	private function parse() {
		$this->_parsed = parse_url($this->_raw);
		if (strlen($this->_raw) > 1) {
			if (isset($this->_parsed['query'])) {
				parse_str(strip_tags($this->_parsed['query']), $parameters);
				$this->_parameters = $parameters;
			}
			$path = $this->explodeSegmentPath($this->_parsed['path']);
			$this->addResource(array_pop($path));
			while ($segment = array_pop($path)) {
				$this->_segments[] = urldecode($segment);
			}
			$this->_segments = array_reverse($this->_segments);
		}
	}
	
	/** @ignore */
	private function explodeSegmentPath($path) {
		if (substr($path, -1) == '/') $path = substr($path, 0, -1);
		return explode('/', substr($path, 1));
	}
	
	/** @private */
	private function addResource($resource) {
		if (strstr($resource, ";")) {
			$aspect = explode(";", $resource);
			$this->_aspect = $aspect[1];
			$resource = $aspect[0];
		}
		if (strstr($resource, ".")) {
			$identity = explode(".", $resource);
			$this->_identity = $identity[0];
			$this->_extension = $identity[1];
		}
		$this->_resource = urldecode($resource);
		$this->_segments[] = urldecode($resource);
	}
	
	/**
	* <p>Gives the resource name part.</p>
	* <p>Eg: <code>resource.ext#fragment</code></p>
	*/
	function resource() {
		if (isset($this->_parsed['fragment'])) {
			return $this->_resource . "#" . $this->_parsed['fragment'];
		} else {
			return $this->_resource;
		}
	}
	
	/**
	* <p>Gives the fragment identifier.</p>
	* <p>Eg: <code>#fragment</code></p>
	*/
	function fragment() {
		if (isset($this->_parsed['fragment'])) {
			return $this->_parsed['fragment'];
		}
	}
	
	/**
	* <p>Gives the identity of the requested resource.</p>
	*/
	function identity() {
		if (isset($this->_identity)) {
			return $this->_identity;
		} else {
			return $this->_resource;
		}
	}
	
	/**
	* Gives the file extension part of the requested resource.
	*/
	function ext() {
		if (isset($this->_extension)) {
			return $this->_extension;
		}
	}
	
	/**
	 * Alias for ext.
	 */
	function extension() {
		return $this->ext();
	}
	
	/**
	 * @todo implement multiple schemes
	 */
	function scheme() {
		return 'http';
	}
	
	/**
	 * Returns the HTTP host of this uri.
	 */
	function host() {
		return $_SERVER['HTTP_HOST'];
	}
	
	function path() {
		return $this->_parsed['path'];
	}
	
	function isEmpty() {
		return (strlen($this->_resource) == 0);
	}
	
	/**
	 * Returns the raw querystring.
	 */
	function query() {
		if (isset($this->_parsed['query'])) {
			return $this->_parsed['query'];
		}
	}
	
	/**
	 * Returns a hash of parameters from the querystring.
	 */
	function parameters() {
		return $this->_parameters;
	}
	
	/**
	 * Returns the value of given parameter.
	 */
	function parameter($key) {
		if (isset($this->_parameters[$key])) return $this->_parameters[$key];
	}
	
	/**
	 * Returns the given segment of the URL, starting from 1.
	 * 
	 * Eg: /content/topic/id gives:
	 *    $uri->segment(1) => content
	 *    $uri->segment(2) => topic
	 * 	  $uri->segment(3) => id
	 */
	function segment($index) {
		if (isset($this->_segments[$index])) return $this->_segments[$index];
	}
	
	/**
	 * Returns the base segment of the URI path.
	 * 
	 * Eg: /content/topic/id gives:
	 * 	  $uri->baseSegment => content
	 */
	function baseSegment() {
		if (isset($this->_segments[0])) return $this->_segments[0];
	}
	
	/**
	 * Returns a hash of path segments
	 */
	function segments() {
		return $this->_segments;
	}
	
	/**
	 * Returns number of segments in this url path.
	 */
	function segmentsCount() {
		return count($this->_segments);
	}
	
	/**
	 * Returns array of segments appearing after a given index.
	 * 
	 * Eg: /content/topic/id gives:
	 * 	  $uri->segmentsFrom(0) => array("content", "topic", "id")
	 */
	function segmentsFrom($index) {
		return array_slice($this->_segments, $index);
	}

	/**
	* Gives the aspect string of the requested URI
	* Eg: <code>/path/to/resource;aspect</code> returns <code>aspect</code>
	*/
	function aspect() {
		if (isset($this->_aspect)) return $this->_aspect;
	}
	
	/**
	 * Returns the full url.
	 */
	function url() {
		return $this->host() . $this->_raw;
	}
	
}

?>
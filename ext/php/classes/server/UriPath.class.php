<?php
// $Id: UriPath.class.php 48 2007-04-30 07:20:21Z maetl_ $
/**
 * @package server
 */
 
 /**
 * Parses the incoming request URI and breaks it into useful chunks.
 *
 * @author maetl
 * @package server
 */
class UriPath {
	var $_raw;
	var $_parsed;
	var $_segments;
	var $_parameters;
	var $_resource;
	var $_identity;
	var $_extension;
	var $_query;
	var $_aspect;

	function UriPath($path) {
		$this->_raw = trim($path);
		$this->_segments = array();
		$this->_parameters = array();
		$this->parse();
	}
	
	/** @private */
	function parse() {
		$this->_parsed = parse_url($this->_raw);
		if (strlen($this->_raw) > 1) {
			if (isset($this->_parsed['query'])) {
				parse_str(strip_tags($this->_parsed['query']), $parameters);
				$this->_parameters = $parameters;
			}
			$path = explode("/", substr($this->_parsed['path'], 1));
			$this->addResource(array_pop($path));
			while ($segment = array_pop($path)) {
				$this->addSegment($segment);
			}
		}
	}
	
	/** @private */
	function addResource($resource) {
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
	
	/** @private */
	function addSegment($segment) {
		$this->_segments[] = urldecode($segment);
	}
	
	/**
	* <p>Gives the resource name part.</p>
	* <p>Eg: <code>resource.ext#fragment</code></p>
	*/
	function getResource() {
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
	function getFragment() {
		if (isset($this->_parsed['fragment'])) {
			return $this->_parsed['fragment'];
		}
	}
	
	/**
	* <p>Gives the identity of the requested resource.</p>
	*/
	function getIdentity() {
		if (isset($this->_identity)) {
			return $this->_identity;
		} else {
			return $this->_resource;
		}
	}
	
	/**
	* Gives the file extension part of the requested resource.
	*/
	function getExtension() {
		if (isset($this->_extension)) {
			return $this->_extension;
		}
	}
	
	function getScheme() {
		return 'http';
	}
	
	function getHost() {
		return $_SERVER['HTTP_HOST'];
	}
	
	function getPath() {
		return $this->_parsed['path'];
	}
	
	function isEmpty() {
		return (strlen($this->_resource) == 0);
	}
	
	function getQuery() {
		if (isset($this->_parsed['query'])) {
			return $this->_parsed['query'];
		}
	}
	
	function getParameters() {
		return $this->_parameters;
	}
	
	function getParameter($key) {
		if (isset($this->_parameters[$key])) {
			return $this->_parameters[$key];
		}
	}
	
	function getSegments() {
		return $this->_segments;
	}
	
	function getBaseSegment() {
		if (count($this->_segments) > 0) {
			return $this->_segments[count($this->_segments)-1];
		} else {
			return '';
		}
	}
	
	function getSegment($index=1) {
		return $this->_segments[count($this->_segments)-$index];
	}	

	/**
	* Gives the aspect string of the requested URI
	* Eg: <code>/path/to/resource;aspect</code> returns <code>aspect</code>
	*/
	function getAspect() {
		if (isset($this->_aspect)) {
			return $this->_aspect;
		}
	}
	
	function getUrl() {
		return $this->getHost() . $this->_raw;
	}
	
}

?>
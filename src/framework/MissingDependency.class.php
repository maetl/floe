<?php
/**
 * $Id$
 * @package framework
 */

/**
 * Required class or PHP file could not be included.
 * 
 * @package framework
 */
class MissingDependency extends Exception {
	var $status = 500;
	var $message = "Missing Dependency";
	var $resource;
	var $include;
	
	function __construct($resource, $include) {
		$this->resource = $resource;
		$this->include = $include;
	}
	
}

?>
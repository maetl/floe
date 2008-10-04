<?php
/**
 * $Id$
 * @package framework
 */

/**
 * Record not found.
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
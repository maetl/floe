<?php
/**
 * $Id: ResourceNotFound.class.php 179 2008-09-02 11:02:38Z coretxt $
 * @package repository
 */

/**
 * Record not found.
 * 
 * @package repository
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
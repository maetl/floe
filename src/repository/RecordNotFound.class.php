<?php
/**
 * $Id$
 * @package repository
 */

/**
 * Record not found.
 * 
 * @package repository
 */
class RecordNotFound extends Exception {
	var $status = 404;
	var $message = "Record Not Found";
	var $resource;
	var $include;
	
	function __construct($resource, $include) {
		$this->resource = $resource;
		$this->include = $include;
	}
	
}

?>
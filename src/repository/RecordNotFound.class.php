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
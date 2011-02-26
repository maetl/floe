<?php
/**
 * $Id: RecordNotFound.class.php 311 2009-10-04 09:51:35Z coretxt $
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
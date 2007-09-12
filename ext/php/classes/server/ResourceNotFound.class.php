<?php
/**
 * @package server
 */

/**
 * HTTP Status Code 404.
 * 
 * Resource not found.
 * 
 * @package server
 */
require_once 'HttpError.class.php';

class ResourceNotFound extends HttpError {
	var $status = 404;
	var $message = "Resource Not Found";
	var $resource;
	var $include;
	
	function __construct($resource, $include) {
		$this->resource = $resource;
		$this->include = $include;
	}
	
}

?>
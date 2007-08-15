<?php
require_once 'HttpError.class.php';

class UnsupportedMethod extends HttpError {
	var $status = 405;
	var $message = "Unsupported Request Method";
	var $resource;
	var $include;
	
	function __construct($resource, $include) {
		$this->resource = $resource;
		$this->include = $include;
	}
	
}

?>
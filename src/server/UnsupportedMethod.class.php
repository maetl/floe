<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package server
 */

/**
 * HTTP Status Code 405.
 * 
 * Unsupported method.
 * 
 * @package server
 */
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
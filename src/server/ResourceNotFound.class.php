<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id: ResourceNotFound.class.php 349 2010-02-12 02:16:09Z coretxt $
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
	
	function __construct($resource, $include='') {
		$this->resource = $resource;
		$this->include = $include;
	}
	
}
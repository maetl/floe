<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage rules
 */
require_once 'ValidationRule.class.php';
	
/**
 * checks if the field contains a value
 *
 * @package repository
 * @subpackage rules
 */
class RequiredRule extends ValidationRule {
	var $message = "Field is required";
	
	function validate($value) {
		return (!$value || !isset($value) || $value === '') ? false : true;
	}

}


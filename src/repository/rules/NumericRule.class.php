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
 * Checks if the field is a number or numeric string
 *
 * @package repository
 * @subpackage rules
 */
class NumericRule extends ValidationRule {
	var $message = "Field must be a number";

	function validate($value) {
		return (!is_numeric($value)) ? false : true;
	}

}


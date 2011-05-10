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
 * checks if the field contains alphanumeric characters only
 * 
 * @package repository
 * @subpackage rules	
 */
class AlphaNumericRule extends ValidationRule {
	
	/**
	 * @todo document allowspaces option setting
	 * @todo unit tests
	 */
	function validate($value, $allowspaces=false) {
		if (strlen($value) == 0) return false;
		($allowspaces) ? $pattern = "/[^A-Za-z0-9\s]+/" : $pattern = "/[^A-Za-z0-9]+/";
		if (preg_match($pattern, $value)) {
			return false;
		} else {
			return true;
		}
	}

}
	
?>

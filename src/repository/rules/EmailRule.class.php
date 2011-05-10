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
 * checks if the field is a valid email address
 *
 * @package repository
 * @subpackage rules
 */
class EmailRule extends ValidationRule {
	var $message = "Please enter a valid email address";

	function validate($email) {
		$regex = '/^([a-zA-Z0-9_\\-\\.\+]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)$/';
		if (!preg_match($regex, $email)) {
			return false;
		} else {
			return true;
		}
	}

}


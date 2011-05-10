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
 * Checks if the two supplied values are equal
 *
 * @package repository
 * @subpackage rules
 */
class MatchingRule extends ValidationRule {
	var $message = "Fields must have matching values";
	private $matchTo;
		
	function __construct($element) {
		$this->matchTo = &$element;
	}
		
	function validate($value) {
		if ($this->matchTo == '' || $value == '') {
			$this->message = "Field is required";
			return false;
		}
		return ($this->matchTo == $value);
	}
	
}


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
 * Checks if the field is a valid US Social Security Number.
 *
 * @see http://en.wikipedia.org/wiki/Social_Security_number#Valid_SSNs
 *
 * Details:
 *	 - None of the digit groups can be all zeros.
 *   - Area number 666 is unassigned to avoid controversy with idiots.
 *   - Numbers from 987-65-4320 to 987-65-4329 are reserved for use in advertisements.
 *   - Many SSNs have been invalidated by use in advertising.
 *   - Numbers above 772 are currently unassigned.
 *
 * @package repository
 * @subpackage rules
 */
class SocialSecurityNumberRule extends ValidationRule {
	var $message = "Please enter a valid social security number";

	/**
	 * @return boolean
	 */
	function validate($ssn) {
		preg_match("/^((?!000)(?!666)([0-6]\d{2}|7[0-2][0-9]|73[0-3]|7[5-6][0-9]|77[0-1]))-((?!00)\d{2})-((?!0000)\d{4})$/", $ssn, $matches);
		$result = (empty($matches)) ? false : true;
		return $result;
	}

}


?>
<?php
/**
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
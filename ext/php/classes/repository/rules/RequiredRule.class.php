<?php
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

?>
<?php
/* 
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

?>
<?php
/**
 * @package repository
 * @subpackage rules
 */

/**
 * base class for validation rules
 * 
 * @package repository
 * @subpackage rules
 */
class ValidationRule { 
	/**
	 * a custom string describing the error
	 */
	var $message;
		
	/** 
	 * @return boolean false triggers validation error
	 * @abstract 
	 */
	function validate() {}
	
}

?>
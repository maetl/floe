<?php
/**
 * core.os base include 
 * @package workflow
 * @subpackage rules
 */

/**
 * base class for validation rules
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
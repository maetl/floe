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

/**
 * base class for validation rules
 * 
 * @package repository
 * @subpackage rules
 * @todo Convert to interface
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

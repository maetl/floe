<?php
/**
 * $Id$
 * @package repository
 * @subpackage rules
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
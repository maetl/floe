<?php
/**
 * $Id: MatchingRule.class.php 264 2009-03-22 06:29:51Z coretxt $
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


?>
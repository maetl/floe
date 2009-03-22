<?php
/**
 * $Id$
 * @package server
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

/**
 * Provides access to the capabilities of the browser
 * making the request.
 * 
 * @package server
 */
class UserAgent {
	var $properties;
	/**
	 * arg $agent User-Agent header string
	 */
	function __construct($agent) {
		$this->properties = $this->extract($agent);
	}

	function __get($key) {
		if (isset($this->properties->$key)) {
			return $this->properties->$key;
		}
	}

	/**
	 * parses the product description of User-Agent string,
	 * just a placeholder for now...
	 * @todo browser detection
	 */
	private function extract($agent) {
		$struct = new stdClass();
		if (strpos($agent, 'Mac')) {
			$struct->platform = 'Mac';
		} elseif (strpos($agent, 'Win')) {
			$struct->platform = 'Win';
		}
		if (strpos($agent, 'Gecko')) {
			$struct->engine = 'Gecko';
		} elseif (strpos($agent, 'MSIE')) {
			$struct->engine = 'IE';
		}
		if (strpos($agent, 'Firefox')) {
			$struct->vendor = 'Mozilla';
		}
		if (strpos($agent, 'Firefox')) {
			$struct->product = 'Firefox';
		}	
		return $struct;
	}
	
}



?>
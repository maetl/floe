<?php
// $Id$
/**
 * @package server
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
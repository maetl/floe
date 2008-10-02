<?php
/**
 * @package repository
 * @subpackage types
 */

/**
 * A date and time value.
 *
 * @package repository
 * @subpackage types
 */
class DateTimeType {
	
	private $value;
	
	function __construct($datetime=false) {
		if (is_int($datetime)) {
			$this->value = new DateTime($datetime);
		} else {
			$this->value = new DateTime(strtotime($datetime));
		}
	}
	
	/**
	 * Convert to default string format.
	 */
	function __toString() {
		return $this->value->format('Y-m-d h:i:s');
	}
	
	/**
	 * Format a date string.
	 */
	function format($date) {
		return $this->value->format($date);
	}
	
}

?>
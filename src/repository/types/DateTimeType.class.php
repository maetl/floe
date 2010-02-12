<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package repository
 * @subpackage types
 */

require_once dirname(__FILE__).'/../Type.class.php';

/**
 * A date and time value type.
 *
 * @package repository
 * @subpackage types
 */
class DateTimeType implements Type {
	const DefaultFormat = 'Y-m-d h:i:s';
	private $value;
	
	function __construct($value=false) {
		if (!$value) {
			$this->value = new DateTime();
		} elseif (is_numeric($value)) {
			$this->value = new DateTime(date(self::DefaultFormat, $value));
		} else {
			$this->value = new DateTime($value);
		}
	}
	
	/**
	 * Convert to default string format.
	 */
	function __toString() {
		return $this->value->format(self::DefaultFormat);
	}
	
	/**
	 * Format a date string, using the default date() syntax.
	 * @see http://www.php.net/manual/en/function.date.php
	 */
	function format($format) {
		return $this->value->format($format);
	}
	
	/**
	 * Format a date string, based on setlocale and the strftime() syntax.
	 * @see http://www.opengroup.org/onlinepubs/007908799/xsh/strftime.html
	 */
	function lformat($format) {
		return strftime($format, $this->value->getTimestamp());
	}
	
}

?>
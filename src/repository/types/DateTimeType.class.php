<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id: DateTimeType.class.php 379 2010-05-12 00:41:36Z coretxt $
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
	 * Format an English language date string, using the default PHP syntax.
	 *
	 * <code>$date->format('jS F Y');</code>
	 *
	 * @see http://www.php.net/manual/en/function.date.php
	 */
	function format($format) {
		return $this->value->format($format);
	}
	
	/**
	 * Format a date string using the strftime syntax.
	 *
	 * <p>Uses strftime:</p>
	 * <code>$date->strformat('%e %B %Y')</code>
	 * 
	 * @see http://php.net/manual/en/function.strftime.php
	 * @see http://www.opengroup.org/onlinepubs/007908799/xsh/strftime.html
	 */
	function strformat($format) {
		return strftime($format, $this->value->format('U'));
	}	
	
	/**
	 * Translate a date string using server locale settings.
	 *
	 * <p>Translated short date:</p>
	 * <code>$date->translate('ShortDate')</code>
	 * 
	 * <p>Translated long date:</p>
	 * <code>$date->translate('LongDate')</code>
	 */
	function to($format) {
		$format = Translation::format($format);
		return strftime($format, $this->value->format('U'));
	}

}

?>
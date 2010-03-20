<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
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
 * A time.
 *
 * @package repository
 * @subpackage types
 */
class TimeType implements Type {
	const DefaultFormat = 'h:i:s';
	private $value;
	
	function __construct($value=false) {
		if (!$value) {
			$this->value = new DateTime();
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
	 * Format a date string using default PHP callback.
	 *
	 * <code>$time->format('h:i:s');</code>
	 *
	 * @see http://php.net/manual/en/function.date.php
	 */
	function format($format) {
		return $this->value->format($format);
	}
	
	/**
	 * Format a date string using server locale settings.
	 *
	 * <p>Uses strftime:</p>
	 * <code>$time->translate('h:i:s')</code>
	 *
	 * @see http://php.net/manual/en/function.strftime.php
	 */
	function translate($format) {
		return strftime($format, $this->value->format('U'));
	}
	
}

?>
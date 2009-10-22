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
 * A single date type.
 *
 * @package repository
 * @subpackage types
 */
class DateType implements Type {
	const DefaultFormat = 'Y-m-d';
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
	 * Format a date string.
	 */
	function format($date) {
		return $this->value->format($date);
	}
	
}

?>
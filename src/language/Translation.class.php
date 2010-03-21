<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package language
 */

require_once dirname(__FILE__).'/LocaleFormat.class.php';

/**
 * Registry for managing translation settings.
 */
class Translation {
	
	private static $locale;
	
	/**
	 * Check if the given locale is available.
	 */	
	static function available($locale) {
		return file_exists(dirname(__FILE__).'/'.$locale.'/'.$locale.'.class.php');
	}	
	
	/**
	 * Set the global locale for this execution context.
	 *
	 * @todo accept-lang grammar/wikipedia reference for better param names
	 */
	static function locale($locale) {
		self::$locale = $locale;
		setlocale(LC_ALL, $locale);
		require_once dirname(__FILE__).'/'.$locale.'/'.$locale.'.class.php';
	}
	
	/**
	 * Returns the current global locale for this execution context.
	 */
	static function currentLocale() {
		if (!isset(self::$locale)) {
			return $_ENV['LANG'];
		} else {
			return self::$locale;
		}
	}
	
	/**
	 * Full name of the language for current global locale.
	 */	
	static function currentLanguage() {
		return constant(self::$locale.'::Name');		
	}
	
	/**
	 * Return a formatting string constant for the current locale.
	 */
	static function format($const) {
		return constant(self::$locale.'::'.$const);
	}
	
}

?>
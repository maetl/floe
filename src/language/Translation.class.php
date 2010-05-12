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
	 * Global locale accessor for this execution context.
	 *
	 * <p>This only supports two letter ISO language codes, not proper locales.</p>
	 *
	 * <h3>Getting the current locale:</h3>
	 * <code>$locale = Translation::locale(); // returns 'en', or 'de' etc...</code>
	 *
	 * <h3>Setting the locale:</h3>
	 * <code>$locale = Translation::locale('pl'); // sets the current locale to be Polish</code>
	 *
	 * @return two letter ISO language code, defaults to current system locale if none previously set
	 * @todo accept-lang grammar/wikipedia reference for better param names
	 * @todo shouldn’t throw an error if locale doesn't exist in Floe languages
	 * @todo support for custom locale paths in app/locales
	 */
	static function locale($locale=false) {
		if ($locale) {
			self::$locale = $locale;
			$formatPath = dirname(__FILE__).'/'.$locale.'/'.$locale.'.class.php';
			if (file_exists($formatPath)) {
				require_once $formatPath;
				setlocale(LC_ALL, self::locales());
			} else {
				setlocale(LC_ALL, self::$locale);
			}
		}
		return (!isset(self::$locale)) ? $_ENV['LANG'] : self::$locale;
	}
	
	/**
	 * Full name of the language for current global locale.
	 */	
	static function language() {
		return constant(self::$locale.'::Name');		
	}
	
	/**
	 * Return a formatting string constant for the current locale.
	 */
	static function format($const) {
		return constant(self::$locale.'::'.$const);
	}
	
	/**
	 * Return a fallback list of system names for the current locale.
	 *
	 * @compatibility PHP 5.2.2 only
	 */
	static function locales() {
		return call_user_func(array(self::$locale, 'locales'));
	}
	
}

?>
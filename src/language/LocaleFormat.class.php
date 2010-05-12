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

/**
 * Formatting strings for a particular locale.
 */
interface LocaleFormat {
	
	/**
	 * Return a standard list of fallback locales for the given language, used to set the
	 * system context with setlocale.
	 *
	 * <p>Known to support OSX and Debian/Ubuntu.</p>
	 *
	 * <p>Example implementation:</p>
	 *
	 * <code>function locales() {
	 *     return array('es_ES', 'es_ES.UTF8', 'es.UTF8');
	 * }</code>
	 *
	 * @return array list of fallback locales
	 */
	function locales();
	
}

?>
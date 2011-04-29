<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package language
 */

/**
 * Standard string conversions.
 */
class Convert {

	/**
	 * Transform a phrase or urlencoded string to program constant form.
	 * 
	 * For example, <b>language plurals</b> transforms to <b>LanguagePlurals</b>
	 * and <b>test-of-inflector</b> transforms to <b>TestOfInflector</b>.
	 * 
	 * @param $word string
	 */
	static function toConstant($word) {
		return str_replace(" ", "", ucwords(str_replace("_"," ", $word)));
	}
}
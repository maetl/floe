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
 * @package language
 */
interface Inflections {

	/**
	 * @param $word string
	 */
	static function toIdentifier($word);
	
	/**
	 * @param $word string
	 */
	static function toWords($word);

	/**
	 * @param $word string
	 */
	static function toSentence($word);
	
	/**
	 * @param $word string
	 */
	static function toPlural($word);
	
	/**
	 * @param $word string
	 */
	static function toSingular($word);
	
	/**
	 * @param $number numeric
	 */
	static function toOrdinal($number);
	
}

?>
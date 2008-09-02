<?php
/**
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
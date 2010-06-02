<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package language
 * @subpackage en
 */
 
require_once dirname(__FILE__).'/../Inflections.class.php';

/**
 * Performs standard linguistic transformations based on English grammar rules.
 * 
 * Inflections are language specific, and correspond roughly to
 * the strategy outlined in Damian Conway's paper 
 * {@link http://www.csse.monash.edu.au/~damian/papers/HTML/Plurals.html An Algorithmic Approach to English Pluralization}.
 * 
 * @package language
 * @subpackage en
 */
class Inflect implements Inflections {

	/**
	 * map of regular plural rules
	 */
	static private $RegularPluralRules = array(
		'/(x|ch|ss|sh)$/' => '\1es',
		'/([^aeiouy]|qu)y$/' => '\1ies',
		'/([o])$/' => '\1es',
		'/(?:([^f])fe|([lr])f)$/' => '\1\2ves',
		'/(analy|ba|diagno|parenthe|progno|synop|the)sis$/' => '\1ses',
		'/man$/' => '\1men',
		'/$/' => 's'
	);
			
	/**
	 * map of irregular plural rules
	 */
	static private $IrregularPluralRules = array(
		'movie' => 'movies',
		'person' => 'people',
		'child' => 'children',
		'octopus' => 'octopi',
		'news' => 'news',
		'status' => 'status',
		'series' => 'series'
	);
		
	/**
	 * map of singular rules
	 */
	static private $SingularRules = array(
		'/(x|ch|ss)es$/' => '\1',
		'/([^aeiouyv]|qu)ies$/' => '\1y',
		'/ies$/' => '\1ie',
		'/([lr])ves$/' => '\1f',
		'/([^f])ves$/' => '\1fe',
		'/(analy|ba|diagno|parenthe|progno|synop|the)ses$/' => '\1sis',
		'/([ti])a$/' => '\1um',
		'/men$/' => '\1man',
		'/s$/' => ''
	);

	/**
	 * Applies a map of grammar rules to match a given word.
	 * 
	 * Returns the transformed word, or false if the word was not matched.
	 * 
	 * @param $word string
	 * @param $rules array of grammar rules
	 */
	static private function applyRules($word, $rules) {
		foreach($rules as $rule => $replace) {
			if (preg_match($rule, $word)) {
				return preg_replace($rule.'i', $replace, $word);
			}
		}
		return false;
	}
	
	/**
	 * Because we can't compare array key case with an insensitive match,
	 * we need to use this to normalize the transformed word back to its original
	 * case.
	 *
	 * This currently only supports words in ASCII characters.
	 *
	 * @param $word string given word
	 * @param $result string transformed result
	 */
	static private function normalizeCase($word, $result) {
		if (ord($word[0]) > 64 && ord($word[0]) < 91) {
			return (ord($word[1]) > 64 && ord($word[1]) < 91) ? strtoupper($result) : ucfirst($result);
		}
		return $result;
	}
	
	/**
	 * Converts a singular word to plural form.
	 * 
	 * @param $word string
	 */
	static function toPlural($word) {
		if (isset(Inflect::$IrregularPluralRules[strtolower($word)])) {
			return Inflect::normalizeCase($word, Inflect::$IrregularPluralRules[strtolower($word)]);
		}
		return ($result = Inflect::applyRules($word, Inflect::$RegularPluralRules)) ? $result : $word;
	}
	
	/**
	 * Converts a plural word to singular form.
	 * 
	 * @param $word string
	 */
	static function toSingular($word) {
		if ($key = array_search(strtolower($word), Inflect::$IrregularPluralRules)) {
			return Inflect::normalizeCase($word, $key);
		}
		return ($result = Inflect::applyRules($word, Inflect::$SingularRules)) ? $result : $word;
	}

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

	/**
	 * Transform a phrase or urlencoded string to program identifier form.
	 * 
	 * Currently treats an identifier as a program constant. This may change
	 * to a standard property or method form, such as <b>testOfInflector</b>
	 * or <b>test_of_inflector</b>.
	 * 
	 * @param $word string
	 */
	static function toIdentifier($word) {
		return str_replace(" ", "", ucwords(str_replace("_"," ", $word)));
	}
	
	/**
	 * @param $word string
	 */
	static function toWords($word) {
	
	}

	/**
	 * Converts a string from camel cased identifier format to sentence case,
	 * adding space breaks between words where necessary and capitalizing
	 * the first word.
	 *
	 * @param $word string
	 */
	static function toSentence($word) {
		return ucfirst(strtolower(preg_replace("/([a-z]+)([A-Z])/","$1 $2", $word)));
	}
	
	/**
	 * @param $number numeric
	 */
	static function toOrdinal($number) {
	
	}
	
	/**
	 * Transforms a word to CamelCased form.
	 * 
	 * @param $word string
	 */
	static public function toClassName($word) {
		return str_replace(" ", "", ucwords(str_replace("_"," ", Inflect::underscore($word))));
	}
	
	/**
	 * Transforms a word to pluralized_underscored form.
	 * 
	 * @param $word string
	 */
	static public function toTableName($word) {
		return Inflect::toPlural(Inflect::underscore($word));
	}

	/**
	 * Transforms a word to underscore_separated form.
	 * 
	 * @param $word string
	 */
	static function underscore($word) {
		return str_replace(' ', '_', strtolower(preg_replace("/([a-z]+)([A-Z])/","$1 $2", str_replace('-', ' ', $word))));
	}

	/**
	 * Breaks down an encoded URI part into sentence form.
	 *
	 * @param string $part
	 * @return string	
	 */
	static function decodeUriPart($part) {
		return str_replace(' ', ' ', ucwords(str_replace('-', ' ', $part)));
	}
	
	/**
	 * Encodes a word or sentence into URI form.
	 *
	 * @param string $part
	 * @return string
	 */
	static function encodeUriPart($part) {
	   $part = strtolower($part);                         // To Lower Case
	   $part = str_replace('&', 'and', $part);            // Replace ampersands with 'and'
	   $part = preg_replace('/(\s|_)+/i', '-', $part);    // Replace whitespace and underscores with a dash
	   $part = preg_replace('/[^a-z0-9\-]/i', '', $part); // Remove anything that isn't lowercase-alpha-numeric or a dash
	   $part = preg_replace('/(\-)+/i', '-', $part);      // Replace multiple dashes with a single dash
	   $part = preg_replace('/^((\-)+)/i', '', $part);    // Trim dashes from the start of the string
	   $part = preg_replace('/((\-)+)$/i', '', $part);    // Trim dashes from the end of the string
	   return $part;
	}
		
	/**
	 * Transforms from a camelCasedProperty to underscored_property form.
	 */
	static function propertyToColumn($property_name) {
			return str_replace("", "_", strtolower(preg_replace("/([A-Z])/","_$1", $property_name)));
	}

	/**
	 * Transforms from underscored_property to camelCasedProperty form.
	 */
	static function columnToProperty($column_name) {
		$word = Inflect::toClassName($column_name);
		return strtolower($word[0]).substr($word,1);
	}
	
}
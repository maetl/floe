<?php
/**
 * $Id$
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
		'/man$/' => '\1men',
		'/$/' => 's'
	);
			
	/**
	 * map of irregular plural rules
	 */
	static private $IrregularPluralRules = array(
		'person' => 'people',
		'child' => 'children',
		'octopus' => 'octopi',
		'movie' => 'movies',
		'news' => 'news',
		'status' => 'status',
		'series' => 'series'
	);
		
	/**
	 * map of singular rules
	 */
	static private $SingularRules = array(
		'/(x|ch|ss)es$/' => '\1',
		'/([^aeiouy]|qu)ies$/' => '\1y',
		'/([lr])ves$/' => '\1f',
		'/([^f])ves$/' => '\1fe',
		'/(analy|ba|diagno|parenthe|progno|synop|the)ses$/' => '\1sis',
		'/([ti])a$/' => '\1um',
		'/men$/' => '\1man',
		'/s$/' => ''
	);
	
	/**
	 * Converts a singular word to plural form.
	 * 
	 * @param $word string
	 */
	static function toPlural($word) {
		if (array_key_exists($word, Inflect::$IrregularPluralRules)) {
			return Inflect::$IrregularPluralRules[$word];
		}
		return ($result = Inflect::applyRules($word, Inflect::$RegularPluralRules)) ? $result : $word;
	}
	
	/**
	 * Converts a plural word to singular form.
	 * 
	 * @param $word string
	 */
	static function toSingular($word) {
		if ($key = array_search($word, Inflect::$IrregularPluralRules)) {
			return $key;
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
	 * @param $word string
	 */
	static function toSentence($word) {
	
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
	function underscore($word) {
		return str_replace(' ', '_', strtolower(preg_replace("/([a-z]+)([A-Z])/","$1 $2", str_replace('-', ' ', $word))));
	}

	/**
	 * Breaks down a URI part into sentence form.
	 */
	function decodeUriPart($cPart) {
		return str_replace(" "," ", ucwords(str_replace("-"," ",$cPart)));
	}
	
	/**
	 * Encodes a word or sentence into URI form.
	 */
	function encodeUriPart($cPart) {
		return str_replace(" ", "-", strtolower(preg_replace("/([a-z]+)([A-Z])/","$1-$2", $cPart)));
	}
		
	/**
	 * Transforms from a camelCasedProperty to underscored_property form.
	 */
	function propertyToColumn($property_name) {
			return str_replace("", "_", strtolower(preg_replace("/([A-Z])/","_$1", $property_name)));
	}

	/**
	 * Transforms from underscored_property to camelCasedProperty form.
	 */
	function columnToProperty($column_name) {
		$word = Inflect::toClassName($column_name);
		return strtolower($word[0]).substr($word,1);
	}
	
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
				return preg_replace($rule, $replace, $word);
			}
		}
		return false;
	}
	
}

?>

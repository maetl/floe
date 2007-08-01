<?php

	/**
	 * @todo normalize naming convention
	 * @package language
	 * @subpackage en
	 */
	class Inflector {
		
		function decodeUriPart($cPart) {
			return str_replace(" "," ", ucwords(str_replace("-"," ",$cPart)));
		}
	
		function encodeUriPart($cPart) {
			return str_replace(" ", "-", strtolower(preg_replace("/([a-z]+)([A-Z])/","$1-$2", $cPart)));
		}
		
		function toIdentifier($id) {
			return ucfirst(Inflector::camelize(Inflector::singularize($id)));
		}
		
		function propertyToColumn($property_name) {
			 return str_replace("", "_",
strtolower(preg_replace("/([A-Z])/","_$1", $property_name)));
		}

		function columnToProperty($column_name) {
			$word = Inflector::camelize($column_name);
			return strtolower($word[0]).substr($word,1);
		}
		
		function pluralize ($word) {
			$plural_rules = array(
				'/(x|ch|ss|sh)$/'			=> '\1es',       # search, switch, fix, box, process, address
				'/series$/'					=> '\1series',
				'/([^aeiouy]|qu)ies$/'		=> '\1y',
				'/([^aeiouy]|qu)y$/'		=> '\1ies',      # query, ability, agency
				'/(?:([^f])fe|([lr])f)$/'   => '\1\2ves', 	 # half, safe, wife
				'/sis$/'					=> 'ses',        # basis, diagnosis
				'/([ti])um$/'				=> '\1a',        # datum, medium
				'/person$/'					=> 'people',     # person, salesperson
				'/man$/'					=> 'men',        # man, woman, spokesman
				'/child$/'					=> 'children',   # child
				'/s$/'						=> 's',          # no change (compatibility)
				'/$/'						=> 's'
			);
			foreach ($plural_rules as $rule => $replacement) {
				if (preg_match($rule, $word)) {
					return preg_replace($rule, $replacement, $word);
				}
			}
			return false;
		}
	
		function singularize ($word) {
			$word = Inflector::pluralize($word);
			$singular_rules = array(
				'/(x|ch|ss)es$/'		   => '\1',
				'/movies$/'				   => 'movie',
				'/series$/'				   => 'series',
				'/([^aeiouy]|qu)ies$/'     => '\1y',
				'/([lr])ves$/'			   => '\1f',
				'/([^f])ves$/'			   => '\1fe',
				'/(analy|ba|diagno|parenthe|progno|synop|the)ses$/' => '\1sis',
				'/([ti])a$/'				=> '\1um',
				'/people$/'					=> 'person',
				'/men$/'					=> 'man',
				'/status$/'					=> 'status',
				'/children$/'				=> 'child',
				'/news$/'					=> 'news',
				'/s$/'						=> ''
			);
			foreach ($singular_rules as $rule => $replacement) {
				if (preg_match($rule, $word)) {
					return preg_replace($rule, $replacement, $word);
				}
			}
			return false;
		}
		
		function camelize($lower_case_and_underscored_word) {
			return str_replace(" ","",ucwords(str_replace("_"," ",$lower_case_and_underscored_word)));
		}    
	
		function underscore($sentence_cased_word) {
			return str_replace(' ', '_', $sentence_cased_word);
		}
	
		function humanize($lower_case_and_underscored_word) {
			return ucwords(str_replace("_"," ",$lower_case_and_underscored_word));
		}
		
		function tableize($class_name) {
			return Inflector::pluralize(Inflector::underscore($class_name));
		}
		
		function toTableName($field_name) {
			return Inflector::tableize(str_replace("_id", "", $field_name));
		}
		
		function toClassName($cPart) {
			return str_replace(" ","", ucwords(str_replace("-"," ",$cPart)));
		}
	
		function foreignKey($class_name) {
			return $this->underscore($class_name) . "_id";
		}
		
		function getter($field_name) {
			return "get" . ucwords(Inflector::singularize($field_name));
		}

		function setter($field_name) {
			return "set" . ucwords(Inflector::singularize($field_name));
		}
		
		function finder($field_name) {
			return "findBy" . Inflector::camelize(Inflector::singularize($field_name));
		}
	
	}

?>
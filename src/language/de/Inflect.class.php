<?php
/**
 * $Id: Inflect.class.php 284 2009-05-21 23:06:07Z coretxt $
 * @package language
 * @subpackage de
 */
require_once dirname(__FILE__).'/../Inflections.class.php';

/**
 * German language support. Hahaha.. yeah, right.
 * {@link http://www.vistawide.com/german/grammar/german_nouns02.htm}
 *
 * @package language
 * @subpackage de
 */
class InflectDE {
	
	function toPlural($word) {
		return $word;
	}
	
}

?>
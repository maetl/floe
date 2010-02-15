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
 * @subpackage en
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
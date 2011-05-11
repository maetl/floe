<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package language
 * @subpackage de
 */
class de implements LocaleFormat {
	
	const Name = 'Deutsch';
	
	const ShortDate = '%e. %b %Y';
	
	const LongDate = '%e. %B %Y';
	
	function locales() {
		return array('de', 'de_DE', 'de_DE.UTF8', 'de.UTF8');
	}
}

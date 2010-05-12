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
class en implements LocaleFormat {
	
	const Name = 'English';
	
	const ShortDate = '%b %e %Y';
	
	const LongDate = '%B %e %Y';
	
	function locales() {
		return array('en', 'en_US', 'en_GB', 'en_US.UTF8', 'en_GB.UTF8', 'en.UTF8');
	}	
	
}

?>
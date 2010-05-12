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
 * @subpackage fr
 */
class fr implements LocaleFormat {
	
	const Name = 'Français';
	
	const ShortDate = '%e %b %Y';
	
	const LongDate = '%e %B %Y';
	
	function locales() {
		return array('fr_FR', 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');		
	}
	
}

?>
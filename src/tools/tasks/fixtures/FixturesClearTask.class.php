<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package tools
 * @subpackage tasks
 */

/**
 * @package tools
 * @subpackage tasks
 */
class FixturesClearTask {
	
	/**
	 * @description clears out fixture records from the database.
	 */
	function process() {
		ConsoleText::printLine("Clear fixtures");
	}
	
}

?>
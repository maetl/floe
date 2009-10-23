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
class ConfigureTask {
	
	/**
	 * @description load a configuration file into the include path
	 */
	function process($env) {
		if (!$env) {
			ConsoleText::printLine("Could not install: no environment specified.");
			return;
		}
		$source = DEV_DIR."/config/{$env[0]}.config.php";
		if (file_exists($source)) {
			copy($source, WEB_DIR.'/config.php');
		}
		ConsoleText::printLine("Installed configuration for {$env[0]} environment.");
	}
	
}

?>
<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
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
		ConsoleText::printLine("Deleting fixtures...");
		$db = StorageAdaptor::gateway();
		// stick all the model classes into the global namespace
		$dir = dir(MOD_DIR);
		while (false !== ($entry = $dir->read())) {
			preg_match("/([a-z-]+)\.model\.php/", $entry, $matches);
			if ($matches) {
				require_once MOD_DIR . $entry;
			}
		}
		// throw up a database table for each model
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, 'Record')) {
				$table = Inflect::toTableName($class);
				if ($db->hasTable($table)) {
					ConsoleText::printLine($table);
					$db->query("TRUNCATE TABLE `$table`");
				}
			}
		}		
	}
	
}

?>
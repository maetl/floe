<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id: FixturesLoadTask.class.php 328 2009-10-23 20:55:09Z coretxt $
 * @package tools
 * @subpackage tasks
 */

require_once dirname(__FILE__) . '/../../../repository/store/StorageAdaptor.class.php';
require_once dirname(__FILE__) . '/../../../repository/Record.class.php';

/**
 * @package tools
 * @subpackage tasks
 */
class FixturesDumpTask {
	
	/**
	 * @description extract json fixtures from dump of default store
	 */
	function process($args) {
		ConsoleText::printLine("Dumping fixtures from storage:");
		
		$db = StorageAdaptor::gateway();
		if ($db->hasTable("schema")) {
			$db->selectAll("schema");
			$this->writeFixture('schema', $db->getObjects());
		}
		
		$dir = dir(MOD_DIR);
		while (false !== ($entry = $dir->read())) {
			preg_match("/([a-z-]+)\.model\.php/", $entry, $matches);
			if ($matches) {
				require_once MOD_DIR . $entry;
			}
		}

		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, 'Record')) {
				$table = Inflect::toTableName($class);
				if ($db->hasTable($table)) {
					$db->selectAll($table);
					$this->writeFixture($table, $db->getObjects());
				}
			}
		}
	}
	
	function writeFixture($name, $objects) {
		$path = DEV_DIR.'/fixtures/'.$name.'.json';
		$encoded = json_encode($objects);
		file_put_contents($path, utf8_encode($encoded));
		ConsoleText::printLine($name);
	}
	
}
?>
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

require_once  dirname(__FILE__).'/../../../repository/store/StorageAdaptor.class.php';
require_once dirname(__FILE__).'/../../../repository/Record.class.php';

/** 
 * @package tools
 * @subpackage tasks
 */
class SchemaGenerateTask {
	
	/**
	 * @description generates a database schema from reflection on declared Record classes (warning: drops all existing tables)
	 */
	function process($args) {
		ConsoleText::printLine("Generating table schema...");
		$db = StorageAdaptor::gateway();
		if ($db->hasTable("schema")) $db->dropTable("schema");
		// stick all the model classes into the global namespace
		$dir = dir(MOD_DIR);
		while (false !== ($entry = $dir->read())) {
			preg_match("/([a-z-]+)\.model\.php/", $entry, $matches);
			if ($matches) {
				require_once MOD_DIR . $entry;
			}
		}
		$joinTables = array();
		// throw up a database table for each model
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, 'Record')) {
				$table = Inflect::toTableName($class);
				if ($db->hasTable($table)) $db->dropTable($table);
				$record = new $class();
				if (get_parent_class($record) == 'Record') {
					$db->createTable($table, $record->properties());
					ConsoleText::printLine("created $table");
				} else {
					foreach($record->properties() as $name=>$type) {
						try {
							$db->addColumn($record->getTable(), $name, $type); 
						} catch(ResourceError $e) {
							// passthrough, add hasField()
							// check to stop exception being thrown
						}
					}
				}
				foreach($record->relations() as $join) {
					if (!array_key_exists($join, $joinTables)) {
						$joinTables[$join] = Inflect::underscore($class)."_id";
					} else {
						$foreignKey = Inflect::underscore($class)."_id";
						$fields = array($joinTables[$join]=>"integer", $foreignKey=>"integer");
						if ($db->hasTable($join)) $db->dropTable($join);
						$db->createTable($join, $fields);
						ConsoleText::printLine("created $join");
					}
				}
			}
		}		
	}
	
}

?>
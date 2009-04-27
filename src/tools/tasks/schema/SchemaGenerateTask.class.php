<?php
/**
 * $Id$
 * @package tools
 * @subpackage tasks
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
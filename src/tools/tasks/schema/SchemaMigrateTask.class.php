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

/**
 * Runs a migration script.
 *
 * @package tools
 * @subpackage tasks
 */
class SchemaMigrateTask {
	
	/**
	 * @description performs a schema migration script
	 */
	function process($args) {
		if (empty($args)) {
			ConsoleText::printLine("No migration specified. Defaulting to latest version.");
			$args[0] = 'up';
		}
		$db = StorageAdaptor::gateway();
		if (!$db->hasTable("schema")) {
			echo "Creating schema table...\n";
			$db->createTable("schema", array("version"=>"integer"));
		}
		
		$db->query("select version from `schema` order by version desc");
		$version = $db->getValue();
		if (!$version) {
			$db->insert("schema", array("version"=>1));
			$version = 1;
		}
		
		$versionName = str_pad($version, 3, '0', STR_PAD_LEFT);
		$versions = $this->readMigrationDir();
		if ($args[0] == 'up') {
			sort($versions);
			$offset = array_search($versionName, $versions);
			$migrations = array_slice($versions, $offset+1);
		} elseif ($args[0] == 'down') {
			if (!isset($args[1])) die("No version specified for rollback.\n");
			rsort($versions);
			$offset = array_search($args[1], $versions);
			$total = count($versions);
			$migrations = array_slice($versions, 0, ($total-$offset)+1);
		} elseif ($args[0] == 'version') {
			echo "Schema Version $versionName\n";
			exit;
		} else {
			throw new Exception("Invalid migration method. Must be up or down.");
		}
		foreach($migrations as $migration) {
			$this->runMigration($migration, $args[0]);
		}
		$currentVersion = (int)$versions[count($versions)-1];
		$db->update("schema", array("id"=>1), array("version"=>$currentVersion));
	}
	
	function runMigration($version, $direction) {
		require_once DEV_DIR.'/migrations/'.$version.'.migration.php';
		$className = "Migration$version";
		if (class_exists($className)) {
			$migration = new $className;
			$migration->$direction();
			ConsoleText::printLine("Migrating $direction to $version");
		} else {
			throw new Exception("Migration $className not found");
		}
	}
	
	function readMigrationDir() {
		$versions = array();
		$dir = dir(DEV_DIR.'/migrations/');
		while (false !== ($entry = $dir->read())) {
			preg_match("/([0-9]+)\.migration\.php/", $entry, $matches);
			if ($matches) {
				$versions[] = $matches[1];
			}
		}
		return $versions;
	}
	
}

?>
<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package tools
 * @subpackage tasks
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
		
		$db = Storage::adaptor();
		if (!$db->hasTable("schema")) {
			echo "Creating schema table...\n";
			$db->createTable("schema", array("version"=>"integer"));
		}
		
		$db->query("select version from `schema` order by version desc");
		$version = $db->getValue();
		if (!$version) {
			$db->insert("schema", array("version"=>0));
			$version = 0;
		}
		
		$versionName = str_pad($version, 3, '0', STR_PAD_LEFT);
		$versions = $this->readMigrationDir();
		if ($args[0] == 'up') {
			sort($versions);
			$offset = array_search($versionName, $versions);
			$migrations = ($version == 0) ? $versions : array_slice($versions, $offset+1);
		
		} elseif ($args[0] == 'down') {
			if (!isset($args[1])) die("No version specified for rollback.\n");
			if (!is_numeric($args[1])) die("Invalid schema version (must be numeric).\n");
			
			rsort($versions);
			$offset = array_search($args[1], $versions);
			$total = count($versions);
			$migrations = array_slice($versions, 0, ($total-$offset)+1);
			
		} elseif ($args[0] == 'version') {
			ConsoleText::printLine("The installed schema is at version $versionName.");
			exit;
			
		} else {
			throw new Exception("Invalid migration method. Must be up or down.");
		}
		
		foreach($migrations as $migration) {
			$this->runMigration($migration, $args[0]);
		}
		
		$currentVersion = (count($versions) == 0) ? 1 : (int)$versions[count($versions)-1];
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
			throw new Exception("Class $className not found.");
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
		if (empty($versions)) throw new Exception("No migrations defined in dev directory.");
		return $versions;
	}
	
}

<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package tools
 * @subpackage tasks
 */

require_once dirname(__FILE__) . '/../../../repository/Record.class.php';

/**
 * Imports data from .json encoded fixtures.
 *
 * <p>All fixtures: <code>./floe fixtures:load</code></p>
 * <p>By name: <code>./floe fixtures:load books</code> will load <code>dev/fixtures/books.json</code></p>
 *
 * @package tools
 * @subpackage tasks
 */
class FixturesLoadTask {
	
	/**
	 * @description load fixtures into the database
	 */
	function process($args) {
		$fixtures = (empty($args)) ? $this->collectAll() : $this->loadFromList($args);
		
		$db = StorageAdaptor::gateway();
		foreach($fixtures as $table=>$rows) {
			$table = Inflect::toSingular($table);
			$table = Inflect::toTableName($table);
			echo "loading $table\n";
			foreach($rows as $row) {
				$db->insert($table, $row);
			}
		}
	}
	
	private function loadFromList($list) {
		$fixtures = array();
		foreach($list as $entry) {
			$fixturePath = DEV_DIR.'/fixtures/'.$entry.'.json';
			$fixtures[$entry] = $this->loadFixture($fixturePath);
		}
		return $fixtures;
	}
	
	private function loadFixture($entry) {
		if (file_exists($entry)) {
			return json_decode(file_get_contents($entry), true);
		} else {
			throw new Exception(basename($entry).' not found.');
		}
	}
	
	private function collectAll() {
		$fixtures = array();
		$dir = dir(DEV_DIR.'/fixtures/');
		while (false !== ($entry = $dir->read())) {
			preg_match("/([a-z-]+)\.json/", $entry, $matches);
			if ($matches) {
				$fixtures[$matches[1]] = $this->loadFixture(DEV_DIR.'/fixtures/'.$entry);
			}
		}
		return $fixtures;
	}
	
}

?>
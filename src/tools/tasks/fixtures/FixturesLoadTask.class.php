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

require_once dirname(__FILE__) . '/../../../repository/store/StorageAdaptor.class.php';
require_once dirname(__FILE__) . '/../../../repository/Record.class.php';

/**
 * @package tools
 * @subpackage tasks
 */
class FixturesLoadTask {
	
	/**
	 * @description load fixtures into the database
	 */
	function process($args) {
		$db = StorageAdaptor::gateway();
		$fixtures = array();

		$dir = dir(DEV_DIR.'/fixtures/');
		while (false !== ($entry = $dir->read())) {
			preg_match("/([a-z-]+)\.json/", $entry, $matches);
			if ($matches) {
				$fixtures[$matches[1]] = json_decode(file_get_contents(DEV_DIR.'/fixtures/'.$entry), true);
			}
		}

		foreach($fixtures as $table=>$rows) {
			$table = Inflect::toSingular($table);
			$table = Inflect::toTableName($table);
			echo "loading $table\n";
			foreach($rows as $row) {
				$db->insert($table, $row);
			}
		}
	
	}
	
}

?>
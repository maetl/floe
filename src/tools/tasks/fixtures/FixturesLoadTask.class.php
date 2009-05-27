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
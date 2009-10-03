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

require_once dirname(__FILE__).'/../../ConsoleText.class.php';
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/collector.php';

/**
 * @package tools
 * @subpackage tasks
 */
class TestListTask {
	
	function __construct() {
		// need to exit here to stop simpletest from borking on autorun
		register_shutdown_function(array($this, 'passthru'));
	}
	
	function passthru() {
		exit;
	}
	
	static $installedTests = array();
	
	/**
	 * @description view the list of installed tests
	 */
	function process($args) {
		ConsoleText::printLine("List of installed tests for app:");
		if (empty(self::$installedTests)) {
			$this->scanTests();
		}
		foreach(self::$installedTests as $test) ConsoleText::printLine($test);
	}
	
	function scanTests() {
		$baseClassList = get_declared_classes();
		$tests = new DirectoryIterator(DEV_DIR.'/tests');
		foreach($tests as $file) {
			if (strstr($file->getFileName(), '.test.php')) {
				$group = str_replace('.test.php', '', $file->getFileName());
				require_once DEV_DIR.'/tests/'.$file;
				$currentClassList = get_declared_classes();
				$testCases = array_diff($currentClassList, $baseClassList);
				$baseClassList = $currentClassList;
				foreach($testCases as $classname) {
					if (is_subclass_of($classname, 'UnitTestCase')) {
						self::$installedTests[] = $group.'.'.$classname;
					}
				}
			}
		}
	}
	
}

?>
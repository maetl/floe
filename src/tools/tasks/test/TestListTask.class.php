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
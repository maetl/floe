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

require_once dirname(__FILE__).'/../../ConsoleText.class.php';
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/collector.php';

/**
 * @package tools
 * @subpackage tasks
 */
class TestGroupTask {
	
	/**
	 * @description run a group of tests
	 */
	function process($args) {
		$testPath = DEV_DIR."/tests/{$args[0]}.test.php";
		if (file_exists($testPath)) {
			$test = new TestSuite(basename($testPath));
			$test->collect(dirname($testPath), new SimplePatternCollector("/".basename($testPath)."/"));
			$test->run(new TextReporter());
		} else {
			ConsoleText::writeLine("Test {$args[0]} not found");
		}
	}
	
}

?>
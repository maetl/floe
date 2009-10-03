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
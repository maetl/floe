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

require_once 'simpletest/unit_tester.php';
require_once 'simpletest/collector.php';
require_once 'simpletest/xml.php';

/**
 * @package tools
 * @subpackage tasks
 */
class TestAllTask {

	/**
	 * @description run all installed tests
	 */
	function process($args) {
		$test = new TestSuite(__CLASS__);
		$test->collect(DEV_DIR.'/tests', new SimplePatternCollector('/\.test\.php$/'));
		$index = new RecursiveDirectoryIterator(DEV_DIR.'/tests');
		foreach($index as $dir) {
			if ($dir->isDir()) $test->collect($dir->getPathName(), new SimplePatternCollector('/\.test\.php$/'));
		}
		if (isset($args[0])) {
			$reporterName = ucfirst($args[0])."Reporter";
			if (class_exists($reporterName)) $reporter = new $reporterName();
		}
		if (!isset($reporter)) $reporter = new DefaultReporter();
		$result = $test->run($reporter);
		return ($result ? 0 : 1);		
	}
	
}

?>
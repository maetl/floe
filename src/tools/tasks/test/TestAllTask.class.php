<?php
/**
 * $Id$
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
	function process() {
		require_once 'simpletest/unit_tester.php';
		require_once 'simpletest/collector.php';
		$test = new TestSuite(__CLASS__);
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
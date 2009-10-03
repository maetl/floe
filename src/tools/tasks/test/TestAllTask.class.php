<?php
/**
 * $Id: SchemaMigrateTask.class.php 273 2009-04-10 01:56:06Z coretxt $
 * @package tools
 * @subpackage tasks
 */

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
		$test->run(new SelectiveReporter(new TextReporter, @$_SERVER['argv'][2], @$_SERVER['argv'][3]));		
	}
	
}

?>
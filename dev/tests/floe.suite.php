<?php
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/collector.php';
require_once 'simpletest/extensions/treemap_reporter.php';

$test = new TestSuite('floe.ext.php.tests');
$fileRole = new SimplePatternCollector('/test\.php$/');
$test->collect(dirname(__FILE__), $fileRole);
$test->run(new TextReporter());

?>
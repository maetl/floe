<?php
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/collector.php';

$test = new TestSuite('floe.ext.php.tests');
$test->collect(dirname(__FILE__), new SimplePatternCollector('/test\.php$/'));
$test->run(new DefaultReporter());

?>
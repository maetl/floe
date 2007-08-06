<?php
// $Id$
require_once "simpletest/unit_tester.php";
require_once "simpletest/web_tester.php";
require_once "simpletest/shell_tester.php";
require_once 'simpletest/reporter.php';
require_once 'simpletest/collector.php';


$test = new TestSuite('floe.ext.php.tests');
$fileRole = new SimplePatternCollector('/test\.php$/');
$test->collect('/Users/maetl/Sites/floe/ext/php/tests/', $fileRole);
if ($ARGV['serialize']) {
	$test->run(new SerializeReporter());
} else {
	$test->run(new TextReporter());
}

?>
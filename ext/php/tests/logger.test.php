<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once 'EventLogger.class.php';

Mock::generate('LogHandler');

class EventLoggerTest extends UnitTestCase {
	
	function testMultipleEventsEmitToLog() {
		$handler = new MockLogHandler();
		$handler->expectCallCount('emit', 5);
		$handler->expectAt(0, 'emit', array(Level::Debug, 'zero'));
		$handler->expectAt(1, 'emit', array(Level::Info, 'one'));
		$handler->expectAt(2, 'emit', array(Level::Warning, 'two'));
		$handler->expectAt(3, 'emit', array(Level::Error, 'three'));
		$handler->expectAt(4, 'emit', array(Level::Critical, 'four'));
		EventLogger::handler($handler);
		
		EventLogger::debug("zero");
		EventLogger::info("one");
		EventLogger::warning("two");
		EventLogger::error("three");
		EventLogger::critical("four");
	}
	
}

?>
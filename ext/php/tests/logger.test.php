<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once 'classes/framework/EventLog.class.php';

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
		
		EventLog::handler($handler);
		
		EventLog::debug("zero");
		EventLog::info("one");
		EventLog::warning("two");
		EventLog::error("three");
		EventLog::critical("four");
		
		$this->assertIsA(EventLog::pop(), 'MockLogHandler');
		
		EventLog::debug("zero");
		EventLog::info("one");
		EventLog::warning("two");
		EventLog::error("three");
		EventLog::critical("four");
	}
	
}

?>
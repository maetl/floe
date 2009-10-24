<?php
require_once "simpletest/autorun.php";
require_once dirname(__FILE__).'/../../src/framework/Timer.class.php';

class TimerTest extends UnitTestCase {
	
	function testMeasureElapsedTimeFromStart() {
		$timer = new Timer();
		sleep(1);
		$this->assertTrue($timer->end() > 1.0);
		$timer = new Timer();
		sleep(2);
		$this->assertTrue($timer->end() > 2.0);
	}
	
}

?>
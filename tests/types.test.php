<?php
require_once 'simpletest/autorun.php';

require_once dirname(__FILE__).'/../src/repository/types/DateTimeType.class.php';
require_once dirname(__FILE__).'/../src/repository/types/DateType.class.php';
require_once dirname(__FILE__).'/../src/repository/types/TimeType.class.php';

class DateTimeTypeTest extends UnitTestCase {
	
	function testEmptyValueDefaultsToCurrentTime() {
		$obj = new DateTimeType();
		$this->assertEqual(date('Y-m-d h:i:s'), (string)$obj);
	}
	
	function testTimestampValueConversion() {
		$obj = new DateTimeType(315554400);
		$this->assertEqual(date('Y-m-d h:i:s', 315554400), (string)$obj);
	}
	
	function testDateStringValueConversion() {
		$obj = new DateTimeType('1st January 1980');
		$this->assertEqual('1980-01-01 12:00:00', (string)$obj);
	}
	
	function testLocaleFormat() {
		$date = new DateTimeType('01/01/2010');
		setlocale(LC_ALL, 'de_DE');
		$this->assertEqual('Januar', $date->strformat('%B'));
	}
	
}

class DateTypeTest extends UnitTestCase {
	
	function testEmptyValueDefaultsToToday() {
		$obj = new DateType();
		$this->assertEqual(date('Y-m-d'), (string)$obj);
	}
	
	function testTimestampValueConversion() {
		$obj = new DateType(315554400);
		$this->assertEqual(date('Y-m-d', 315554400), (string)$obj);
	}
	
	function testDateStringValueConversion() {
		$obj = new DateType('01/01/1980');
		$this->assertEqual('1980-01-01', (string)$obj);
	}
	
	function testLocaleFormat() {
		$date = new DateType('01/01/2010');
		setlocale(LC_ALL, 'nl_NL');
		$this->assertEqual('januari', $date->strformat('%B'));
	}
	
}

class TimeTypeTest extends UnitTestCase {
	
	function testEmptyValueDefaultsToToday() {
		$obj = new TimeType();
		$this->assertEqual(date('h:i:s'), (string)$obj);
	}
	
	function testLocaleFormat() {
		$date = new TimeType('01/01/2010');
		setlocale(LC_ALL, 'ru_RU');
		$this->assertEqual('января', $date->strformat('%B'));
	}
	
}

?>
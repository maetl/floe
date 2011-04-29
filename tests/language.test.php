<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../src/language/Translation.class.php';

class LanguageTranslateTest extends UnitTestCase {
	
	function testLocaleAccessor() {
		$this->assertEqual('de', Translation::locale('de'));
		$this->assertEqual('de', Translation::locale());
		$this->assertEqual('en', Translation::locale('en'));
		$this->assertEqual('en', Translation::locale());
	}
	
	function testFormatFromLocale() {
		$time = time();
		Translation::locale('de');
		$this->assertTrue(Translation::locale(), 'de');
		$this->assertEqual(strftime(de::LongDate, $time), strftime(Translation::format('LongDate'), $time));
	}
	
}

?>
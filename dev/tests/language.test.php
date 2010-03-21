<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/Translation.class.php';
require_once dirname(__FILE__).'/../../src/language/de/Inflect.class.php';

class LanguageTranslateTest extends UnitTestCase {
	
	function testLoadingLocaleFormatSettings() {
		$time = time();
		
		Translation::locale('en');
		$this->assertTrue(Translation::currentLanguage(), 'en');
		$this->assertEqual(strftime(en::LongDate, $time), strftime(Translation::format('LongDate'), $time));
		
		Translation::locale('de');
		$this->assertTrue(Translation::currentLanguage(), 'de');
		$this->assertEqual(strftime(de::LongDate, $time), strftime(Translation::format('LongDate'), $time));
	}
	
	function testAvailableLanguages() {
		$this->assertTrue(Translation::available('en'));
		$this->assertTrue(Translation::available('de'));
		$this->assertFalse(Translation::available('da'));		
	}
	
}

?>
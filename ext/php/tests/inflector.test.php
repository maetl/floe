<?php
require_once "simpletest/autorun.php";
require_once "language/en/Inflect.class.php";

class InflectorTest extends UnitTestCase {

	function testUriNameEncoding() {
		$this->assertEqual("graphic-design", Inflect::encodeUriPart("Graphic Design"));
		$this->assertEqual("what-is-a-page?", Inflect::encodeUriPart("What Is A Page?"));
	}
	
	function testUriNameDecoding() {
		$this->assertEqual("Graphic Design", Inflect::decodeUriPart("graphic-design"));
		$this->assertEqual("What Is A Page?", Inflect::decodeUriPart("what-is-a-page?"));
	}
	
	function testUnderscore() {
		$this->assertEqual('date_field', Inflect::underscore('date field'));
		$this->assertEqual('Date_Field', Inflect::underscore('Date Field'));
	}

}

?>
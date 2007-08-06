<?php
require_once "simpletest/autorun.php";
require_once "language/en/Inflect.class.php";

class InflectorTest extends UnitTestCase {

	function testUriNameEncoding() {
		$this->assertEqual("graphic-design", Inflector::encodeUriPart("Graphic Design"));
		$this->assertEqual("what-is-a-page?", Inflect::encodeUriPart("What Is A Page?"));
	}
	
	function testUriNameDecoding() {
		$this->assertEqual("Graphic Design", Inflect::decodeUriPart("graphic-design"));
		$this->assertEqual("What Is A Page?", Inflect::decodeUriPart("what-is-a-page?"));
	}

	function testPluralize() {
		$this->assertEqual('searches', Inflect::pluralize('search'));
		$this->assertEqual('searches', Inflect::pluralize('searches'));
		$this->assertEqual('books', Inflect::pluralize('book'));
		$this->assertEqual('halves', Inflect::pluralize('half'));
	}

	function testSinglularize() {
		$this->assertEqual('search', Inflect::singularize('searches'));
		$this->assertEqual('search', Inflect::singularize('search'));
		$this->assertEqual('parenthesis', Inflect::singularize('parentheses'));
		$this->assertEqual('book', Inflect::singularize('books'));
		$this->assertEqual('half', Inflect::singularize('halves'));
	}
	
	function testUnderscore() {
		$this->assertEqual('date_field', Inflect::underscore('date field'));
		$this->assertEqual('Date_Field', Inflect::underscore('Date Field'));
	}
	
	function testGetter() {
		$this->assertEqual('getAuthor', Inflect::getter('authors'));
		$this->assertEqual('getAuthor', Inflect::getter('author'));
		$this->assertEqual('getBook', Inflect::getter('books'));
	}
	
	function testSetter() {
		$this->assertEqual('setAuthor', Inflect::setter('authors'));
		$this->assertEqual('setAuthor', Inflect::setter('author'));
		$this->assertEqual('setTitle', Inflect::setter('titles'));
	}

}

?>
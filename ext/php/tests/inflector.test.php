<?php
require_once "simpletest/autorun.php";
require_once "language/en/Inflector.class.php";

class InflectorTest extends UnitTestCase {

	function testUriNameEncoding() {
		$this->assertEqual("graphic-design", Inflector::encodeUriPart("Graphic Design"));
		$this->assertEqual("what-is-a-page?", Inflector::encodeUriPart("What Is A Page?"));
	}
	
	function testUriNameDecoding() {
		$this->assertEqual("Graphic Design", Inflector::decodeUriPart("graphic-design"));
		$this->assertEqual("What Is A Page?", Inflector::decodeUriPart("what-is-a-page?"));
	}

	function testPluralize() {
		$this->assertEqual('searches', Inflector::pluralize('search'));
		$this->assertEqual('searches', Inflector::pluralize('searches'));
		$this->assertEqual('books', Inflector::pluralize('book'));
		$this->assertEqual('halves', Inflector::pluralize('half'));
	}

	function testSinglularize() {
		$this->assertEqual('search', Inflector::singularize('searches'));
		$this->assertEqual('search', Inflector::singularize('search'));
		$this->assertEqual('parenthesis', Inflector::singularize('parentheses'));
		$this->assertEqual('book', Inflector::singularize('books'));
		$this->assertEqual('half', Inflector::singularize('halves'));
	}
	
	function testUnderscore() {
		$this->assertEqual('date_field', Inflector::underscore('date field'));
		$this->assertEqual('Date_Field', Inflector::underscore('Date Field'));
	}
	
	function testGetter() {
		$this->assertEqual('getAuthor', Inflector::getter('authors'));
		$this->assertEqual('getAuthor', Inflector::getter('author'));
		$this->assertEqual('getBook', Inflector::getter('books'));
	}
	
	function testSetter() {
		$this->assertEqual('setAuthor', Inflector::setter('authors'));
		$this->assertEqual('setAuthor', Inflector::setter('author'));
		$this->assertEqual('setTitle', Inflector::setter('titles'));
	}

}

?>
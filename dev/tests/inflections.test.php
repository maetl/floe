<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/en/Inflect.class.php';

class EnglishPluralsTest extends UnitTestCase {

	function testRegularPluralNouns() {
		$this->assertEqual('boys', Inflect::toPlural('boy'));
		$this->assertEqual('girls', Inflect::toPlural('girl'));
		$this->assertEqual('cats', Inflect::toPlural('cat'));
		$this->assertEqual('dogs', Inflect::toPlural('dog'));
		$this->assertEqual('books', Inflect::toPlural('book'));
	}
	
	function testRegularSibilantPluralNouns() {
		$this->assertEqual('glasses', Inflect::toPlural('glass'));
		$this->assertEqual('phases', Inflect::toPlural('phase'));
		$this->assertEqual('witches', Inflect::toPlural('witch'));
	}
	
	function testNounsEndingInOeS() {
		$this->assertEqual('heroes', Inflect::toPlural('hero'));
		$this->assertEqual('volcanoes', Inflect::toPlural('volcano'));
	}
	
	function testVerbsEndingInEs() {
		$this->assertEqual('searches', Inflect::toPlural('search'));
	}
	
	function testNounsEndingInYIe() {
		$this->assertEqual('ladies', Inflect::toPlural('lady'));
		$this->assertEqual('lady', Inflect::toSingular('ladies'));
		$this->assertEqual('canaries', Inflect::toPlural('canary'));
		$this->assertEqual('canary', Inflect::toSingular('canaries'));
	}
	
	function testIrregularPluralNouns() {
		$this->assertEqual('parentheses', Inflect::toPlural('parenthesis'));
		$this->assertEqual('people', Inflect::toPlural('person'));
		$this->assertEqual('children', Inflect::toPlural('child'));
		$this->assertEqual('octopi', Inflect::toPlural('octopus'));
		$this->assertEqual('halves', Inflect::toPlural('half'));
		$this->assertEqual('movies', Inflect::toPlural('movie'));
	}

	function testIrregularSingularNouns() {
		$this->assertEqual('parenthesis', Inflect::toSingular('parentheses'));
		$this->assertEqual('movie', Inflect::toSingular('movies'));
		$this->assertEqual('selectedmovie', Inflect::toSingular('selectedmovies'));
		$this->assertEqual('person', Inflect::toSingular('people'));
		$this->assertEqual('child', Inflect::toSingular('children'));
		$this->assertEqual('octopus', Inflect::toSingular('octopi'));
		$this->assertEqual('half', Inflect::toSingular('halves'));			
	}
	
	function testCaseSensitiveSingularNouns() {
		$this->assertEqual('Movie', Inflect::toSingular('Movies'));
		$this->assertEqual('MOVIE', Inflect::toSingular('MOVIES'));
		$this->assertEqual('People', Inflect::toPlural('Person'));
		$this->assertEqual('PEOPLE', Inflect::toPlural('PERSON'));
		$this->assertEqual('Book', Inflect::toSingular('Books'));
		//$this->assertEqual('BOOK', Inflect::toSingular('BOOKS'));
		$this->assertEqual('Lady', Inflect::toSingular('Ladies'));
		//$this->assertEqual('LADY', Inflect::toSingular('LADIES'));
	}
	
	function testCaseSensitivePluralNouns() {
		$this->assertEqual('Movies', Inflect::toPlural('Movie'));
		$this->assertEqual('MOVIES', Inflect::toPlural('MOVIE'));
		$this->assertEqual('People', Inflect::toPlural('Person'));
		$this->assertEqual('PEOPLE', Inflect::toPlural('PERSON'));
		$this->assertEqual('Books', Inflect::toPlural('Book'));
		//$this->assertEqual('BOOKS', Inflect::toPlural('BOOK'));
		$this->assertEqual('Ladies', Inflect::toPlural('Lady'));
		//$this->assertEqual('LADIES', Inflect::toPlural('LADY'));		
	}
	
	function testRegularSingularNouns() {
		$this->assertEqual('boy', Inflect::toSingular('boys'));
		$this->assertEqual('girl', Inflect::toSingular('girls'));
		$this->assertEqual('cat', Inflect::toSingular('cats'));
		$this->assertEqual('dog', Inflect::toSingular('dogs'));
		$this->assertEqual('book', Inflect::toSingular('books'));	
	}

}

class StringTransformationUtilityTest extends UnitTestCase {

	function testPropertyToColumnFormat() {
		$this->assertEqual("item_id", Inflect::propertyToColumn("itemId"));	
	}
	
	function testUriNameEncoding() {
		$this->assertEqual("graphic-design", Inflect::encodeUriPart("Graphic Design"));
		$this->assertEqual("what-is-a-page", Inflect::encodeUriPart("What Is a Page?"));
		$this->assertEqual("what-is-a-page", Inflect::encodeUriPart("what is a page?"));
		$this->assertEqual("what-is-a-page", Inflect::encodeUriPart("What Is A Page?"));
		$this->assertEqual("remove-brackets", Inflect::encodeUriPart("Remove (brackets)"));
		$this->assertEqual("ampersands-and-more-so", Inflect::encodeUriPart("Ampersands & More So"));
		$this->assertEqual("ampersands-and-more-and-more", Inflect::encodeUriPart("Ampersands & More & More"));
		$this->assertEqual("crunchx", Inflect::encodeUriPart("CRUNCH^*%^*^*^*^*X"));
		$this->assertEqual("this-is-umpossible", Inflect::encodeUriPart("THIS IS UMPOSSIBLE"));
		$this->assertEqual("single-and-space-only", Inflect::encodeUriPart("Single & Space | Only"));
	}
	
	function testUriNameDecoding() {
		$this->assertEqual("Graphic Design", Inflect::decodeUriPart("graphic-design"));
		$this->assertEqual("What Is A Page?", Inflect::decodeUriPart("what-is-a-page?"));
	}
	
	function testUnderscore() {
		$this->assertEqual('date_field', Inflect::underscore('date field'));
		$this->assertEqual('date_field', Inflect::underscore('Date Field'));
		$this->assertEqual('date_field', Inflect::underscore('DateField'));
		$this->assertEqual('date_field', Inflect::underscore('date-field'));
		$this->assertEqual('date_time_field', Inflect::underscore('Date time field'));
		$this->assertEqual('date_time_field', Inflect::underscore('Date Time Field'));
		$this->assertEqual('date_time_field', Inflect::underscore('DateTimeField'));
		$this->assertEqual('date_time_field', Inflect::underscore('date-time-field'));
	}
	
	function testCamelizeClassName() {
		$this->assertEqual("Object", Inflect::toClassName("object"));
		$this->assertEqual("ObjectIdentifier", Inflect::toClassName("object identifier"));
		$this->assertEqual("ObjectIdentifier", Inflect::toClassName("Object identifier"));
		$this->assertEqual("ObjectIdentifier", Inflect::toClassName("object_identifier"));
		$this->assertEqual("ObjectIdentifier", Inflect::toClassName("object-identifier"));
		$this->assertEqual("ObjectIdentifierString", Inflect::toClassName("Object identifier_string"));
		$this->assertEqual("ObjectIdentifierString", Inflect::toClassName("Object Identifier String"));
		$this->assertEqual("ObjectIdentifierString", Inflect::toClassName("object-identifier-string"));
		$this->assertEqual("ObjectIdentifierString", Inflect::toClassName("object_identifier_string"));
	}
	
	function testUnderscoredPluralizedTableName() {
		$this->assertEqual("objects", Inflect::toTableName("object"));
		$this->assertEqual("objects", Inflect::toTableName("Object"));
		$this->assertEqual("object_identifiers", Inflect::toTableName("ObjectIdentifier"));
		$this->assertEqual("object_identifier_strings", Inflect::toTableName("ObjectIdentifierString"));
		$this->assertEqual("object_identifier_strings", Inflect::toTableName("Object Identifier String"));
		$this->assertEqual("object_identifier_strings", Inflect::toTableName("object-identifier-string"));
		$this->assertEqual("object_identifier_strings", Inflect::toTableName("object_identifier_string"));
	}
	
	function testColumnToProperty() {
		$this->assertEqual("property", Inflect::columnToProperty("property"));
		$this->assertEqual("occurredOn", Inflect::columnToProperty("occurred_on"));
		$this->assertEqual("occurredOn", Inflect::columnToProperty("OccurredOn"));
		$this->assertEqual("propertyAttribute", Inflect::columnToProperty("property_attribute"));
		$this->assertEqual("dereferencePropertyAttribute", Inflect::columnToProperty("dereference_property_attribute"));
	}
	
	function testIdentifierToSentence() {
		$this->assertEqual("Property", Inflect::toSentence("property"));
		$this->assertEqual("Property name", Inflect::toSentence("propertyName"));
		$this->assertEqual("Property of an object", Inflect::toSentence("propertyOfAnObject"));
	}

}

?>
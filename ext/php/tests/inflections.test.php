<?php
require_once "simpletest/autorun.php";
require_once "language/en/Inflect.class.php";

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
		$this->assertEqual('canaries', Inflect::toPlural('canary'));
	}
	
	function testIrregularNouns() {
		$this->assertEqual('people', Inflect::toPlural('person'));
		$this->assertEqual('children', Inflect::toPlural('child'));
		$this->assertEqual('octopi', Inflect::toPlural('octopus'));
		$this->assertEqual('halves', Inflect::toPlural('half'));
	}
	
	function testRegularSingularNouns() {
		$this->assertEqual('boy', Inflect::toSingular('boys'));
		$this->assertEqual('girl', Inflect::toSingular('girls'));
		$this->assertEqual('cat', Inflect::toSingular('cats'));
		$this->assertEqual('dog', Inflect::toSingular('dogs'));
		$this->assertEqual('book', Inflect::toSingular('books'));	
	}
	
	function testIrregularSingularNouns() {
		$this->assertEqual('parenthesis', Inflect::toSingular('parentheses'));
	}

}


?>
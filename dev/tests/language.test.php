<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/de/Inflect.class.php';

class DeutschPluralsTest extends UnitTestCase {

	function testRegularPluralNouns() {
		$this->assertEqual('Spanier', InflectDE::toPlural('Spanier'));
	}

}
?>
<?php
require_once "simpletest/autorun.php";
require_once dirname(__FILE__).'/../../src/repository/Record.class.php';

class DomainObject extends Record {

	function __define() {
		$this->property("key1", "string");
	}
	
	function getValueCheck() {
		return "value";
	}

}

class ConcreteDomainObject extends DomainObject {

	function __define() {
		$this->property("key2", "string");
	}

}

class RepositoryRecordSemanticsTest extends UnitTestCase {

	function setUp() {
		Record::baseAncestor('DomainObject');
	}

	function testBaseAncestorInheritance() {
		$obj = new ConcreteDomainObject();
		$this->assertEqual("value", $obj->getValueCheck());
	}
	
	function tearDown() {
		Record::baseAncestor('Record');
	}
}

?>
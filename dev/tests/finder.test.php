<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once dirname(__FILE__).'/../../src/repository/Finder.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
}

class PeopleFinder extends Finder {}

class Person extends Record {
	
	function __define() {
		$this->property("name", "string");
		$this->property("age", "integer");
	}
	
}

class FinderBasicQueryTest extends UnitTestCase {
	
	function setUp() {
		$db = StorageAdaptor::instance();
		$person = new Person();
		$db->createTable("people", $person->properties());
		$db->insert("people", array("name"=>"Mark", "age"=>29));
		$db->insert("people", array("name"=>"Maxwell", "age"=>29));
		$db->insert("people", array("name"=>"Maetl", "age"=>99));
	}
	
	function testCollection() {
		$finder = new PeopleFinder();
		$this->assertEqual(count($finder->findAll()), 3);
		$this->assertEqual(count($finder->findByKey("age", 29)), 2);
		$this->assertIsA($finder->findById(1), 'Person');
	}

	function tearDown() {
		$db = StorageAdaptor::instance();
		$db->dropTable("people");
	}
	
}

?>
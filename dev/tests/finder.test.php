<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once dirname(__FILE__).'/../../src/repository/Finder.class.php';
require_once dirname(__FILE__).'/../../src/repository/store/mysql/MysqlQuery.class.php';

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
		$this->property("gender", "string");
	}
	
}

class FinderBasicQueryTest extends UnitTestCase {
	
	function setUp() {
		$db = StorageAdaptor::instance();
		$person = new Person();
		$db->createTable("people", $person->properties());
		$db->insert("people", array("name"=>"Jack", "age"=>29, "gender"=>"male"));
		$db->insert("people", array("name"=>"Jill", "age"=>29, "gender"=>"female"));
		$db->insert("people", array("name"=>"Box", "age"=>99, "gender"=>"neuter"));
	}
	
	function testFindQueries() {
		$finder = new PeopleFinder();
		$this->assertEqual(count($finder->findAll()), 3);
		$this->assertEqual(count($finder->findByKey("age", 29)), 2);
		$query = new MysqlQuery();
		$query->whereNotEquals("age", 99);
		$this->assertEqual(count($finder->findByCriteria($query)), 2);
		$this->assertIsA($finder->findById(1), 'Person');
	}
	
	function testDynamicFindMethods() {
		$finder = new PeopleFinder();
		$person1 = $finder->findByName('Jack');
		$this->assertEqual(29, $person1->age);
		$person2 = $finder->findByNameAndAge("Box", 99);
		$this->assertEqual("neuter", $person2->gender);
		$person3 = $finder->findByNameAndAgeAndGender("Jill", 29, "female");
		$this->assertEqual("female", $person3->gender);
	}

	function tearDown() {
		$db = StorageAdaptor::instance();
		$db->dropTable("people");
	}
	
}

?>
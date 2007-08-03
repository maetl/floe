<?php
require_once 'simpletest/autorun.php';
require_once 'repository/Record.class.php';

class ActiveModelTest extends UnitTestCase {
	function ActiveModelTest() {
		parent::UnitTestCase();
		$config = Floe::defaultTestDb();
		define('DB_HOST', $config[0]);
		define('DB_NAME', $config[1]);
		define('DB_USER', $config[2]);
		define('DB_PASS', $config[3]);
	}
}

class Dog extends Record {
	function __define() {
		$this->property("age", "integer");
		$this->property("breed", "string");
		$this->property("name", "string");
	}
	function isPuppy() {
		return ($this->age < 3);
	}
}

class ModelWithBasicPropertiesTest extends ActiveModelTest {

	function setUp() {
		$dogModel = new Dog();
		$adaptor = StorageAdaptor::instance();
		$adaptor->createTable("dogs", $dogModel->properties());
	}

	function testCreateAndStoreNewInstance() {
		$dog = new Dog();
		$dog->age = 2;
		$dog->breed = "Terrier";
		$dog->name = "Jack";
		$this->assertTrue($dog->save());
		$id = $dog->id;
		unset($dog);
		$adaptor = StorageAdaptor::instance();
		$adaptor->selectById("dogs", $id);
		$dog = $adaptor->getRecord();
		$this->assertIsA($dog, 'Dog');
		$this->assertEqual(2, $dog->age);
		$this->assertEqual("Terrier", $dog->breed);
		$this->assertEqual("Jack", $dog->name);
		$this->assertTrue($dog->isPuppy());
	}
	
	function tearDown() {
		$adaptor = StorageAdaptor::instance();
		$adaptor->dropTable("dogs");
	}


}

?>
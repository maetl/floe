<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../../src/repository/services/sqlite/SqliteAdaptor.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
}

class SqliteQueryTest extends UnitTestCase {
	protected $db;
	private $db_name;
	
	function __construct() {
		parent::__construct();
		$env = new stdClass;
		$env->DB_NAME = 'db.floe_test_sqlite';
		$this->db_name = $env->DB_NAME;
		$this->db = new SqliteConnection($env);
	}
	
	function tearDown() {
		$this->db->execute("DROP TABLE people");
	}
	
	function testCanCreateTable() {
		$gateway = new SqliteAdaptor($this->db);
		$gateway->createTable("people", array('first_name'=>'string'));
		$this->assertTrue($this->db->execute("SELECT * FROM people"));
	}
	
	function testCanInsertAndUpdateOnTableWithSingleRecord() {
		$gateway = new SqliteAdaptor($this->db);
		$gateway->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$gateway->insert('people', array('id'=>1, 'first_name'=>'jim','age'=>27));
		$gateway->query('SELECT * FROM people WHERE id="1"');
		$result = $gateway->getObject();
		$this->assertEqual($result->id, 1);
		$this->assertEqual($result->first_name, 'jim');
		$this->assertEqual($result->age, 27);
		$gateway->update('people', array('id'=>1), array('first_name'=>'jimmy','age'=>38));
		$gateway->query('SELECT * FROM people WHERE id="1"');
		$result = $gateway->getObject();
		$this->assertEqual($result->first_name, 'jimmy');
		$this->assertEqual($result->age, 38);
	}
	
	function testCanDeleteFromTable() {
		$gateway = new SqliteAdaptor($this->db);
		$gateway->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$gateway->insert('people', array('id'=>1, 'first_name'=>'jules','age'=>26));
		$gateway->delete('people', array('id'=>1));
		$gateway->query('SELECT * FROM people WHERE id="1"');
		$this->assertFalse($gateway->getObject());
		$gateway->insert('people', array('id'=>1, 'first_name'=>'jules','age'=>37));
		$gateway->insert('people', array('id'=>2, 'first_name'=>'julius','age'=>38));
		$gateway->insert('people', array('id'=>3, 'first_name'=>'julius','age'=>39));
		$gateway->query('SELECT * FROM people');
		$this->assertEqual(3, count($gateway->getObjects()));
		$gateway->delete('people', array('first_name'=>'julius'));
		$gateway->query('SELECT * FROM people');
		$this->assertEqual(1, count($gateway->getObjects()));
	}
	
	function testCanSelectFromTable() {
		$gateway = new SqliteAdaptor($this->db);
		$gateway->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$gateway->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$gateway->insert('people', array('id'=>2, 'first_name'=>'markus','age'=>26));
		$gateway->insert('people', array('id'=>3, 'first_name'=>'markus','age'=>27));
		$gateway->query('SELECT * FROM people WHERE id=2');
		$person = $gateway->getObject();
		$this->assertEqual($person->first_name, 'markus');
		$this->assertEqual($person->age, '26');
		$gateway->query('SELECT * FROM people WHERE age=26');
		$people = $gateway->getObjects();
		$this->assertEqual($people[0]->first_name, 'mark');
		$this->assertEqual($people[1]->first_name, 'markus');
		$this->assertEqual(count($people), 2);
	}
	
	function testCanAlterTable() {
		$gateway = new SqliteAdaptor($this->db);
		$gateway->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$gateway->insert('people', array('id'=>1, 'first_name'=>'peter','age'=>26));
		$gateway->insert('people', array('id'=>2, 'first_name'=>'paul','age'=>26));
		$gateway->insert('people', array('id'=>3, 'first_name'=>'paul','age'=>27));
		$gateway->addColumn('people', 'birthdate', 'date');
		$gateway->update('people', array('id'=>2), array('birthdate'=>'1979-9-24'));
		$gateway->query('SELECT * FROM people WHERE id=2');
		$person = $gateway->getObject();
		$this->assertEqual($person->name, 'paul');
		$this->assertEqual($person->birthdate, '1979-09-24');
	}	
	
}

?>
<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../../src/repository/store/sqlite/SqliteGateway.class.php';

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
		$query = new SqliteGateway($this->db);
		$query->createTable("people", array('first_name'=>'string'));
		$this->assertTrue($this->db->execute("SELECT * FROM people"));
	}
	
	function testCanInsertAndUpdateOnTableWithSingleRecord() {
		$query = new SqliteGateway($this->db);
		$query->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$query->insert('people', array('id'=>1, 'first_name'=>'jim','age'=>27));
		$query->query('SELECT * FROM people WHERE id="1"');
		$result = $query->getObject();
		$this->assertEqual($result->id, 1);
		$this->assertEqual($result->first_name, 'jim');
		$this->assertEqual($result->age, 27);
		$query->update('people', array('id'=>1), array('first_name'=>'jimmy','age'=>38));
		$query->query('SELECT * FROM people WHERE id="1"');
		$result = $query->getObject();
		$this->assertEqual($result->first_name, 'jimmy');
		$this->assertEqual($result->age, 38);
	}
}

?>
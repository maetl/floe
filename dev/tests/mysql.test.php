<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../../src/repository/store/mysql/MysqlGateway.class.php';
require_once dirname(__FILE__).'/../../src/repository/Record.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
}

class MysqlQueryTest extends UnitTestCase {
	protected $db;
	
	function __construct() {
		parent::__construct();
		$env = new stdClass;
		$env->DB_HOST = 'localhost';
		$env->DB_NAME = 'floe_test';
		$env->DB_USER = 'default';
		$env->DB_PASS = 'launch';
		$this->db = new MysqlConnection($env);
	}
}
	
class MysqlQuerySingleTableTest extends MysqlQueryTest {

	function tearDown() {
		$this->db->execute("DROP TABLE people");
	}
	
	function testCanCreateTable() {
		$query = new MysqlGateway($this->db);
		$query->createTable("people", array('first_name'=>'string'));
		$this->assertTrue($this->db->execute("DESCRIBE people"));
	}
	
	function testCanInsertIntoAndUpdateTable() {
		$query = new MysqlGateway($this->db);
		$query->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$query->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$result = mysql_fetch_object($this->db->execute('SELECT * FROM people WHERE id="1"'));
		$this->assertEqual($result->id, 1);
		$this->assertEqual($result->first_name, 'mark');
		$this->assertEqual($result->age, 26);
		$query->update('people', array('id'=>1), array('age'=>27));
		$result = mysql_fetch_object($this->db->execute('SELECT * FROM people WHERE id="1"'));
		$this->assertEqual($result->age, 27);
		$query->update('people', array('id'=>1), array('first_name'=>'markus','age'=>26));
		$result = mysql_fetch_object($this->db->execute('SELECT * FROM people WHERE id="1"'));
		$this->assertEqual($result->first_name, 'markus');
		$this->assertEqual($result->age, 26);
	}
	
	function testCanDeleteFromTable() {
		$query = new MysqlGateway($this->db);
		$query->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$query->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$query->delete('people', array('id'=>1));
		$this->assertFalse(mysql_fetch_object($this->db->execute('SELECT * FROM people WHERE id="1"')));
		$query->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$query->insert('people', array('id'=>2, 'first_name'=>'markus','age'=>26));
		$query->insert('people', array('id'=>3, 'first_name'=>'markus','age'=>27));
		$this->assertTrue(mysql_fetch_object($this->db->execute('SELECT * FROM people')));
		$query->delete('people', array('first_name'=>'markus'));
		$this->assertTrue(mysql_fetch_object($this->db->execute('SELECT * FROM people')));
		$query->delete('people', array('age'=>'26'));
		$this->assertFalse(mysql_fetch_object($this->db->execute('SELECT * FROM people')));
	}
	
	function testCanSelectFromTable() {
		$query = new MysqlGateway($this->db);
		$query->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$query->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$query->insert('people', array('id'=>2, 'first_name'=>'markus','age'=>26));
		$query->insert('people', array('id'=>3, 'first_name'=>'markus','age'=>27));
		$query->selectById('people', 2);
		$person = $query->getObject();
		$this->assertEqual($person->first_name, 'markus');
		$this->assertEqual($person->age, '26');
		$query->selectByKey('people', array('age'=>26));
		$people = $query->getObjects();
		$this->assertEqual($people[0]->first_name, 'mark');
		$this->assertEqual($people[1]->first_name, 'markus');
		$this->assertEqual(count($people), 2);
	}
	
	function testCanAlterTable() {
		$query = new MysqlGateway($this->db);
		$query->createTable("people", array('first_name'=>'string', 'age'=>'number'));
		$query->insert('people', array('id'=>1, 'first_name'=>'mark','age'=>26));
		$query->insert('people', array('id'=>2, 'first_name'=>'markus','age'=>26));
		$query->insert('people', array('id'=>3, 'first_name'=>'markus','age'=>27));
		$query->addColumn('people', 'birthdate', 'date');
		$query->changeColumn('people', 'first_name', 'name');
		$query->update('people', array('id'=>2), array('birthdate'=>'1979-9-24'));
		$query->selectById('people', 2);
		$person = $query->getObject();
		$this->assertEqual($person->name, 'markus');
		$this->assertEqual($person->birthdate, '1979-09-24');
	}

}

class MysqlRecordIteratorTest extends MysqlQueryTest {

	function setUp() {
		$query = new MysqlGateway($this->db);
		$query->createTable("books", array('title'=>'string','author_id'=>'int'));
		$query->createTable("authors", array('name'=>'string'));
	}
	
	function tearDown() {
		$query = new MysqlGateway($this->db);
		$query->dropTable("books");
		$query->dropTable("authors");
	}

	function testCanIterateFromQuery() {
		$query = new MysqlGateway($this->db);
		$query->insert('authors', array('name'=>'James Joyce'));
		$author_id = $query->insertId();
		$query->insert('books', array('title'=>'Finnegans Wake','author_id'=>$author_id));
		$query->insert('books', array('title'=>'Ulysses','author_id'=>$author_id));
		$query->insert('authors', array('name'=>'H. P. Lovecraft'));
		$author_id = $query->insertId();
		$query->insert('books', array('title'=>'At the Mountains of Madness','author_id'=>$author_id));
		$query->selectAll('books');
		$booksIterator = $query->getIterator();
		$this->assertEqual($booksIterator->count(), 3);
		while ($row = $booksIterator->next()) {
			$this->assertIsA($row, 'stdClass');
		}
		$query->selectAll('authors');
		$authorsIterator = $query->getIterator();
		$this->assertEqual($authorsIterator->count(), 2);
	}

}

?>
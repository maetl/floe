<?php
require_once 'simpletest/autorun.php';
require_once 'classes/language/en/Inflect.class.php';
require_once 'classes/repository/store/mysql/MysqlAdaptor.class.php';
require_once 'classes/repository/Record.class.php';

class MysqlQueryTest extends UnitTestCase {
	function MysqlQueryTest() {
		parent::UnitTestCase();
		$this->db = new MysqlConnection();
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

class Thing extends Record {
	function __define() {
		$this->property('stringField', 'string');
		$this->property('integerField', 'integer');
		$this->property('floatField', 'float');
		$this->property('dateField', 'datetime');
	}
}

class MysqlRecordFieldTypesTest extends MysqlQueryTest {

	function testCanInsertAllTypes() {
		$query = new MysqlGateway($this->db);
		
		$query->createTable("things", array(
										'string_field'=>'a string',
										'integer_field'=>'int',
										'float_field'=>'float',
										'date_field'=>'date',
										'datetime_field'=>'datetime'
											));
	
		$typeValues = array(
			'string_field' => 'a string',
			'integer_field' => 33,
			'float_field' => 2.567,
			'date_field' => '2006-09-09'
		);
		
		$query->insert("things", $typeValues);
		
		$query->selectById('things', $query->insertId());
		$object = $query->getRecord();
		$this->assertTrue(is_string($object->stringField));
		$this->assertTrue(is_integer($object->integerField));
		$this->assertEqual(33, $object->integerField);
		$this->assertTrue(is_float($object->floatField));
		$this->assertIdentical(2.567, $object->floatField);
		$this->assertIsA($object->dateField, 'DateTime');
		$this->assertEqual('2006-09-09', $object->dateField->format('Y-m-d'));
		
		$query->dropTable("things");
	}

}

?>
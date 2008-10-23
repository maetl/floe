<?php
require_once 'simpletest/autorun.php';
require_once 'simpletest/mock_objects.php';
require_once dirname(__FILE__).'/../../src/repository/store/StorageAdaptor.class.php';
require_once dirname(__FILE__).'/../../src/repository/store/mysql/MysqlQuery.class.php';

class MysqlQueryCriteriaTest extends UnitTestCase {

	function testStarSelect() {
		$query = StorageAdaptor::query();
		$query->select()->from("articles");
		$this->assertEqual($query->__toString(), "SELECT * FROM articles");
	}
	
	function testMultipleColumnsSelectAsArray() {
		$query = StorageAdaptor::query();
		$query->select(array("title","summary"))->from("articles");
		$this->assertEqual($query->__toString(), "SELECT title,summary FROM articles");
	}
	
	function testMultipleColumnsSelectAsArgs() {
		$query = StorageAdaptor::query();
		$query->select("title","summary","updated")->from("articles");
		$this->assertEqual($query->__toString(), "SELECT title,summary,updated FROM articles");
	}	
	
	function testSingleColumnsSelect() {
		$query = StorageAdaptor::query();
		$query->select("title")->select("summary")->from("articles");
		$this->assertEqual($query->__toString(), "SELECT title,summary FROM articles");
	}
	
	function testFieldSelectWithWhereClause() {
		$query = StorageAdaptor::query();
		$query->select(array("title","summary"))->from("articles")->whereEquals("title", "Hello");
		$this->assertEqual($query->__toString(), "SELECT title,summary FROM articles WHERE title = 'Hello'");
	}
	
	function testSingleWhereClause() {
		$query = StorageAdaptor::query();
		$query->whereEquals("key", "value");
		$this->assertEqual($query->toSql(), "WHERE key = 'value'");
	}
	
	function testMultiplePredicates() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->whereEquals("lol", "rofl");
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' AND lol = 'rofl'");
	}
	
	function testMultiplePredicateOperators() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->whereNotEquals("lol", "rofl");
		$query->whereLike("goto", "hell");
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' AND lol != 'rofl' AND goto LIKE '%hell%'");
	}
	
	function testChainedMultiplePredicateOperators() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar")->whereNotEquals("lol", "rofl")->whereLike("goto", "hell");
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' AND lol != 'rofl' AND goto LIKE '%hell%'");
	}
	
	function testDefaultOrderByClause() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->orderBy('foo');
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' ORDER BY foo DESC");
	}
	
	function testExplicitOrderByClause() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->orderBy('foo')->asc();
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' ORDER BY foo ASC");
	}
	
	function testZeroLimitClause() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->orderBy('foo')->asc()->limit(0,10);
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' ORDER BY foo ASC LIMIT 0,10");
	}	
	
	function testPositiveLimitClause() {
		$query = StorageAdaptor::query();
		$query->whereEquals("foo", "bar");
		$query->orderBy('foo')->asc()->limit(10,20);
		$this->assertEqual($query->toSql(), "WHERE foo = 'bar' ORDER BY foo ASC LIMIT 10,20");
	}
	
	function testTableAliases() {
		$query = StorageAdaptor::query();
		$query->select("i.field")->from("items i");
		$query->whereEquals("foo", "bar");
		$this->assertEqual($query->__toString(), "SELECT i.field FROM items i WHERE foo = 'bar'");
	}
	
	function testColumnAliasesAsGiven() {
		$query = StorageAdaptor::query();
		$query->select("i.field AS fsharp", "i.foo AS fb")->from("items i");
		$query->whereEquals("foo", "bar");
		$this->assertEqual($query->__toString(), "SELECT i.field AS fsharp,i.foo AS fb FROM items i WHERE foo = 'bar'");
	}	
	
	function testSelectCountFunction() {
		$query = StorageAdaptor::query();
		$query->selectCount()->from("things");
		$this->assertEqual($query->__toString(), "SELECT COUNT(id) AS count FROM things");
	}
	
	function testWhereJoinFunction() {
	    $query = StorageAdaptor::query();
	    $query->select()->from('movie','trailer')->whereJoin('trailer.id','movie.id');
	    $this->assertEqual($query->__toString(),"SELECT * FROM movie WHERE trailer.id = movie.id");
	}
	
}

?>
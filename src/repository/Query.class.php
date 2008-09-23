<?php
require_once dirname(__FILE__).'/../language/en/Inflect.class.php';

/**
 * Criteria based query interface.
 */
class Query {
	
	protected $limitLower;
	
	protected $limitUpper;
	
	protected $orderBy;
	
	protected $orderDir;
	
	protected $whereClauses;
	
	function __construct() {
		$this->whereClauses = array();
	}
	
	static function criteria($field, $operator, $value) {
		$criteria = new stdClass;
		$criteria->field = Inflect::underscore($field);
		$criteria->operator = $operator;
		$criteria->value = $value;
		return $criteria;
	}
	
	function whereEquals($key, $value) {
		$this->whereClauses[] = self::criteria($key, "=", $value);
		return $this;		
	}
	
	function whereNotEquals($key, $value) {
		$this->whereClauses[] = self::criteria($key, "!=", $value);
		return $this;		
	}
	
	function whereLike($key, $value) {
		$this->whereClauses[] = self::criteria($key, "LIKE", "%$value%");
		return $this;				
	}
	
	function whereGreaterThan($key, $value) {
		$this->whereClauses[] = self::criteria($key, ">", $value);
		return $this;		
	}

	function whereLessThan($key, $value) {
		$this->whereClauses[] = self::criteria($key, "<", $value);
		return $this;		
	}
	
	function whereWithinRange($key, $upper, $lower) {
		$this->whereClauses[] = self::criteria($key, "BETWEEN $upper AND", $lower);
		return $this;
	}
	
	function whereNotWithinRange($upper, $lower) {
		$this->whereClauses[] = self::criteria($key, "NOT BETWEEN $upper AND", $lower);
		return $this;		
	}	
	
	function orderBy($field) {
		$this->orderBy = Inflect::underscore($field);
		return $this;
	}
	
	function limit($lower, $upper) {
		$this->limitLower = (integer)$lower;
		$this->limitUpper = (integer)$upper;
		return $this;
	}
	
	function desc() {
		$this->orderDir = 'DESC';
		return $this;
	}
	
	function asc() {
		$this->orderDir = 'ASC';
		return $this;
	}
	
}

?>
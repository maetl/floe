<?php
/**
 * @package repository
 */
require_once dirname(__FILE__).'/../language/en/Inflect.class.php';

/**
 * Criteria based select query interface.
 * 
 * @package repository
 */
class Query {
	
	protected $limitLower;
	
	protected $limitUpper;
	
	protected $orderBy;
	
	protected $orderDir;
	
	protected $whereClauses;
	
	protected $selectFields;
	
	protected $tableName;
	
	function __construct() {
		$this->selectFields = array();
		$this->whereClauses = array();
	}
	
	/**
	 * Add a field to the select query.
	 */
	function selectColumn($column) {
		$this->selectFields[] = $column;
		return $this;
	}
	
	/**
	 *
	 */
	function selectColumns($columns) {
		$this->selectFields = array_merge($this->selectFields, $columns);
		return $this;
	}

	/**
	 * Select all fields from the table.
	 */
	function selectAll() {
		$this->selectFields[] = "*";
		return $this;
	}
	
	/**
	 * Add a table name.
	 */
	function from($table) {
		$this->tableName = $table;
		return $this;
	}
	
	/**
	 * Static factory for generating a criteria object.
	 *
	 * Criteria objects represent predicates in a where clause,
	 * consisting of a field to match on, the value to match against, 
	 * and an operator to express the match.
	 *
	 * <code>Query::criteria("title", "=", "My Title");</code>
	 * <code>Query::criteria("count", ">=", 999);</code>
	 */
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
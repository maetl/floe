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
	 * Select given columns from the target table.
	 *
	 * @return Query
	 */
	function select($column="*") {
		if (func_num_args() > 1) {
			$columns = func_get_args();
			$this->selectFields = array_merge($this->selectFields, $columns);
		} else {
			if (is_array($column))  {
				$this->selectFields = array_merge($this->selectFields, $column);	
			} else {
				$this->selectFields[] = $column;				
			}
		}
		return $this;
	}
	
	/**
	 * Select a column with result as given alias.
	 *
	 * @return Query
	 */
	function selectAs($column) {
		return $this->select("$column AS $alias");
	}

	/**
	 * Select a count of the given column.
	 *
	 * If no column given, defaults to <code>id</code>.
	 *
	 * @todo move to MysqlSpecific query object
	 * @return Query
	 */
	function selectCount($column="id") {
		return $this->select("COUNT($column) AS count");
	}
	
	/**
	 * Add a table name.
	 *
	 * @return Query
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
	 *
	 * @return stdClass criteria object
	 */
	static function criteria($field, $operator, $value) {
		$criteria = new stdClass;
		$criteria->field = Inflect::underscore($field);
		$criteria->operator = $operator;
		$criteria->value = $value;
		return $criteria;
	}
	
	/**
	 * Add an equals clause to the query.
	 *
	 * @return Query
	 */
	function whereEquals($key, $value) {
		$this->whereClauses[] = self::criteria($key, "=", $value);
		return $this;		
	}

	/**
	 * Add a not-equals clause to the query.
	 *
	 * @return Query
	 */	
	function whereNotEquals($key, $value) {
		$this->whereClauses[] = self::criteria($key, "!=", $value);
		return $this;		
	}

	/**
	 * Column matches given value using LIKE.
	 *
	 * @return Query
	 */	
	function whereLike($key, $value) {
		$this->whereClauses[] = self::criteria($key, "LIKE", "%$value%");
		return $this;				
	}

	/**
	 * Column is greater than given value
	 *
	 * @return Query
	 */	
	function whereGreaterThan($key, $value) {
		$this->whereClauses[] = self::criteria($key, ">", $value);
		return $this;		
	}

	/**
	 * Column is less than given value.
	 *
	 * @return Query
	 */
	function whereLessThan($key, $value) {
		$this->whereClauses[] = self::criteria($key, "<", $value);
		return $this;		
	}
	
	/**
	 * Add a range based criteria to the query.
	 *
	 * @return Query
	 */	
	function whereWithinRange($key, $upper, $lower) {
		$this->whereClauses[] = self::criteria($key, "BETWEEN $upper AND", $lower);
		return $this;
	}
	
	/**
	 * Add a range based criteria to the query.
	 *
	 * @return Query
	 */	
	function whereNotWithinRange($upper, $lower) {
		$this->whereClauses[] = self::criteria($key, "NOT BETWEEN $upper AND", $lower);
		return $this;		
	}

	/**
	 * Order the query results by the given column.
	 *
	 * @return Query
	 */	
	function orderBy($field) {
		$this->orderBy = Inflect::underscore($field);
		return $this;
	}
	
	/**
	 * Limit the query results to given range.
	 *
	 * @return Query
	 */	
	function limit($lower, $upper) {
		$this->limitLower = (integer)$lower;
		$this->limitUpper = (integer)$upper;
		return $this;
	}
	
	/**
	 * Sort query results in descending order.
	 *
	 * @return Query
	 */	
	function desc() {
		$this->orderDir = 'DESC';
		return $this;
	}
	
	/**
	 * Sort query results in ascending order.
	 *
	 * @return Query
	 */	
	function asc() {
		$this->orderDir = 'ASC';
		return $this;
	}
	
}

?>
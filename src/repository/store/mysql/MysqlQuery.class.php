<?php
/**
 * @package repository
 * @subpackage store.mysql
 */
require_once dirname(__FILE__).'/../../Query.class.php';

/**
 * Query criteria builder representing a Mysql query
 *
 * @package repository
 * @subpackage store.mysql
 */
class MysqlQuery extends Query {
	
	/**
	 * Concatenate a criteria clause for output in SQL query.
	 *
	 * @ignore
	 */
	private function mergeClauses($criteria) {
		return "{$criteria->field} {$criteria->operator} '{$criteria->value}'";
	}
	
	/**
	 * Returns a Mysql specific WHERE clause. SELECT * FROM people WHERE key='value' AND 
	 *
	 * @return string
	 */
	function toSql() {
		$sql = "";
		if (count($this->whereClauses) > 0) {
			$sql .= "WHERE ";
			$sql .= implode(' AND ', array_map(array($this, 'mergeClauses'), $this->whereClauses));
		}
		if ($this->orderBy) {
			$sql .= " ORDER BY {$this->orderBy} ";
			$sql .= ($this->orderDir) ? $this->orderDir : 'DESC';
		}
		if ($this->limitLower >= 0 && $this->limitUpper != null) {
			$sql .= " LIMIT {$this->limitLower},{$this->limitUpper}";
		}
		return $sql;
	}
	
	/**
	 * Cast the query object to a string.
	 *
	 * @return string
	 */
	function __toString() {
		$sql = "SELECT ";
		$sql .= implode(',', $this->selectFields);
		$sql .= " FROM {$this->tableName} ";
		$sql .= $this->toSql();
		return trim($sql);
	}
	
}

?>
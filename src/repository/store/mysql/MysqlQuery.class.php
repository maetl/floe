<?php
require_once dirname(__FILE__).'/../../Query.class.php';

/**
 * Query criteria builder representing a Mysql query
 */
class MysqlQuery extends Query {
	
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
		if ($this->limitLower && $this->limitUpper) {
			$sql .= " LIMIT {$this->limitLower},{$this->limitUpper}";
		}
		return $sql;
	}
	
}

?>
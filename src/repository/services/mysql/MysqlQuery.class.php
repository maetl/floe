<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package repository
 * @subpackage services.mysql
 */

require_once dirname(__FILE__).'/../../Query.class.php';

/**
 * Query criteria builder representing a Mysql query
 *
 * @package repository
 * @subpackage services.mysql
 */
class MysqlQuery extends Query {
	
	/**
	 * Concatenate a criteria clause for output in SQL query.
	 *
	 * @ignore
	 */
	private function mergeClauses($criteria) {
	    $value = ($criteria->unquoted) ? $criteria->value : "'{$criteria->value}'";
		return "{$criteria->field} {$criteria->operator} $value";
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
		if ($this->groupBy) {
			$sql .= "GROUP BY {$this->groupBy}";
			if (count($this->havingClauses) > 0) {
				$sql .= " HAVING ";
				$sql .= implode(' AND ', array_map(array($this, 'mergeClauses'), $this->havingClauses));
			}
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
		$sql .= ($this->selectFields) ? implode(',', $this->selectFields) : "*";
		$sql .= " FROM ". implode(',', $this->tableNames) ." ";
		$sql .= $this->toSql();
		return trim($sql);
	}
	
}

?>
<?php
/**
 * $Id$
 * @package repository
 * @subpackage store.mysql
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
	    if ($criteria->isJoin) {
	       return "{$criteria->field} {$criteria->operator} {$criteria->value}";
	    }
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
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
require_once dirname(__FILE__).'/../StorageIterator.class.php';

/**
 * @package repository
 * @subpackage store.mysql
 */
class MysqlIterator implements StorageIterator {

	var $_result;
	var $_current;
	var $_count;

	function MysqlIterator(&$result) {
		$this->_result = $result;
		$this->_current = 1;
		$this->_count = mysql_num_rows($this->_result);
	}
	
	function current() {
	
	}
	
	function valid() {
	
	}
	
	function key() {
	
	}
	
	function rewind() {
	
	}

	function count() {
		return $this->_count;
	}
	
	function next() {
		if ($this->_current == $this->_count) {
			$this->close();
			return null;
		}
		if (!($row = mysql_fetch_object($this->_result))) {
			$this->_current++;
			return null;
		}
		return $row;
	}
	
	function close() {
		$this->_current = 0;
	}
	
}

?>
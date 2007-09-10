<?php
// $Id: MysqlIterator.class.php 36 2007-04-01 04:12:37Z maetl_ $
/**
 * @package repository
 * @subpackage store
 */
require_once 'repository/store/StorageIterator.class.php';

/**
 * @package repository
 * @subpackage store
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
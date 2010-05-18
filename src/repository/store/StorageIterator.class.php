<?php
/**
 * $Id$
 * @package repository
 * @subpackage store
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

/**
 * Adaptor for iterating over typed record sets.
 * 
 * @package repository
 * @subpackage store
 */
class StorageIterator implements Iterator {
	private $iterator;
	private $recordType;
	
	function __construct($iterator) {
		$this->iterator = $iterator;
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
	
	function setRecordType($type) {
		$this->recordType = $type;
	}

	function next() {
		$object = $this->iterator->next();
		$record = (isset($object->type)) ? $object->type : $this->recordType;
		return new $record($object);
	}
	
	function close() {
		$this->_current = 0;
	}

}

?>
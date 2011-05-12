<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage store
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

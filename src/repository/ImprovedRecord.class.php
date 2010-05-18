<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id: Query.class.php 362 2010-03-10 00:53:10Z coretxt $
 * @package repository
 */

/**
 * A rewrite of the repository for floe 0.7 series.
 *
 * Will run alongside existing code until all tests pass. The goal is to use higher level
 * abstractions for query methods, whilst cleaning up some of the ORM functionality
 * and making the relational behavior more consistent.
 */
class ImprovedRecord {

	/**
	 * Constructor for active record objects.
	 *
	 * @param $object hash/stdClass of data to wrap, or int of record id
	 */
	function __construct($object=false) {
		// identity mapperrrrrrrrrrrr
	}

}

?>
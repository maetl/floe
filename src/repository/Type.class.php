<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 */

/**
 * A value object that represents a particular atomic concept.
 *
 * Equality of type objects is based on value rather than identity.
 *
 * @package repository
 */
interface Type {
	
	/**
	 * Convert to default string format.
	 */
	function __toString();
	
}

?>
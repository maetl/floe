<?php
/**
 * $Id$
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
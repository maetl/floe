<?php

/**
 * A value object that represents a particular atomic concept.
 *
 * Equality of type objects is based on value rather than identity.
 */
interface Type {
	
	/**
	 * Convert to default string format.
	 */
	function __toString();
	
}

?>
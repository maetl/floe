<?php
// $Id$
/**
 * @package repository
 */

/**
 * Formalizes the belongsTo/hasOne join, where one class depends on another.
 */
class DependentRelation {
	var $owner;
	var $dependent;

	function __construct($owner, $dependent) {
		$this->owner = $owner;
		$this->dependent = $dependent;
	}

}

?>
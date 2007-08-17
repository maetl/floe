<?php
// $Id: Entity.class.php 60 2007-07-02 15:00:02Z maetl_ $
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
<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package framework
 */

/**
 * Timer
 * 
 * @package framework
 */
class Timer {

	public $start;
	public $end;
	public $difference;

	public function __construct() {
		$this->start = microtime(true);
	}

	public function end() {
		$this->end = microtime(true);
		$this->difference =  $this->end - $this->start;
		return $this->difference;
	}
	
	function getMicroTime() {
		$time = microtime();
		$time = explode(' ', $time);
		return $time[1] + $time[0];
	}

	public static function avg($timers = array()) {
		$diff = 0;
		$length = count($timers);
		foreach ($timers as $timer) {
			$diff += $timer->difference;
		}
		return ($diff / $length);
	}

}

?>
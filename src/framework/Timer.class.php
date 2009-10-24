<?php
/**
 * $Id$
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
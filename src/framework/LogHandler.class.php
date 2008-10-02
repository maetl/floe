<?php
/**
 * @package framework
 */

/**
 * Interface for loggers to implement.
 * 
 * @package framework
 */
interface LogHandler {
	
	public function emit($level, $message);
	
}

?>
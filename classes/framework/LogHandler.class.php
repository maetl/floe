<?php
/**
 * @package floe
 */

/**
 * Interface for loggers to implement.
 * 
 * @package floe
 */
interface LogHandler {
	
	public function emit($level, $message);
	
}

?>
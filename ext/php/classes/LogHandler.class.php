<?php
/**
 * Interface for loggers to implement.
 */
interface LogHandler {
	
	public function emit($level, $message);
	
}

?>
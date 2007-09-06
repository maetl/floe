<?php
/**
 * Floe logger class
 * @package floe
 */
require_once 'LogHandler.class.php';
require_once 'Level.class.php';

/**
 * Implementation of an application-wide static event logger.
 * 
 * Set up the logger by adding observers that implement the LogHandler
 * interface.
 */
class EventLogger {
	
	static private $handlers = array();
	
	static function handler(LogHandler $observer) {
		self::$handlers[] = $observer;
	}

	static function debug($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Debug, $message);
		}
	}	
	
	static function info($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Info, $message);
		}
	}
	
	static function warning($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Warning, $message);
		}
	}

	static function error($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Error, $message);
		}
	}
	
	static function critical($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Critical, $message);
		}
	}

	
}


?>
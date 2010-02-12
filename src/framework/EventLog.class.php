<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package framework
 */

require_once 'LogHandler.class.php';
require_once 'Level.class.php';

/**
 * Implementation of an application-wide static event logger.
 * 
 * Set up the logger by adding observers that implement the LogHandler
 * interface.
 *
 * @package framework
 */
class EventLog {
	
	static private $handlers = array();
	
	/**
	 * Add a handler to the event logger.
	 */
	static function handler(LogHandler $observer) {
		self::$handlers[] = $observer;
	}
	
	/**
	 * Pop the topmost handler out from the logger.
	 * 
	 * This can be used when you only want to log
	 * a single part of a system. You can
	 * attach a new handler before the inspected code is
	 * executed, then pop it immediately afterwards:
	 * 
	 * <code>
	 * EventLogger::handler(new TemporaryDumpLogHandler());
	 * 
	 * $object = Factory::getObjectWeAreInterestedIn();
	 * $object->runOperation();
	 * 
	 * $handler = EventLogger::pop();
	 * </code>
	 * 
	 * This code will add a log handler for the duration of
	 * operations on $object, then pop it to the handler.
	 * 
	 * Note that calling pop() without assigning it will
	 * trigger the execution of the handlers destruct
	 * sequence. This is because the handler is being dereferenced
	 * and it is garbage collected immediately.
	 * 
	 * @return LogHandler
	 */
	static function pop() {
		return array_pop(self::$handlers);
	}
	
	/**
	 * Cleans out the entire log and destroys all handlers.
	 */
	static function clean() {
		self::$handlers = array();
	}

	/**
	 * Sends a Debug level message to the log.
	 * 
	 * Debug messages are temporary dumps and tests
	 * of code.
	 */
	static function debug($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Debug, $message);
		}
	}	

	/**
	 * Sends an Info level message to the log.
	 * 
	 * Info level messages relate to routine aspects of
	 * a system, such as loading a resource or executing
	 * a query.
	 */	
	static function info($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Info, $message);
		}
	}

	/**
	 * Sends an Warning level message to the log.
	 * 
	 * The Warning level denotes non-harmful boundary conditions
	 * or breakages of contract within the system. 
	 */		
	static function warning($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Warning, $message);
		}
	}

	/**
	 * Sends an Error level message to the log.
	 * 
	 * The Error level represents a serious system error that
	 * has caused a process to malfunction.
	 */
	static function error($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Error, $message);
		}
	}
	
	/**
	 * Sends an Critical level message to the log.
	 * 
	 * The Critical level is a serious infrastructure or
	 * service shutdown that will disrupt a range of processes.
	 */
	static function critical($message) {
		foreach(self::$handlers as $handler) {
			$handler->emit(Level::Critical, $message);
		}
	}

	
}


?>
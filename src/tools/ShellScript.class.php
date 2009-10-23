<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package tools
 */

require_once dirname(__FILE__).'/CommandIndex.class.php';
require_once dirname(__FILE__).'/ConsoleText.class.php';

/**
 * Command line shell for managing the application and running
 * build tasks. 
 *
 * @package tools
 */
class ShellScript {
	
	/**
	 * Starts up the command line shell.
	 *
	 * <p>The behavior of this process depends on ARGS passed to the
	 * calling PHP script. When no arguments are passed, the shell
	 * defaults to a waiting prompt. If arguments are passed, it attempts
	 * to find a matching task and exits after the task has executed.</p>
	 */
	static function start() {		
		if (isset($_SERVER['argv'][1])) {
			$command = $_SERVER['argv'][1];
			$arguments = array_slice($_SERVER['argv'], 2);
			$index = new CommandIndex($command, $arguments);
			$index->invoke();
		} else {
			self::loop();
		}
	}
	
	/**
	 * Starts up a primitive interactive shell, passing
	 * commands in through the std input.
	 */
	static function loop() {
		ConsoleText::startSession();
		ConsoleText::printBlocks();
		ConsoleText::printHeader();
		ConsoleText::printPrompt();
		$f = fopen('php://stdin', 'r');
		while ($cmd = fgets($f)) {
			$cmd = trim($cmd);
			if (self::isExit($cmd)) break;
			if (self::isBlank($cmd)) {
				ConsoleText::printPrompt();
				continue;
			}
			$args = split(' ', $cmd);
			$command = new CommandIndex($args[0], array_slice($args, 1));
			$command->invoke();
			ConsoleText::printPrompt();
		}
		ConsoleText::endSession();
	}
	
	/**
	 * Checks a list of possible commands that will exit out of the shell. Inspired
	 * by Python's mind boggling quit message (type 'quit' in a python shell,
	 * and see what it tells you).
	 */
	private static function isExit($input) {
		return in_array($input, array("q", "quit", "exit", "stop"));
	}
	
	/**
	 * Ignores blank input or control characters.
	 */
	private static function isBlank($input) {
		return (!$input || ord($input) == 27);
	}
	
}

?>
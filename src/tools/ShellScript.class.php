<?php
/**
 * $Id: Inflections.class.php 264 2009-03-22 06:29:51Z coretxt $
 * @package language
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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
			
			$command = explode(':', $_SERVER['argv'][1]);
			$arguments = array_slice($_SERVER['argv'], 2);
			
			echo $command[0];
			
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
			$command = new CommandIndex();
			$command->$cmd();
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
<?php
/**
 * $Id$
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

define("BR", "\n");

/**
 * Registry of commands that can be invoked by the shell.
 *
 * <p>Passes the given command on to the TaskManager if it is not provided in the index.</p>
 *
 * @package tools
 */
class ConsoleText {

	function printHeader() {
		echo self::white('Floe. '.file_get_contents(dirname(__FILE__).'/../VERSION')).BR;
	}

	function printBlocks() {
		echo self::white(file_get_contents(dirname(__FILE__).'/assets/HEADER')).BR;
	}

	function printPrompt() {
		echo self::white('# ');
	}
	
	function printText($text) {
		echo self::white($text);
	}

	function printLine($text) {
		echo self::white($text).BR;
	}

	function printHelp() {
		echo self::white(file_get_contents(dirname(__FILE__).'/assets/HELP')).BR;
	}
	
	function printListing($list) {
		$columnWidth = strlen(array_reduce(array_keys($list), array('ConsoleText', 'longestKey')));
		foreach($list as $key=>$value) {
			$width = ($columnWidth == strlen($key)) ? 1 : $columnWidth-strlen($key)+1;
			echo self::white($key . self::fillWs($width));
			echo self::white('-  '.$value).BR;
		}
	}
	
	static function longestKey($a, $b) {
		return (strlen($a) > strlen($b)) ? $a : $b;
	}
	
	static function fillWs($chars) {
		$a=''; for($i=0;$i<$chars+2;$i++) { $a.=' '; } return $a;
	}
	
	static function startSession() {
		if (self::$colorize) echo "\033[37m";
	}
	
	static function endSession() {
		if (self::$colorize) echo "\033[0m";
	}
	
	/** @ignore */
	private static $colorize = false;
	
	/**
	 * Switch the console into ANSI color text mode.
	 *
	 * <p>All console responses will be written to the screen in glorious
	 * 1970's style.</p>
	 */
	static function colorize() {
		self::$colorize = true;
	}
	
	/**
	 * Paints the output text white.
	 */
	static function white($text) {
		return $text;
		//echo (self::$colorize) ? "\033[37m".$text."\033[0m" : $text;
	}
	
	/**
	 * Paints the output text red.
	 */
	static function red($text) {
		echo (self::$colorize) ? "\033[31m".$text."\033[0m" : $text;
	}
	
}

?>
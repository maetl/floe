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

require_once dirname(__FILE__).'/ConsoleText.class.php';
require_once dirname(__FILE__).'/../language/en/Inflect.class.php';

/**
 * Manages and runs tasks from the command line interface.
 *
 * @package tools
 */
class TaskManager {
	
	private $taskIndex = array();
	
	function __construct() {
		$this->collectFromDirectory(dirname(__FILE__).'/tasks');
		if (defined('DEV_DIR')) $this->collectFromDirectory(DEV_DIR.'/tasks');
	}
	
	function runTask($command, $arguments) {
		if (array_key_exists($command, $this->taskIndex)) {
			$taskClass = $this->taskIndex[$command]->classname;
			$task = new $taskClass();
			if (method_exists($task, 'process')) {
				$task->process($arguments);
			}
			return true;
		} else {
			return false;
		}
	}
	
	function collectTask($file) {
		if (strstr($file->getPath(), '.svn')) return;
		$classfile = (string)$file;
		$filename = $file->getFileName();
		if (strstr($filename, '.class.php')) {
			$taskClass = str_replace('.class.php', '', $filename);
			$namespace = $this->getNamespace($file);
			$taskName = strtolower(str_replace(ucfirst($namespace), '', str_replace('Task', '', $taskClass)));
		} elseif (strstr($filename, '.task.php')) {
			$taskName = str_replace('.task.php', '', $filename);
			$taskClass = Inflect::toClassName($taskName).'Task';
		}
		$executable = $this->getNamespace($file).':'.$taskName;
		$taskInfo = new stdClass;
		require_once $classfile;
		$reflected = new ReflectionMethod($taskClass, 'process');
		$taskInfo->description = $this->getDescription($reflected->getDocComment());
		$taskInfo->classname = $taskClass;
		$taskInfo->path = $classfile;
		$this->taskIndex[$executable] = $taskInfo;
	}
	
	function getNamespace($file) {
		$namespace = str_replace('/', '', str_replace($this->currentPath, '', dirname($file)));
		return ($namespace == '') ? '' : $namespace;
	}
	
	function getDescription($comment) {
		preg_match("/(@description\s)(.+)(\n)/", $comment, $match);
		if (isset($match[2])) return $match[2];
	}

	private $currentPath;
	
	function collectFromDirectory($path) {
		$this->currentPath = $path;
		$directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
		foreach($directory as $file) {
			if (!$file->isDir()) $this->collectTask($file);
		}
	}
	
	function getTaskListing() {
		$listing = array();
		foreach($this->taskIndex as $cmd => $task) {
			$listing[$cmd] = $task->description;
		}
		return $listing;
	}

	
}

?>
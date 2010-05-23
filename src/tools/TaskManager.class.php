<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package tools
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
				try {
					$task->process($arguments);
				} catch(Exception $e) {
					ConsoleText::printLine("\n". get_class($e) . "! " . $e->getMessage());
					return true;
				}
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
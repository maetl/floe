<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package tools
 */

require_once dirname(__FILE__).'/TaskManager.class.php';
require_once dirname(__FILE__).'/ConsoleText.class.php';

/**
 * Provides a map of registered commands that can be executed
 * by the shell.
 *
 * @package tools
 */
class CommandIndex {
	
	private $command;
	private $arguments;
	
	function __construct($command, $arguments=false) {
		$this->command = $command;
		$this->arguments = $arguments;
	}
	
	/**
	 * Invoke the command, delegating to missing method handler
	 * if method does not exist.
	 */
	function invoke() {
		call_user_func_array(array($this, $this->command), $this->arguments);
	}
	
	function __call($method, $args) {
		$manager = new TaskManager();
		if (!$manager->runTask($this->command, $this->arguments)) {
			ConsoleText::printLine($method . " was not found");	
		}
	}
	
	function tasks() {
		$manager = new TaskManager();
		ConsoleText::printLine("Available tasks for app:");
		ConsoleText::printListing($manager->getTaskListing());
	}
	
	function t() {
		$this->tasks();
	}
	
	function ls() {
		$this->tasks();
	}	
	
	function help() {
		ConsoleText::printHelp();
	}
	
	function h() {
		$this->help();
	}
	
	function project() {
		ConsoleText::red("Not implemented yet.");
	}
	
	function p() {
		$this->project();
	}
	
}

?>
<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package server
 * @subpackage template
 */

require_once 'Twig/Autoloader.php';
Twig_Autoloader::register();

if (!defined('FloeApp_Scratch')) define('FloeApp_Scratch', sys_get_temp_dir());

if (!defined('OUTPUT_VAR')) define('OUTPUT_VAR', 'output');

class TwigTemplate implements TemplateHandler {
	private $environment;
	private $variables;

	function __construct() {
		$this->variables = array();
		$loader = new Twig_Loader_Filesystem(FloeApp_Templates);
		$this->environment = new Twig_Environment($loader, array('cache' => FloeApp_Scratch.'/cache_t'));
	}
	
	function assign($key, $value) {
		$this->variables[$key] = $value;
	}
	
	function set($key, $value) {
		$this->assign($key, $value);
	}
	
	function render($template) {
		$template = $this->environment->loadTemplate($template.'.tpl');
		return $template->render($this->variables);
	}
}
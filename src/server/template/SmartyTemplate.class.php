<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package server
 */

require_once 'Smarty/Smarty.class.php';

if (!defined('OUTPUT_VAR')) define('OUTPUT_VAR', 'output');

class SmartyTemplate implements TemplateHandler {
	private $smarty;

	function __construct() {
		$this->smarty = new Smarty();
		$this->smarty->template_dir = FloeApp_Templates;
		$this->smarty->compile_dir = TMP_DIR.'./templates';
		$this->smarty->cache_dir = TMP_DIR.'./cache';
		$this->smarty->config_dir = FloeApp_Templates.'/config';
	}
	
	/**
	 * @todo assign_by_ref instead of assign?
	 */
	function assign($key, $value) {
		$this->smarty->assign($key, $value);
	}
	
	function set($key, $value) {
		$this->smarty->assign($key, $value);		
	}
	
	function render($template) {
		$output = $this->smarty->fetch($template.'.tpl');
		if ($this->wrappedTemplate) {
			$this->assign(OUTPUT_VAR, $output);
			$output = $this->smarty->fetch($this->wrappedTemplate.'.tpl');
		}
		return $output;
	}
	
	function wrap($template) {
		$this->wrappedTemplate = $template;
	}
}
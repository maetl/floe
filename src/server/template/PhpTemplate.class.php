<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package server
 * @subpackage template
 */

require_once "TemplateHandler.class.php";

if (!defined('OUTPUT_VAR')) define('OUTPUT_VAR', 'output');

/**
 * Handler for classic PHP style templating.
 *
 * in:     $template->assign('var', 'val');
 * out:    <p><?= $var ?></p>
 */
class PhpTemplate implements TemplateHandler {
	private $variables;
	private $wrappedTemplate;

	function __construct() {
		$this->variables = array();
	}
	
	/**
	 * Assign a variable to the template
	 *
	 * @param string $key name of the variable
	 * @param mixed  $value the object to assign
	 */
	public function assign($key, $value) {
		$this->variables[$key] = $value;
	}
	
	/**
	 * synonym of assign
	 *
	 * @see Response::assign
	 */
	public function set($key, $value) {
		$this->assign($key, $value);
	}
	
	/**
	 * Wraps a main layout template around the render call.
	 *
	 * @throws Exception
	 * @param string $template path to wrapping template
	 */
	public function wrap($template) {
		$this->wrappedTemplate = $template;
	}

	/**
	 * Embed a template inside this template.
	 *
	 * <p>Can only be called from inside the template HTML scope:</p>
	 * 
	 * <pre>
	 *   &lt;form&gt;
	 *     &lt;?php $this->embed('form-fields'); ?&gt;
	 *   &lt;/form&gt;
	 * </pre>
	 *
	 * @param string $template path to template to include
	 */
	private function embed($template) {
		$this->writeTemplate($template);
	}
	
	/**
	 * Write a PHP template to the render buffer, applying any
	 * assigned variables to the current scope.
	 * 
	 * @param string $template template name
	 */
	private function writeTemplate($template) {
		extract($this->variables);
		$templatePath = TPL_DIR . "/" . $template . ".php";
		if (file_exists($templatePath)) {
			include $templatePath;
		} else {
			require_once dirname(__FILE__).'/../ResourceNotFound.class.php';
			throw new ResourceNotFound("Response template not found", $templatePath);
		}
	}

	/**
	 * Renders a template to the response buffer.
	 * 
	 * @throws Exception
	 * @param string $template path to PHP template
	 */
	public function render($template) {
		ob_start();
		$this->writeTemplate($template);
		if ($this->wrappedTemplate) {
			$this->assign(OUTPUT_VAR, ob_get_contents());
			ob_clean();
			$this->writeTemplate($this->wrappedTemplate);
		}
		$out = ob_get_contents();
		ob_clean();
		return $out;
	}
}

?>
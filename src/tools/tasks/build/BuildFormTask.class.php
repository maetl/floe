<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package tools
 * @subpackage tasks
 */

require_once dirname(__FILE__) . '/../../../repository/Record.class.php';

/**
 * @package tools
 * @subpackage tasks
 */
class BuildFormTask {
	
	private $buffer;
	
	function __construct() {
		$this->buffer = '';
	}
	
	/**
	 * @description generates an HTML form template from a model definition
	 */
	function process($modelName) {
		if (!$modelName) {
			echo "No model to build\n";
			return;
		}
		$modelFile = MOD_DIR."$modelName.model.php";
		if (file_exists($modelFile)) {
			require_once $modelFile;
			$modelClass = ucfirst($modelName);
			$model = new $modelClass;
			$properties = $model->properties();
			foreach($properties as $name=>$type) {
				$type = ucfirst($type);
				$generator = 'generate'.$type.'Field';
				$this->$generator($modelName, $name);
			}
			$this->generateForm($modelName);
		} else {
			echo "Model not found: $modelFile\n";
		}
	}
	
	function generateForm($modelName) {
		$path = TPL_DIR . Inflect::toPlural($modelName) . "/$modelName-form.php";
		file_put_contents($path, $this->buffer);
	}
	
	function generateTextField($model, $name) {
		$this->writeRowOpen();
		$this->writeLabel($name);
		$this->writeTextarea($model, $name);
		$this->writeRowClose();
	}
	
	function generateStringField($model, $name) {
		$this->writeRowOpen();
		$this->writeLabel($name);
		$this->writeInput($model, $name);
		$this->writeRowClose();	
	}
	
	function generateDateField($model, $name) {
		$this->writeRowOpen();
		$this->writeLabel($name);
		$this->writeInputDate($model, $name);
		$this->writeRowClose();	
	}
	
	function generateIntegerField($model, $name) {
		$this->writeRowOpen();
		$this->writeLabel($name);
		$this->writeInput($model, $name);
		$this->writeRowClose();	
	}
	
	function generateBooleanField($model, $name) {
		$this->writeRowOpen();
		$this->writeLabel($name);
		$this->writeCheckbox($model, $name);
		$this->writeRowClose();
	}
	
	function writeRowOpen() {
		$this->buffer .= "<div class=\"row input\">\n";
	}
	
	function writeRowClose() {
		$this->buffer .= "\n</div>\n";
	}
	
	function writeLabel($text) {
		$text = Inflect::toSentence($text);
		$this->buffer .= "\t<label>$text</label>\n\t";
	}
	
	function writeInput($model, $name) {
		$value = '<?php echo $'.$model.'->'.$name.'; ?>';
		$this->buffer .= sprintf('<input type="text" name="%s" value="%s">', $name, $value);
	}
	
	function writeInputDate($model, $name) {
		$value = '<?php echo $'.$model.'->'.$name.'->format("Y-n-d"); ?>';
		$this->buffer .= sprintf('<input type="text" name="%s" value="%s">', $name, $value);
	}
	
	function writeCheckbox($model, $name) {
		$value = '<?php if ($'.$model.'->'.$name.') echo \'checked="checked"\'; ?>';
		$this->buffer .= sprintf('<input type="checkbox" name="%s" value="1" %s >', $name, $value);
	}
	
	function writeTextarea($model, $name) {
		$value = '<?php echo $'.$model.'->'.$name.'; ?>';
		$this->buffer .= sprintf('<textarea name="%s">%s</textarea>', $name, $value);
	}
	
}

?>
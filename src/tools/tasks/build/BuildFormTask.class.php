<?php
/**
 * $Id: TaskManager.class.php 274 2009-04-10 04:00:06Z coretxt $
 * @package tools
 * @subpackage tasks
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

require_once dirname(__FILE__) . '/../../../repository/store/StorageAdaptor.class.php';
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
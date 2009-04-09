<?php
/**
 * @package server
 * @subpackage controllers
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
require_once dirname(__FILE__) .'/../SessionState.class.php';

/**
 * Base controller implementation.
 * 
 * Provides request and response state to the controller subclass.
 * 
 * @package server
 * @subpackage controllers
 */
class IdentityController {
	protected $request;
	protected $response;
	protected $session;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
		$this->session = SessionState::instance();
		$this->importModel();
		$this->assignDefaults();
	}

	/**
	 * The identity of this controller.
	 */
	public function identity() {
		return strtolower(str_replace('Controller', '', get_class($this)));
	}
	
	/**
	 * Imports a model file based on controller identity.
	 * 
	 * Should this be phased out in favor of autoload?
	 */
	private function importModel() {
		$modelPath = MOD_DIR . '/' . $this->identity() . '.model.php';
		if (file_exists($modelPath)) require_once $modelPath;
	}
	
	/**
	 * Provide set of default properties & metadata to
	 * the response.
	 */
	private function assignDefaults() {
		$this->response->assign('controller', $this->identity());
	}

}


?>
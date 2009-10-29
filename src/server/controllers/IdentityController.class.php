<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package server
 * @subpackage controllers
 */

/**#@+
 * Required dependency.
 */
require_once dirname(__FILE__) .'/../SessionState.class.php';
/**#@-*/

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
<?php
/**
 * @package server
 * @subpackage controllers
 */

/**
 * Base controller implementation.
 * 
 * Provides request and response state to the controller subclass.
 * 
 * @package server
 * @subpackage controllers
 */
class BaseController {
	protected $request;
	protected $response;
	protected $session;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
		$this->session = SessionState::instance();
		$this->importModel();
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
	 * The identity of this controller.
	 */
	public function identity() {
		return strtolower(str_replace('Controller', '', get_class($this)));
	}

}


?>
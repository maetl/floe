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

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
	}

}


?>
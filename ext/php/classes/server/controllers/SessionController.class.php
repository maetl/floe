<?php
/**
 * @package server
 * @subpackage controllers
 */
require_once 'server/SessionState.class.php';

/** 
 * Provides session state to the controller subclass.
 * 
 * @package server
 * @subpackage controllers
 */
class SessionController {
	protected $request;
	protected $response;
	protected $session;

	public function __construct($request, $response) {
		$this->request = $request;
		$this->response = $response;
		$this->session = SessionState::instance();
	}

}


?>
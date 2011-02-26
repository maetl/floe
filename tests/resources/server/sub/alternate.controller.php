<?php

class AlternateController extends BaseController {

	function index() {
		$this->response->write("index");
	}
	
	function action() {
		$this->response->write("alternate");
	}
	
}

?>
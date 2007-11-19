<?php

class SampleController extends BaseController {
	
	function before() {
		$this->response->write("a;");
	}
	
	function index() {
		$this->response->write("b;");
	}
	
	function after() {
		$this->response->write("c;");
	}
	
}

?>
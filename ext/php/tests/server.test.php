<?php
require_once "simpletest/autorun.php";
require_once "simpletest/mock_objects.php";

require_once "classes/server/Membrane.class.php";
require_once "classes/server/receptors/IdentityDispatcher.class.php";
require_once "classes/server/controllers/BaseController.class.php";


class ControllerDispatchTest extends UnitTestCase {
	
	function setUp() {
		define('CTR_DIR', dirname(__FILE__).'/resources/server/');
		$this->getRequest = $_GET;
		$this->postRequest = $_POST;
		$this->serverEnv = $_SERVER;
	}
	
	function tearDown() {
		$_GET = $this->getRequest;
		$_POST = $this->postRequest;
		$_SERVER = $this->serverEnv;
	}

	function mockMethodVerb($method) {
		$_SERVER['REQUEST_METHOD'] = $method;
	}
	
	function mockUri($path) {
		$_SERVER['REQUEST_URI'] = $path;
	}

	
	function testIndexRouteInvoked() {
		$this->mockUri('/');
		$request = new Request();
		$response = new Response();

		$this->assertFalse(class_exists('IndexController'));
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('IndexController'));
	}	
	
	function testSampleRouteInvoked() {
		$this->mockUri('/sample');
		$request = new Request();
		$response = new Response();

		$this->assertFalse(class_exists('SampleController'));
		$this->assertFalse($response->body());
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('SampleController'));
		$this->assertEqual("a;b;c;", $response->body());
	}

	function testMissingResourceException() {
		$this->mockUri('/missing/controller');
		$request = new Request();
		$response = new Response();
		
		$dispatcher = new IdentityDispatcher();
		$this->expectException();
		$dispatcher->run($request, $response);
	}
	
}

?>
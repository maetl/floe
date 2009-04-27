<?php
require_once "simpletest/autorun.php";
require_once "simpletest/mock_objects.php";

require_once dirname(__FILE__).'/../../src/server/Membrane.class.php';
require_once dirname(__FILE__).'/../../src/server/receptors/IdentityDispatcher.class.php';
require_once dirname(__FILE__).'/../../src/server/controllers/BaseController.class.php';


class ServerTestCase extends UnitTestCase {
	
	function setUp() {
		if (!defined('CTR_DIR')) define('CTR_DIR', dirname(__FILE__).'/resources/server/');
		if (!defined('MOD_DIR')) define('MOD_DIR', dirname(__FILE__).'/resources/models/');
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
	
}


class ControllerDispatchTest extends ServerTestCase {
	
	function testIndexRouteInvoked() {
		$this->mockMethodVerb('GET');
		$this->mockUri('/');
		$request = new Request();
		$response = new Response();

		$this->assertFalse(class_exists('IndexController'));
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('IndexController'));
	}	
	
	function testSampleRouteInvoked() {
		$this->mockMethodVerb('GET');		
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
	
	function testSubFolderBaseRouteInvoked() {
		$this->mockMethodVerb('GET');		
		$this->mockUri('/sub');
		$request = new Request();
		$response = new Response();

		$this->assertFalse(class_exists('SubController'));
		$this->assertFalse($response->body());
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('SubController'));
		$this->assertEqual("sub", $response->body());
	}
	
	function testSubFolderAlternateRouteInvoked() {
		$this->mockMethodVerb('GET');		
		$this->mockUri('/sub/alternate');
		$request = new Request();
		$response = new Response();

		$this->assertFalse(class_exists('AlternateController'));
		$this->assertFalse($response->body());
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('AlternateController'));
		$this->assertEqual("index", $response->body());
	}
	
	function testSubFolderAlternateRouteMethodInvoked() {
		$this->mockMethodVerb('GET');		
		$this->mockUri('/sub/alternate/action');
		$request = new Request();
		$response = new Response();

		$this->assertFalse($response->body());
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('AlternateController'));
		$this->assertEqual("alternate", $response->body());
	}
	
	function testMissingResourceException() {
		$this->mockMethodVerb('GET');		
		$this->mockUri('/missing/controller');
		$request = new Request();
		$response = new Response();
		
		$dispatcher = new IdentityDispatcher();
		$this->expectException();
		$dispatcher->run($request, $response);
	}
	
	function testBindAllToIndexNoException() {
		$this->mockMethodVerb('GET');		
		$this->mockUri('/missing/controller');
		$request = new Request();
		$response = new Response();
		
		define('BindMissingDefault', true);
		$dispatcher = new IdentityDispatcher();
		$dispatcher->run($request, $response);
		$this->assertTrue(class_exists('IndexController'));
	}
	
}



?>
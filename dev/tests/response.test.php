<?php
require_once 'simpletest/autorun.php';

/**
 * Note: some of these tests won't work when the Response is triggered via CLI
 * rather than via Apache.
 */
require_once dirname(__FILE__).'/../../src/server/Response.class.php';

if (!defined('TPL_DIR')) define('TPL_DIR', dirname(__FILE__).'/resources/templates/');

class ResponseTest extends UnitTestCase {
	
	function assertResponseHeader($header) {
		if (!SimpleReporter::inCli()) $this->assertTrue(array_key_exists($header, apache_response_headers()));
	}
	
	function testHeadersAlreadySent() {
		$response = new Response();
		$response->header("X-Non-Authenticate", "Negotiate");
		$this->assertResponseHeader("X-Non-Authenticate");
		$this->expectException('Exception');
		$response->out();
	}
	
	function testOutputBuffer() {
		$response = new Response();
		$response->write("<h1>Hello</h1>");
		$response->write("<p>World</p>");
		$this->assertPattern("/<h1>Hello<\/h1><p>World<\/p>/", $response->body());
	}
	
	function testTemplateRenderPlain() {
		$response = new Response();
		$response->render('hello');
		$this->assertPattern("/<h1>Hello World<\/h1>/", $response->body());
	}
	
	function testTemplateRenderVars() {
		$response = new Response();
		$response->assign('booleanVar', true);
		$response->assign('integerVar', 999);
		$response->assign('stringVar', 'sesame');
		$response->assign('arrayVar', array("green","eggs","boiled","ham"));
		$object = new stdClass; $object->var = "value";
		$response->assign('objectVar', $object);
		$response->render('vars');
		$this->assertPattern("/<li>booleanVar: 1<\/li>/", $response->body());
		$this->assertPattern("/<li>integerVar: 999<\/li>/", $response->body());
		$this->assertPattern("/<li>stringVar: sesame<\/li>/", $response->body());
		$this->assertPattern("/<li>arrayVar: green,eggs,boiled,ham<\/li>/", $response->body());
		$this->assertPattern("/<li>objectVar: value<\/li>/", $response->body());
	}
	
	function testTemplateRenderWrappedLayout() {
		$response = new Response();
		$response->wrap('wrapper');
		$response->assign('foo', 'bar');
		$response->render('hello');
		$this->assertPattern("/<span>bar<\/span>/", $response->body());
		$this->assertPattern("/<div><h1>Hello World<\/h1><\/div>/", $response->body());
	}
	
}

?>
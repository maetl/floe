<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/server/Response.class.php';

class ResponseTest extends UnitTestCase {
	
	function assertResponseHeader($header) {
		if (!SimpleReporter::inCli()) $this->assertTrue(array_key_exists($header, apache_response_headers()));
	}
	
	function testHeadersAlreadySent() {
		//$this->assertResponseHeader("Content-Type");
		$response = new Response();
		$response->header("X-Non-Authenticate", "Negotiate");
		//$this->expectException();
		$response->out();
	}
	
	function testOutputBuffer() {
		$response = new Response();
		$response->write("<h1>Hello</h1>");
		$response->write("<p>World</p>");
		$this->expectException();
		$response->out();
		$content = ob_get_contents();
		$this->assertPattern("/<h1>Hello<\/h1><p>World<\/p>/", $content);
	}
	
}

?>
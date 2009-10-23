<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/repository/services/http/HttpGateway.class.php';


class HttpServiceTest extends UnitTestCase {
	
	const HOST = 'http://conformity.sourceforge.net';

	function testGetRequest() {
		$client = new HttpGateway();
		$client->get(self::HOST.'/basic/get');
		$this->assertEqual(200, $client->getStatus());
		$this->assertPattern("/CANHAZHTTPGET/", $client->getBody());
	}
	
	function testPostRequest() {
		$client = new HttpGateway();
		$client->post(self::HOST.'/basic/post', array("greeting"=>"Hello", "from"=>"HttpGateway"));
		$this->assertEqual(200, $client->getStatus());
		$this->assertPattern("/Hello back/", $client->getBody());
	}
	
	function testHeadRequest() {
		$client = new HttpGateway();
		$client->header("X-Requested-Square", 4);
		$client->head(self::HOST.'/basic/head');
		$this->assertEqual(200, $client->getStatus());
		$this->assertEqual("True", $client->getHeader("X-Requested-By-Head"));
		$this->assertEqual(4*4, $client->getHeader("X-Requested-Result"));
	}
	
	function testContentNegotiation() {
		$client = new HttpGateway();
		$client->header("Accept", "application/xml");
		$client->get(self::HOST.'/basic/content');
		$this->assertEqual(200, $client->getStatus());
		$this->assertPattern("/<title>Hello World<\/title>/", $client->getBody());
		$client->header("Accept", "text/javascript");
		$client->get(self::HOST.'/basic/content');
		$this->assertEqual(200, $client->getStatus());
		$this->assertPattern("/{message:\"Hello World\"}/", $client->getBody());
	}
	
	function testBasicAuthentication() {
		$client = new HttpGateway();
		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEqual(401, $client->getStatus());
		$this->assertPattern("/You are not authorized/", $client->getBody());
		$client->authorize("random", "random");
		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEqual(401, $client->getStatus());
		$this->assertPattern("/Invalid username and password/", $client->getBody());
		$client->authorize("username", "password");
		$client->get(self::HOST.'/basic/auth/basic');
		$this->assertEqual(200, $client->getStatus());
		$this->assertPattern("/You are logged in/", $client->getBody());
	}
	
	function testNotFoundError() {
		$client = new HttpGateway();
		$client->get(self::HOST.'/basic/errors/missing');
		$this->assertEqual(404, $client->getStatus());
		$this->assertPattern("/Resource Not Found/", $client->getBody());
	}
	
	function testInternalServerError() {
		$client = new HttpGateway();
		$client->get(self::HOST.'/basic/errors/crash');
		$this->assertEqual(500, $client->getStatus());
		$this->assertPattern("/The Server Exploded/", $client->getBody());
	}
	
	function testFailOnNetworkTimeout() {
		$client = new HttpGateway();
		$client->setTimeout(1);
		$this->expectException();
		$client->get(self::HOST.'/basic/errors/timeout');
	}
	
	function testFailOnClientError() {
		$client = new HttpGateway();
		$client->failOnError();
		$this->expectException(new Exception("Client Error", 404));
		$client->get(self::HOST.'/basic/errors/missing');
		$this->assertEqual(404, $client->getStatus());
		$this->assertPattern("/The Server Exploded/", $client->getBody());
	}
	
	function testFailOnServerError() {
		$client = new HttpGateway();
		$client->failOnError();
		$this->expectException(new Exception("Server Error", 500));
		$client->get(self::HOST.'/basic/errors/crash');
		$this->assertEqual(404, $client->getStatus());
		$this->assertPattern("/The Server Exploded/", $client->getBody());		
	}
	
}

?>
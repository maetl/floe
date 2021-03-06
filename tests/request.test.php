<?php
require_once "simpletest/autorun.php";
require_once "simpletest/mock_objects.php";

require_once dirname(__FILE__).'/../src/server/Request.class.php';
require_once dirname(__FILE__).'/../src/server/HttpEnvelope.class.php';

Mock::generate("HttpEnvelope");
Mock::generatePartial("HttpEnvelope", "StubHttpEnvelope", array('header'));

class RequestTest extends UnitTestCase {
	protected $getRequest;
	protected $postRequest;
	protected $serverEnv;
	
	function setUp() {
		$this->getRequest = $_GET;
		$this->postRequest = $_POST;
		$this->serverEnv = $_SERVER;
		if (SimpleReporter::inCli()) {
			$this->mockMethodVerb('GET');
			$this->mockUri('/');
		}
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
	
	function testGetParameterAccess() {
		$_GET = array("q"=>"welcome to the machine");
		$this->mockMethodVerb('GET');
		
		$request = new Request();
		$this->assertTrue($request->isGet());
		$this->assertEqual('welcome to the machine', $request->getParameter('q'));
		$this->assertEqual('welcome to the machine', $request->q);
	}
	
	function testPostParameterAccess() {
		$_POST = array("hello"=>"world", "foo"=>"bar");
		$this->mockMethodVerb('POST');
		$http = new MockHttpEnvelope();
		$http->setReturnValue('header', 'application/x-www-form-urlencoded', array('Content-Type'));
		
		$request = new Request($http);
		$this->assertTrue($request->isPost());
		$this->assertEqual('world', $request->postParameter('hello'));
		$this->assertEqual('world', $request->hello);
		$this->assertEqual('bar', $request->postParameter('foo'));
		$this->assertEqual('bar', $request->foo);
		$this->assertEqual("hello=world&foo=bar", $request->entityBody());
	}
	
	function testArrayAsPostParameter() {
		$_POST = array("hello"=>array("hello", "world"));
		$this->mockMethodVerb('POST');
		$http = new MockHttpEnvelope();
		$http->setReturnValue('header', 'application/x-www-form-urlencoded', array('Content-Type'));
		
		$request = new Request($http);
		$this->assertEqual(array("hello", "world"), $request->hello);
		$this->assertEqual("hello", $request->hello[0]);
	}
	
	function testNonExistingAndEmptyPostParametersReturned() {
		$_POST = array("hello"=>"world", "foo"=>"", "bar"=>0, "boo"=>false, "hoo"=>true);
		$this->mockMethodVerb('POST');
		$http = new MockHttpEnvelope();
		$http->setReturnValue('header', 'application/x-www-form-urlencoded', array('Content-Type'));
		
		$request = new Request($http);
		$this->assertEqual("world", $request->hello);
		$this->assertFalse($request->world);
		$this->assertEqual(array("hello"=>"world", "foo"=>"", "bar"=>0, "boo"=>false, "hoo"=>true), $request->posted());
	}
	
	function testUriPathComponents() {
		$this->mockUri("/controller/action/id");

		$request = new Request();
		$this->assertEqual("id", $request->uri->identity());
		$this->assertEqual(3, count($request->uri->segments()));
	}
	
	function testEnvelopeSupportsPlainHeaders() {
		$http = new MockHttpEnvelope();
		$http->setReturnValue('header', 'no-cache', array('Pragma'));
		$http->setReturnValue('header', 'http://example.org/action', array('Referer'));

		$request = new Request($http);
		$this->assertEqual('no-cache', $request->header('Pragma'));
		$this->assertEqual('http://example.org/action', $request->referer());
	}
	
	function testEnvelopeSupportsUserAgent() {
		$http = new MockHttpEnvelope();
		$ua = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1.5) Gecko/20070713 Firefox/2.0.0.5';
		$http->setReturnValue('header', $ua, array('User-Agent'));

		$request = new Request($http);
		$browser = $request->browser();
		$this->assertEqual(new UserAgent($ua), $browser);
		$this->assertEqual('Mac', $browser->platform);
		$this->assertEqual('Gecko', $browser->engine);
		$this->assertEqual('Firefox', $browser->product);
		$this->assertEqual('Mozilla', $browser->vendor);
	}
	
	function testEnvelopeSupportsAcceptLanguage() {
		$http = new StubHttpEnvelope();
		$lang = "en-us";
		$http->setReturnValue('header', $lang, array('Accept-Language'));
		
		$request = new Request($http);
		$language = $request->language();
		$precedence = $request->languages();
		$this->assertEqual('en-us', $language);
		$this->assertEqual(array('en-us'=>1), $precedence);
	}
	
	function testEnvelopeSupportsAcceptLanguagePrecedence() {
		$http = new StubHttpEnvelope();
		$lang = "de-de;q=1,en-us;q=0.8,fr;q=0.6";
		$http->setReturnValue('header', $lang, array('Accept-Language'));
		
		$request = new Request($http);
		$language = $request->language();
		$precedence = $request->languages();
		$this->assertEqual('de-de', $language);
		$this->assertEqual(array('de-de'=>1,'en-us'=>0.8,'fr'=>0.6), $precedence);
	}
}

?>
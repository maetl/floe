<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/repository/store/redis/RedisConnection.class.php';

class RedisConnectionTest extends UnitTestCase {
	
	function skip() {
		$this->skipUnless(true);
	}
	
	function testIdempotentConnectionToLocalhost() {
		$connection = new RedisConnection('127.0.0.1');
		$this->assertTrue($connection->connect());
		$this->assertTrue($connection->connect());
		$connection->disconnect();
	}
	
	function testWriteConnectionToLocalhost() {
		$connection = new RedisConnection('127.0.0.1');
		$this->assertTrue($connection->connect());
		$connection->write("PING");
		$this->assertEqual("PONG", $connection->read());
		$connection->disconnect();
	}
	
	//function testBadHostThrowsResourceError() {
		//$connection = new RedisConnection('bad.host');
		//$this->expectException();		
	//}
	
	function testEchoToConnection() {
		$connection = new RedisConnection('127.0.0.1');
		$this->assertTrue($connection->connect());
		$connection->write("ECHO 6\r\nBerlin\r\n");
		$this->assertEqual("Berlin", $connection->read());
		$connection->write("ECHO 6\r\nLondon\r\n");
		$this->assertEqual("London", $connection->read());
		$connection->disconnect();		
	}
	
	function testGetAndSetKeyValueAsString() {
		$connection = new RedisConnection('127.0.0.1');
		$connection->write("SET city 6\r\nBerlin\r\n");
		$this->assertEqual("OK", $connection->read());
		
		$connection->write("GET city");
		$this->assertEqual("Berlin", $connection->read());
		$connection->disconnect();
	}
	
}

?>
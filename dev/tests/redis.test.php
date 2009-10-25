<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../../src/repository/store/redis/RedisConnection.class.php';

/**
 * Install and start the Redis server:
 *
 * $> wget http://redis.googlecode.com/files/redis-1.02.tar.gz
 * $> tar -xzf redis-1.02.tar.gz
 * $> cd redis-1.02
 * $> make
 * $> ./redis-server
 */
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
	
	function testBadHostThrowsResourceError() {
		//$connection = new RedisConnection('bad.host');
		//$this->expectException();		
	}
	
	function testBasicDataSetValues() {
		$connection = new RedisConnection('127.0.0.1');
		$this->assertTrue($connection->connect());
		$connection->write("Testing the socket");
		//$this->dump($connection->read());
		$connection->disconnect();
	}
	
}

?>
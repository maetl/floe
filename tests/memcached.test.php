<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../src/repository/services/memcached/MemcachedAdaptor.class.php';

/**
 * Cache Rules Everything Around Me (C.R.E.A.M), Get the Memory, Hit and Hit and Miss Yo...
 *
 * Start the server with:
 * $ memcached -d -m 1024 -l localhost -p 11211
 */
class MemcachedExtensionTest extends UnitTestCase {

	private $cache;

	function skip() {
		$this->skipUnless(extension_loaded('memcache'));
	}
	
	function setUp() {
		$env = new stdClass();
		$env->MEMCACHED_HOST = 'localhost';
		$env->MEMCACHED_PORT = '11211';
		$this->cache = new MemcachedAdaptor(new MemcachedConnection($env));
	}

	function testCanStoreTemporaryData() {
		$person = new stdClass();
		$person->id = 1;
		$person->firstname = "Barack";
		$person->lastname = "Obama";
		
		$this->cache->write('person', 1, $person);
		$cached = $this->cache->read('person', 1);
		
		$this->assertEqual($person->firstname, $cached->firstname);
		$this->assertEqual($person->lastname, $cached->lastname);
		
		$this->cache->delete('person', 1);
		$this->assertFalse($this->cache->read('person', 1));
	}
	
	function testCacheExpiry() {
		$person = new stdClass();
		$person->firstname = "John";
		$person->lastname = "McCain";	
		
		$this->cache->write('person', 2, $person, 1);
		sleep(3);
		$cached = $this->cache->read('person', 2);
		
		//$this->assertTrue($this->cache->isExpired());
		
		$this->assertEqual($person->firstname, $cached->firstname);
		$this->assertEqual($person->lastname, $cached->lastname);		
		
		$this->cache->delete('person', 2);
		$this->assertFalse($this->cache->read('person', 2));
	}

}

?>
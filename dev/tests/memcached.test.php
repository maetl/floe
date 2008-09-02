<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/repository/store/memcached/MemcachedAdaptor.class.php';

class MemcachedExtensionTest extends UnitTestCase {

	function testCanStoreTemporaryData() {
		$env = new stdClass();
		$env->MEMCACHED_HOST = 'localhost';
		$env->MEMCACHED_PORT = '11211';
		$memcached = new MemcachedAdaptor(new MemcachedConnection($env));
		
		$person = new stdClass();
		$person->firstname = "Barack";
		$person->lastname = "Obama";
		
		$memcached->store('person', 1, $person);
		$cached = $memcached->fetch('person', 1);
		
		$this->assertEqual($person, $cached);
		
		$memcached->delete('person', 1);
		$this->assertFalse($memcached->fetch('person', 1));
	}

}



?>
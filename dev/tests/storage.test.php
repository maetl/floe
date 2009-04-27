<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/repository/store/StorageAdaptor.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
}

class StorageAdaptorSingletonTest extends UnitTestCase {
	
	function testMysqlAdaptorInstance() {
		$adaptor = StorageAdaptor::instance();
		$this->assertIsA($adaptor, 'StorageAdaptor');
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
	}
	
	function testStorageAdaptorAPI() {
		$adaptor = StorageAdaptor::instance();
	}
	
}

?>
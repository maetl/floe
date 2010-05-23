<?php
require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/../../src/repository/Storage.class.php';

if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'floe_test');
	define('DB_USER', 'default');
	define('DB_PASS', 'launch');
}

class StorageAdaptorSingletonTest extends UnitTestCase {
	
	function testDefaultAdaptorInstance() {
		$s = Storage::init();
		$this->assertIsA($s, 'Storage');
		$this->assertIdentical($s, Storage::init());
		$this->assertIdentical($s, Storage::init());
	}
	
	function testSupportedAdaptorInstances() {
		$m = Storage::adaptor('Mysql');
		$this->assertIsA($m, 'MysqlAdaptor');
		$s = Storage::adaptor('Sqlite');
		$this->assertIsA($s, 'SqliteAdaptor');
	}
	
}

?>
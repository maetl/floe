<?php
require_once 'simpletest/autorun.php';
require_once 'Floe.class.php';
require_once 'repository/store/StorageAdaptor.class.php';

class StorageAdaptorSingletonTest extends UnitTestCase {
	
	function testMysqlAdaptorInstance() {
		$adaptor = StorageAdaptor::instance();
		$this->assertIsA($adaptor, 'MysqlAdaptor');
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
	}
	
}


?>
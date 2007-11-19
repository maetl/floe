<?php
require_once 'simpletest/autorun.php';
require_once 'classes/repository/store/StorageAdaptor.class.php';

class StorageAdaptorSingletonTest extends UnitTestCase {
	
	function testMysqlAdaptorInstance() {
		$adaptor = StorageAdaptor::instance();
		$this->assertIsA($adaptor, 'MysqlGateway');
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
		$this->assertIdentical($adaptor, StorageAdaptor::instance());
	}
	
}


?>
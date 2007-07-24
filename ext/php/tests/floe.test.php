<?php
require_once "Floe.class.php";

class FloeTest extends UnitTestCase {

	function testFloeImport() {
		$this->assertEqual("core", Floe::namespace());
	}

}

?>
<?php
/**
 * @package floe
 */

/**
 * Static class to load tests with default data.
 * 
 * @package floe
 * @todo flesh out Zend/PEAR import statements, or remove altogether
 */
class Floe {
	
	static function inCli() {
		return (php_sapi_name() == 'cli');
	}

	static function namespace($default="core") {
		return $default;
	}
	
	static function namespaceFormat($default="core") {
		return $default;
	}
	
	static function defaultTestUri() {
		return "http://floe/ext/php/tests/web/";
	}

	static function defaultTestDb() {
		return array("localhost", "floe_test", "default", "launch");
	}
	
	static function import() {
		
	}
	
	static function Zend_Import() {
	
	}

	static function importPear($import) {
	
	}
	
}

?>
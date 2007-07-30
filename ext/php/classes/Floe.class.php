<?php
/**
 * Stupid package registry stuff
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
	
	static function import() {
		
	}
	
	static function Zend_Import() {
	
	}

	static function importPear($import) {
	
	}
	
}

?>
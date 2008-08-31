<?php
/**
 * Package based class loader, used in lieu of
 * having real namespaces.
 *
 * @package framework
 */
class Package {
	
	/**
	 * Load a Floe class specified by package path.
	 *
	 * @param $class
	 */
	public static function import($class) {
		require_once LIB_DIR . '/floe' . str_replace(".", "/", $class) . ".class.php";
	}
	
}

?>
<?php
/**
 * $Id: ResourceNotFound.class.php 179 2008-09-02 11:02:38Z coretxt $
 * @package framework
 */
if (!defined('LIB_DIR')) define('LIB_DIR', dirname(__FILE__).'/../../');

/**
 * Package based class loader, used in lieu of
 * having real namespaces.
 *
 * @package framework
 */
class Package {
	
	/**
	 * Load a library class specified by package path.
	 *
	 * @param $class
	 */
	public static function import($class) {
		require_once LIB_DIR . str_replace(".", "/", $class) . ".class.php";
	}
	
}

?>
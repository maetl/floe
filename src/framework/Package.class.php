<?php
/**
 * $Id$
 * @deprecated
 * @package framework
 */
if (!defined('LIB_DIR')) define('LIB_DIR', dirname(__FILE__).'/../../');

/**
 * Load replacement importer {@link Using}
 */
require_once dirname(__FILE__)."/Using.class.php";

/**
 * Package based class loader, used in lieu of
 * having real namespaces.
 *
 * @deprecated
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
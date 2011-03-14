<?php
/**
 * @deprecated
 * @package framework
 */
if (!defined('LIB_DIR')) define('LIB_DIR', dirname(__FILE__).'/../../');

/**
 * Load replacement importer {@link Using}
 */
require_once dirname(__FILE__)."/Using.class.php";

/**
 * Derives a package schema for the application load path.
 *
 * @deprecated
 * @package framework
 */
class Package {
	
	private static $searchPaths = array('server','server/receptors', 'server/controllers');
	
	/**
	 * Load a library class specified by package path.
	 *
	 * @param $class
	 */
	public static function import($path) {
		Using::import($path);
	}
	
	/**
	 * List of locations in the package search path.
	 */
	public static function locations() {
		return self::$searchPaths;
	}
	
}

?>
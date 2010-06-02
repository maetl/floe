<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id: Using.class.php 352 2010-02-15 14:07:51Z coretxt $
 * @package framework
 */
if (!defined('LIB_DIR')) define('LIB_DIR', dirname(__FILE__).'/../../');
if (!defined('APP_DIR')) define('APP_DIR', dirname(__FILE__).'/../../../app');

/**
 * Class importer for PHP 5.2.
 *
 * <p>Replaces {@link Package::import()} and manual require_once statements at the top of files.</p>
 *
 * @todo add hooks into autoloader
 * @todo pluggable file naming schemes
 * @package framework
 */
class Using {
	
	private static $searchPaths = array();
	
	/**
	 * Load a library class specified by package path.
	 *
	 * @param $class
	 */
	public static function import($class) {
		require_once LIB_DIR . str_replace(".", "/", $class) . ".class.php";
	}
	
	/**
	 * Load a model class.
	 *
	 * @param $class
	 */
	public static function path($class) {
		require_once MOD_DIR . str_replace(".", "/", $class) . ".model.php";
	}
	
	/**
	 * Load a finder class.
	 *
	 * @param $class
	 */
	public static function searchPaths() {
		return self::$searchPaths;
	}
	
}

?>
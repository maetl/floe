<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package floe
 */

require_once 'framework/Package.class.php';
require_once 'framework/Using.class.php';
require_once 'server/Membrane.class.php';

/**
 * Load the application defaults.
 * @package floe
 */
class FloeApp {
	
	const VERSION = '0.6.6';
	
	/**
	 * Load the application config.
	 *
	 * Optional parameters points to where class packages are installed,
	 * otherwise defaults to the local "/../app/" directory.
	 *
	 * @param $app path to app directory
	 */
	public static function register($app = false) {
		if (!$app) $app = dirname(__FILE__).'/../app/';
		$floePath = dirname(__FILE__);
		
		try {
			if (!require_once $app.'/config.php')
				throw new Exception("No application configured.");

			if (!defined('FloeApp_Controllers')) define('FloeApp_Controllers', $app.'/controllers');
			if (!defined('FloeApp_Models')) define('FloeApp_Models', $app.'/models');
			if (!defined('FloeApp_Locales')) define('FloeApp_Locales', $app.'/locales');
			if (!defined('FloeApp_Templates')) define('FloeApp_Templates', $app.'/templates');
			if (!defined('FloeApp_Scratch')) define('FloeApp_Scratch', sys_get_temp_dir());

			foreach(array($floePath.'/server/receptors/',
						  $floePath.'/server/controllers/',
						  $floePath.'/repository/',
			 			  FloeApp_Controllers,
			 			  FloeApp_Models) as $classPath) {
				set_include_path(get_include_path().PATH_SEPARATOR.$classPath);
			}

			spl_autoload_extensions('.class.php, .php, .model.php, .inc');

			spl_autoload_register();
			spl_autoload_register("FloeApp_Helpers_Autoload");

			if (!require_once $app.'/controllers/application.controller.php')
				throw new Exception("No application configured.");	
		
		} catch(Exception $error) {
			$response = new Response();
			$response->raise($error);
			$response->out();
			exit;
		}
	}
}

/**
 * Structured search for application specific helper objects.
 * Hook for spl_autoload_register
 * @todo flexible naming conventions
 */
function FloeApp_Helpers_Autoload($className) {
	if (strstr($className, 'Finder')) {
		$classPath = str_replace('finder', '.finder.php', strtolower($className));
		require_once FloeApp_Models."/$classPath";
	} elseif (file_exists(FloeApp_Models."/$className.model.php")) {
		require_once FloeApp_Models."/$className.model.php";
	}
}

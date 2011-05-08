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

require_once 'framework/Environment.class.php';

/**
 * Load the application defaults.
 * @package floe
 */
class Floe {
	
	const VERSION = '0.6.6';
	const APP_DIR = 'app';
	const DEV_DIR = 'dev';
	const LIB_DIR = 'lib';
	const TMP_DIR = 'tmp';
	const WEB_DIR = 'www';
	const CTR_DIR = 'controllers';
	const MOD_DIR = 'models';
	const TPL_DIR = 'templates';
	
	/**
	 * Register the application environment and register the autoloader.
	 *
	 * @param string $path path to the application root.
	 */
	public static function init($path) {
		
		Environment::setClassPath(dirname(__FILE__));
		Environment::setClassPath($path);

		Environment::buildManifest();

		spl_autoload_register(array('Environment', 'loadClass'));
	}
}
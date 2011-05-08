<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package framework
 */

class Environment {

	private static $configFile = 'config.php';

	private static $manifestFile = 'floe.manifest.php';

	private static $manifest = array();
	
	private static $allowedFilesPattern = '/\.class\.php$/';

	private static $cachePath;
	
	public static function setConfigFile($filename) {
		self::$configFile = $filename;
	}
	
	public static function getCachePath() {
		if (!isset(self::$cachePath)) self::$cachePath = sys_get_temp_dir();
		return self::$cachePath;
	}
	
	public static function setCachePath($path) {
		self::$cachePath = $path;
	}
	
	public static function getManifestPath() {
		return self::getCachePath() . DIRECTORY_SEPARATOR . self::$manifestFile;
	}
	
	private static $classpath = array();
	
	public static function setClassPath($path) {
		if (is_dir($path)) {
			self::$classpath[] = $path;
		} else {
			throw new Exception("Directory not found: $path");
		}
	}
	
	public static function loadClass($class) {
		if (array_key_exists($class, self::$manifest) && file_exists(self::$manifest[$class])) {
			require_once self::$manifest[$class];
		}
	}
	
	public static function buildManifest() {
		foreach (self::$classpath as $path) {
			self::collectClasses($path);
		}
		self::saveManifest();
	}
	
	public static function loadManifest() {
		$manifestPath = self::getManifestPath();
		if (file_exists($manifestPath)) {
			self::$manifest = require($manifestPath);
		}
	}
	
	private static function saveManifest() {
		$content  = '<?php // floe.manifest generated at '  . date('Y-m-d H:i:s') . PHP_EOL;
		$content .= 'return ' . var_export(self::$manifest, true) . ';';
		file_put_contents(self::getManifestPath(), $content);
	}
	
	private static function collectClasses($path) {
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
		
		foreach ($files as $file) {
			if ($file->isFile() && preg_match(self::$allowedFilesPattern, $file->getFilename())) {
				if ($classes = self::scrapeClassFile($file->getPathname())) {
					foreach ($classes as $class) {
						self::$manifest[$class] = $file->getPathname();
					}
				}
			}
		}
	}
	
	private static function scrapeClassFile($file) {
		$classes = array();
		$tokens = token_get_all(file_get_contents($file));
		$length = count($tokens);
		
		for ($current = 0; $current < $length; $current++) {
			if (is_array($tokens[$current])) {
				if ($tokens[$current][0] == T_INTERFACE || $tokens[$current][0] == T_CLASS) {
					$current += 2;
					$classes[] = $tokens[$current][1];
				}
			}
		}
		return $classes;
	}
}
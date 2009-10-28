<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2005-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id: Response.class.php 333 2009-10-24 21:44:48Z coretxt $
 * @package server
 * @subpackage receptors
 */

require_once dirname(__FILE__) .'/../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../../language/en/Inflect.class.php';
//require_once dirname(__FILE__) .'/../controllers/RouteController.class.php';

class RouteDispatcher implements Receptor {
	static private $routes = array();
	
	static function addRoute($pattern, $binding) {
		self::$routes[$pattern] = $binding; 
	}
	
	static function map(array $routes) {
		self::$routes = $routes;
	}
	
	function run(Request $request, Response $response) {
		$binding = $this->matchRoutingPattern($request->uri);
		require_once CTR_DIR.$binding.'.controller.php';
	}
	
	function matchRoutingPattern($uri) {
		
		foreach(self::$routes as $pattern => $binding) {
			// test exact match first...
			if ($pattern == $uri->path()) return $binding;
			
			// test binding on regex routing rule
			preg_match('/'.str_replace('/', '\/', $pattern).'/', $uri->path(), $matches);
			if(empty($matches)) continue;
			
			preg_match('/(%)([0-9]+)/', $binding, $target);
			if (isset($target[2])) {
				if (isset($matches[$target[2]])) return $matches[$target[2]];
			}
		}
		
		throw new ResourceNotFound("No routing available for {$uri->path()}");
	}
	
}

?>
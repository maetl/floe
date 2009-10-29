<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id: Response.class.php 333 2009-10-24 21:44:48Z coretxt $
 * @package server
 * @subpackage receptors
 */

/**#@+
 * Required dependency.
 */
require_once dirname(__FILE__) .'/../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../../language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../ResourceNotFound.class.php';
/**#@-*/

/**
 * Binds requests to a controller based on regex routing patterns.
 * 
 * <p>Routing patterns are specified as strings, using the standard regex grouping syntax
 * to capture the named context:</p>
 * 
 * - Literal pattern: "/my/action" => "index.action" binds to IndexController:;action()
 * - Named context: "/page/(.*)/" => "$1.index" maps to a matching controller name, eg: /page/base
 *   would bind to BaseController::index()
 * - Named context: "/page/(.*)/action-(.*)" => "$1.$2" will bind the method name dynamically.
 * 
 * <p>Be extra careful with the mapping between regex matching groups and allowed characters in PHP class
 * and method names. It's better to be explicit and define specific bindings to actions, rather than trying
 * to write one pattern to rule them all.</p>
 * 
 * @package server
 * @subpackage receptors
 */
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
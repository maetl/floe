<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package server
 * @subpackage receptors
 */

/**
 * Bind base URL requests to this controller by default.
 */
if (!defined('DefaultControllerBinding')) define('DefaultControllerBinding', 'index');

/**#@+
 * Required dependency.
 */
require_once dirname(__FILE__).'/../../language/en/Inflect.class.php';
require_once dirname(__FILE__).'/../ResourceNotFound.class.php';
/**#@-*/

/**
 * Delegates request binding to a controller based on RESTful convention.
 * 
 * The resource binding is based on HTTP verbs invoking a named action
 * on the controller:
 * 
 * A GET request to /things/id => maps to the ThingController::get() method
 * A POST request to /things/id => maps to the ThingController:post() method
 * A PUT request to /things/id maps to the ThingController::put() method
 * A DELETE request to /things/id maps to the ThingController::delete() method
 * 
 * The default URL mapping should throw a ResourceNotFound exception if no IndexController exists.
 * 
 * @package server
 * @subpackage receptors
 * @todo complete exception handling
 */
class ResourceDispatcher implements Receptor {

	public function run(Request $request, Response $response) {
		$base = $request->uri->segment(0);
		$params = $request->uri->segmentsFrom(1);
		if ($base == '') $base = DefaultControllerBinding;
		$path = APP_DIR ."controllers/$base.controller.php";
		if (!file_exists($path)) {
			$identity = $request->uri->segment(1);
			$path = APP_DIR ."controllers/$base/$identity.controller.php";
			$base = $identity;
			$params = $request->uri->segmentsFrom(2);
			if (!file_exists($path)) {
				include_once 'server/ResourceNotFound.class.php';
				throw new ResourceNotFound("Controller not found", $controllerPath);
			}
		}
		include_once $path;
		$classname = Inflect::toClassName($base).'Controller';
		if (class_exists($classname)) {
			$controller = new $classname($request, $response);
		} else {
			include_once 'server/ResourceNotFound.class.php';
			throw new ResourceNotFound("Controller $classname not defined", $path);
		}
		$method = $request->method();
		if (method_exists($controller, $method)) {
			if (method_exists($controller, 'before')) call_user_func(array($controller, 'before'));
			call_user_func_array(array($controller, $method), $params);
			if (method_exists($controller, 'after')) call_user_func(array($controller, 'after'));
		} else {
			include_once 'server/UnsupportedMethod.class.php';
			throw new UnsupportedMethod($method, $path);
		}

	}

}

?>
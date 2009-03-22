<?php
/**
 * $Id$
 * @package server
 * @subpackage receptors
 *
 * Copyright (c) 2007-2009 Coretxt
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
if (!defined('DefaultMethodBinding')) {
	define('DefaultMethodBinding', 'index');
}

require_once 'language/en/Inflect.class.php';

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
		if ($base == '') $base = DefaultMethodBinding;
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
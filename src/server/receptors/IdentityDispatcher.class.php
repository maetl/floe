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

require_once dirname(__FILE__) .'/../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../../language/en/Inflect.class.php';
require_once dirname(__FILE__) .'/../controllers/IdentityController.class.php';

if (!defined('DefaultMethodBinding')) define('DefaultMethodBinding', 'index');

/**
 * Delegates request binding to a controller based on URI identity.
 * 
 * The identity binding is based on a simple heirachical convention
 * for invoking controller methods:
 * 
 * /thing/action => maps to the ThingController::action() method
 * /thing/action/id => maps to the ThingController:action() method and passes the ID as a parameter
 * /thing => maps to the ThingController::index() method
 * / => maps to the IndexController::index() method
 * 
 * The default URL mapping should throw a ResourceNotFound exception if no IndexController exists.
 * 
 * @package server
 * @subpackage receptors
 * @todo document the precedence heirachy and refactor to better communicate what this code does
 */
class IdentityDispatcher implements Receptor {

	/**
	 * Look for a controller that maps to the URI 
	 * and invoke its identity method.
	 */
	public function run(Request $request, Response $response) {
		$base = (count($request->uri->segments()) == 1) ? $request->uri->identity() : $request->uri->segment(0);
		$identity = $request->uri->segment(1);
		$params = $request->uri->segmentsFrom(2);
		if ($base == '') $base = DefaultMethodBinding;
		if ($identity == '') $identity = $base;
		$path = CTR_DIR ."/$base.controller.php";
		if (!file_exists($path)) {
			$path = CTR_DIR ."/$base/$identity.controller.php";
			$base = $identity;
			$identity = $request->uri->segment(2);
			if ($identity == '') $identity = $base;
			$params = $request->uri->segmentsFrom(3);
			if (!file_exists($path) && defined('BindMissingDefault')) {
				$base = DefaultMethodBinding;
				$path = CTR_DIR ."/$base.controller.php";
				$params = $request->uri->segmentsFrom(0);
				if (!$path) {
					include_once dirname(__FILE__).'/../ResourceNotFound.class.php';
					throw new ResourceNotFound("Controller not found", $path);
				}
			}
		}
		if (file_exists($path)) {
			include_once $path;
		} else {
			if (file_exists(TPL_DIR.'/'.$base.'.php')) {
				$response->render($base);
				return;
			} else {
				include_once dirname(__FILE__).'/../ResourceNotFound.class.php';
				throw new ResourceNotFound("Controller file not found", $path);
			}
		}
		$classname = Inflect::toClassName($base).'Controller';
		if (class_exists($classname)) {
			$controller = new $classname($request, $response);
		} else {
			include_once dirname(__FILE__).'/../ResourceNotFound.class.php';
			throw new ResourceNotFound("Controller $classname not defined", $path);
		}
		$identity = $this->stripActionIdentifier($identity);
		if (method_exists($controller, $identity)) {
			$this->invoke($controller, $identity, $params);
		} elseif (method_exists($controller, DefaultMethodBinding)) {
			$this->invoke($controller, DefaultMethodBinding, $request->uri->segmentsFrom(1));
		} else {
			include_once dirname(__FILE__).'/../ResourceNotFound.class.php';
			throw new ResourceNotFound("Method $identity not defined in $classname", $path);
		}
	}
	
	private function stripActionIdentifier($identity) {
		// converts base action to a compatible format
		$identity = strtolower(Inflect::underscore(Inflect::decodeUriPart($identity)));
		// returns the base action name without a file extension
		if (strstr($identity, '.')) {
			$identity = explode('.', $identity);
			$identity = $identity[0];
		}
		return $identity;
	}
	
	private function invoke($controller, $identity, $params) {
		EventLog::info(sprintf("Invoked [%s->%s(%s)]", get_class($controller), $identity, implode(",", $params)));
		if (method_exists($controller, 'before')) call_user_func(array($controller, 'before'));
		call_user_func_array(array($controller, $identity), $params);
		if (method_exists($controller, 'after')) call_user_func(array($controller, 'after'));			
	}
	
}

?>
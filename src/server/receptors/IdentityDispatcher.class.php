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

/**#@+
 * Required dependency.
 */
require_once dirname(__FILE__) .'/../../framework/EventLog.class.php';
require_once dirname(__FILE__) .'/../../language/en/Inflect.class.php';
require_once dirname(__FILE__) .'/../controllers/IdentityController.class.php';
require_once dirname(__FILE__).'/../ResourceNotFound.class.php';
/**#@-*/

if (defined('DefaultMethodBinding')) {
	throw new Exception("Deprecated constant [DefaultMethodBinding]. Please use [IdentityDispatcher_DefaultBinding]");
}

if (defined('BindMissingDefault')) {
	throw new Exception("Deprecated constant [BindMissingDefault]. Please use [IdentityDispatcher_BindMissing]");
}

/**
 * Bind base URL requests to this controller method by default.
 */
if (!defined('IdentityDispatcher_DefaultBinding')) define('IdentityDispatcher_DefaultBinding', 'index');

/**
 * Delegates request binding to a controller based on URI identity.
 * 
 * <p>The identity binding is based on a simple heirachical convention
 * for invoking controller methods:</p>
 * 
 * - /thing/action => maps to the ThingController::action() method
 * - /thing/action/id => maps to the ThingController:action() method and passes the ID as a parameter
 * - /thing => maps to the ThingController::index() method
 * - / => maps to the IndexController::index() method
 * 
 * <p>The default URL mapping will throw a ResourceNotFound exception if no controller exists by that name.
 * To override this behavior, and route all requests to a controller method binding, define a constant
 * <code>BindMissingDefault</code> in your app configuration. This will load the default controller on
 * all URL requests, and leaves 404 handling up to you.</p>
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
		if ($base == '') $base = IdentityDispatcher_DefaultBinding;
		if ($identity == '') $identity = $base;
		$path = CTR_DIR ."/$base.controller.php";
		if (!file_exists($path)) {
			$path = CTR_DIR ."/$base/$identity.controller.php";
			$base = $identity;
			$identity = $request->uri->segment(2);
			if ($identity == '') $identity = $base;
			$params = $request->uri->segmentsFrom(3);
			if (!file_exists($path) && defined('IdentityDispatcher_BindMissing')) {
				$base = IdentityDispatcher_DefaultBinding;
				$path = CTR_DIR ."/$base.controller.php";
				$params = $request->uri->segmentsFrom(0);
				if (!$path) {
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
				throw new ResourceNotFound("Controller file not found", $path);
			}
		}
		$classname = Inflect::toClassName($base).'Controller';
		if (class_exists($classname)) {
			$controller = new $classname($request, $response);
		} else {
			throw new ResourceNotFound("Controller $classname not defined", $path);
		}
		$identity = $this->stripActionIdentifier($identity);
		if (method_exists($controller, $identity)) {
			$this->invoke($controller, $identity, $params);
		} elseif (method_exists($controller, IdentityDispatcher_DefaultBinding)) {
			$this->invoke($controller, IdentityDispatcher_DefaultBinding, $request->uri->segmentsFrom(1));
		} else {
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
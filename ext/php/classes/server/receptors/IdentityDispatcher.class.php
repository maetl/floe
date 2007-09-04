<?php
/**
* @package server
* @subpackage receptors
*/
if (!defined('DefaultMethodBinding')) {
	define('DefaultMethodBinding', 'index');
}

require_once 'language/en/Inflect.class.php';

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
 * @todo complete exception handling
 */
class IdentityDispatcher implements Receptor {

	/**
	 * Look for a controller that maps to the URI 
	 * and invoke its identity method.
	 */
	public function run(Request $request, Response $response) {
		$base = $request->uri->segment(0);
		$identity = $request->uri->segment(1);
		$params = $request->uri->segmentsFrom(2);
		if ($base == '') $base = DefaultMethodBinding;
		if ($identity == '') $identity = DefaultMethodBinding;
		$path = APP_DIR ."controllers/$base.controller.php";
		if (!file_exists($path)) {
			$path = APP_DIR ."controllers/$base/$identity.controller.php";
			$base = $identity;
			$identity = $request->uri->segment(2);
			if ($identity == '') $identity = DefaultMethodBinding;
			$params = $request->uri->segmentsFrom(3);
			if (!file_exists($path)) {
				$base = DefaultMethodBinding;
				$identity = 'page';
				$path = APP_DIR ."controllers/$base.controller.php";
				$params = $request->uri->segmentsFrom(0);
				if (!$path) {
					include_once 'server/ResourceNotFound.class.php';
					throw new ResourceNotFound("Controller not found", $path);
				}
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
		$identity = strtolower(Inflect::underscore(Inflect::decodeUriPart($identity)));
		if (method_exists($controller, $identity)) {
			if (method_exists($controller, 'before')) call_user_func(array($controller, 'before'));
			call_user_func_array(array($controller, $identity), $params);
			if (method_exists($controller, 'after')) call_user_func(array($controller, 'after'));
		} else {
			require_once 'server/ResourceNotFound.class.php';
			throw new ResourceNotFound("Method $identity not defined in $classname", $path);
		}
	}
	
}

?>
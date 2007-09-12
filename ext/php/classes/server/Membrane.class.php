<?php
/**
 * $Id$
 * @package server
 */
require_once 'Request.class.php';
require_once 'Response.class.php';
require_once 'Receptor.class.php';

/**
 * The main application server, using the metaphor of a cellular membrane.
 * 
 * The Membrane responds to requests by delegating to its chain of receptors for processing.
 *   
 * Order of filter execution is defined as first-in-first-out. The run method simply iterates
 * down the filter chain, executing each filter in turn.
 * 
 * Because of the shared-nothing nature of PHP, each invocation of the Membrane is stateless. Information
 * that needs to propagate through multiple requests should utilize sessions.
 * 
 * @package server
 */
class Membrane {

    private $receptors;
    private $request;
    private $response;

    function __construct() {
        $this->request = new Request;
        $this->response = new Response;
    }
	
    /**
    * Atach a servlet style receptor to the outer membrane.
    * 
    * @todo allow Array parameter to assign linked list of Interceptors
    */
    function attach($receptor) {
        if (is_object($receptor)) {
            $this->receptors[] =& $receptor;
        }
    }
    
    /**
    * Run the application receptors and render the response.
    */
    function run() {
        foreach ($this->receptors as $r) {
			try {
				$r->run($this->request, $this->response);
			} catch(Exception $error) {
				$this->response->raise($error);
			}
        }
		$this->response->out();
    }

}

?>
<?php
/**
 * $Id$
* @package server
*/

/**
* Represents a node in the server filter chain.
* 
* Receptors are used by the cell membrane to dispatch requests
* to internal controllers. Uses the servlet model of feeding
* a request and response object to the process run method.
* 
* @package server
* @abstract
*/
interface Receptor {
    /**
    * @param request request object
    * @param response response object
	* @abstract
    */
    public function run(Request $request, Response $response);

}

?>
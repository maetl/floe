<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
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
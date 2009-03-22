<?php
/**
 * $Id$
 * @package server
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
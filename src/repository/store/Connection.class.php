<?php
/**
 * $Id$
 * @package repository
 * @subpackage store
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

/**
 * Abstract connection to a data source.
 * 
 * The connection should be implemented as lazy loading and idempotent:
 * 
 * 1) Lazy Loading: the low level connection should not be established until a call to
 * execute requests a direct lookup on the data source.
 * 
 * 2) Idempotent: Calling the connect method multiple times should not overwrite the
 * existing connection.
 * 
 * @package repository
 * @subpackage store
 */
interface Connection {

	/**
	 * Establish a connection to data source.
	 * 
	 * This usually requires a username, password,
	 * host, and the name of the resource to connect.
	 */
	public function connect();
	
	/**
	 * Execute a bound query over the established connection.
	 */
	public function execute($query);

}

?>
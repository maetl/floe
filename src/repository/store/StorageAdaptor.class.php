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
require_once 'mysql/MysqlConnection.class.php';
require_once 'mysql/MysqlGateway.class.php';
require_once 'mysql/MysqlQuery.class.php';

/**
 * A factory for generating a singleton instance of a storage
 * adaptor. 
 *
 * @package repository
 * @subpackage store
 */
class StorageAdaptor {

		private static $implementation = null;
	
		/**
		 * Returns a MysqlAdaptor instance
		 */
        static function instance($plugin = false) {
        	if (!self::$implementation) {
            	self::$implementation = new MysqlGateway(new MysqlConnection());
        	}
        	return self::$implementation;
        }

		/**
		 * Factory method for returning a Query object.
		 *
		 * @return Query
		 */
		static function query() {
			return new MysqlQuery();
		}
		
}

?>
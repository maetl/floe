<?php
/**
* $Id: StorageAdaptor.class.php 44 2007-04-29 12:00:01Z maetl_ $
* @package repository
* @subpackage store
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
        function instance($plugin = false) {
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
		function query() {
			return new MysqlQuery();
		}
		
}

?>
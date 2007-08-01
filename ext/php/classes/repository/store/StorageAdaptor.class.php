<?php
/**
* $Id: StorageAdaptor.class.php 44 2007-04-29 12:00:01Z maetl_ $
* @package repository
*/
require_once 'repository/store/mysql/MysqlConnection.class.php';
require_once 'repository/store/mysql/MysqlAdaptor.class.php';

/**
 * A factory for generating instances of storage adaptors
 *
 * @package repository
 */
class StorageAdaptor {

		/**
		 * Returns a MysqlAdaptor instance
		 */
        function instance($plugin = false) {
            return new MysqlAdaptor(new MysqlConnection());
        }
		
		/**
		 * Returns a StorageAdaptor instance
		 */
        function connection($plugin = false) {
            return new MysqlConnection();
        }
		
}

?>
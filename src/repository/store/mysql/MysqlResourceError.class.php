<?php
/**
 * @package repository
 * @subpackage store.mysql
 */

/**
 * @package repository
 * @subpackage store
 */
class ResourceError extends Exception {

	var $message = "Unable to connect to the selected resource: %s";

}

/**
 * @package repository
 * @subpackage store.mysql
 */
class MysqlResourceError extends ResourceError {

}

?>
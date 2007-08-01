<?php

class ResourceError extends Exception {

	var $message = "Unable to connect to the selected resource: %s";

}

class MysqlResourceError extends ResourceError {

}

?>
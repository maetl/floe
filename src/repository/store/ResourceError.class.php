<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @package repository
 * @subpackage store
 */

/**
 * @package repository
 * @subpackage store
 */
class ResourceError extends Exception {

	var $message = "Unable to connect to the selected resource: %s";

}

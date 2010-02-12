<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package framework
 */

/**
 * Interface for loggers to implement.
 * 
 * @package framework
 */
interface LogHandler {
	
	public function emit($level, $message);
	
}

?>
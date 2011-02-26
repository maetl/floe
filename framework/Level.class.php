<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id: Level.class.php 350 2010-02-12 14:21:55Z coretxt $
 * @package framework
 */

/**
 * Struct representing enabled logging levels.
 * 
 * @package framework
 */
class Level {
	
	/**
	 * Default logging level.
	 */
	const Debug = 0;
	
	/**
	 * Info logging level.
	 */
	const Info = 1;
	
	/**
	 * Warning logging level.
	 */
	const Warning = 2;
	
	/**
	 * Error logging level.
	 */
	const Error = 3;
	
	/**
	 * Critical logging level.
	 */
	const Critical = 4;

}

?>
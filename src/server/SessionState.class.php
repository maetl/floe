<?php
/**
 * This file is part of Floe, a graceful PHP framework.
 * Copyright (C) 2005-2010 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * $Id$
 * @package server
 */

/**
 * Currently just wraps the session array provided by PHP. This is old code
 * and badly implemented, but there is ample room for improvement.
 * 
 * <p>This session behaves like a hash map that reloads itself when the constructor
 * is called. Values must be explicitly cleared, or else they will persist until
 * the current browser session ends. The whole session can be cleared using the
 * destroy method.</p>
 * 
 * @package server
 * @todo remove duplication in data members
 * @todo encapsulate session storage as a static var 
 * @todo gut the class and add better support for persistent storage
 * @todo add explicit timeout control
 * @todo add multiple subdomain control
 */
class SessionState {
	private $_properties;
	private $_id;
	
	/**
	 * Create a singleton instance of the class.
	 */
	static function instance() {
		if (!isset($GLOBALS['Session'])) {
			$GLOBALS['Session'] = new SessionState();
		}
		return $GLOBALS['Session'];
	}
	
	function __construct() {
		@session_start();
		$this->_properties = array();
		foreach($_SESSION as $key=>$value) {
			$this->_properties[$key] = $value;
		}
	}
	
	
	/**
	 * Add a new key=>value pair to the session.
	 * 
	 * If the key already exists, the value is overwritten.
	 */
	function set($key, $value) {
		$_SESSION[$key] = $value;
		$this->_properties[$key] = $value;
	}
	
	/**
	 * Get the value of a key.
	 * 
	 * Returns false if the key does not exist.
	 */
	function get($key) {
		return (isset($this->_properties[$key])) ? $this->_properties[$key] : false;
	}
	
	/**
	 * Remove a key=>value pair from the session.
	 */
	function remove($key) {
		unset($_SESSION[$key]);
		unset($this->_properties[$key]);
	}
	
	/**
	 * Clear all key=>value pairs from the session
	 */
	function destroy() {
		session_destroy();
		unset($this->_properties);
	}

	function __get($key) {
		return $this->get($key);
	}
	
	function __set($key, $value) {
		return $this->set($key, $value);
	}
	
}

?>
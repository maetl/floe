<?php
/**
 * This file is part of Floe, a minimalist PHP framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
 * @version $Id$
 * @package repository
 * @subpackage store.redis
 */

/**
 * Active connection to Redis store.
 *
 * @package repository
 * @subpackage store.redis
 */
class RedisConnection {
	private $host;
	private $port;
	private $connection;

	function __construct($host, $port=6379) {
		$this->host = $host;
		$this->port = $port;
	}
	
	function connect() {
		if ($this->connection) return true;
		$socket = stream_socket_client($this->host.':'.$this->port, $errno, $errstr);
		if ($socket) {
			$this->connection = $socket;
			return true;
		}
		if ($errno || $errstr) {
			$msg = "Could not connect to {$this->host}:{$this->port} ";
			throw new Exception($msg . $errstr);
		}
	}
	
	function disconnect() {
		if ($this->connection) fclose($this->connection);
		$this->connection = null;
	}
	
	function write($command) {
		while ($command) {
			$i = fwrite($this->connection, $command);
			if ($i == 0) break;
		    $command = substr($command, $i);
		}
	}
	
	function read() {
		if ($data = fgets($this->connection)) return $data;
		$msg = "Could not read from socket";
		throw new Exception($msg);
	}
	
}
?>
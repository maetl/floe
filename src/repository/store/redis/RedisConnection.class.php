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
define('CRLF', sprintf('%s%s', chr(13), chr(10)));

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
		if ($this->connection) {
			$this->write("QUIT");
			fclose($this->connection);
		}
		$this->connection = null;
	}
	
	function write($command) {
		if (!$this->connection) $this->connect();
		fwrite($this->connection, $command.CRLF);
	}
	
	function read() {
		$data = trim(fgets($this->connection), 512);
		
		switch (substr($data, 0, 1)) {
			case "+":
				$result = substr(trim($data), 1);
				break;
			case "-":
				throw new Exception(substr(trim($data), 4));
				break;
			case "$":	
				$length = substr(trim($data), 1);
				$result = "";
				do {
					$result .= trim(fread($this->connection, 1024), CRLF);
				} while (strlen($result) < $length);
				break;
			default:
				throw new Exception("Bad data from connection");
				break;
		}
		return $result;

	}
	
}
?>
<?php
/**
 * This file is part of Floe, a graceful web framework.
 * Copyright (C) 2007-2009 Mark Rickerby <http://maetl.net>
 *
 * See the LICENSE file distributed with this software for full copyright, disclaimer
 * of liability, and the specific limitations that govern the use of this software.
 *
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
	
	function execute($command) {
		$writeResult = $this->write($command);
		if (!$writeResult) {
			throw new Exception("Bad write to socket");
		}
		return $this->read();
	}
	
	function write($command) {
		if (!$this->connection) $this->connect();
		return fwrite($this->connection, $command.CRLF);
	}
	
	function read() {
		$responseHeader = fgets($this->connection);
		if (!$responseHeader) {
			throw new Exception("Bad response from socket");
		}
		
		$prefix = substr($responseHeader, 0, 1);
		$responseBody = substr($responseHeader, 1, -2);
		
		return $this->readResponse($prefix, $responseBody);
	}
	
	private function readResponse($prefix, $data) {
		switch ($prefix) {
			# status
			case "+":
				$result = $this->readStatus($data);
				break;
			
			 # error
			case "-":
				throw new Exception(substr(trim($data), 4));
				break;
			
			# bulk
			case "$":
				$result = $this->readBulk($data);
				break;
			
			default:
				echo $data;
				throw new Exception("Bad data from connection");
				break;
		}
		
		return $result;
	}

	function readStatus($status) {
		if ($status === 'OK') {
			return true;
		} elseif ($status == 'QUEUED') {
			// queue unsupported
			throw new Exception("QUEUED response not supported");
		}
		return $status;
	}
	
	function readBulk($length) {
		if ($length > 0) {
			$data = stream_get_contents($this->connection, $length);
		} else {
			$data = "";
		}
		fread($this->connection, 2);
		return $data;
	}
	
}
?>
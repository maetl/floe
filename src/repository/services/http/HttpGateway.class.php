<?php
/**
 * @package repository
 * @subpackage services.http
 */

/**
 * HTTP client that implements a service gateway.
 *
 * <p>This is an experimental attempt to integrate HTTP services with the repository,
 * though this class itself has been battle tested in Flicks.co.nz and works pretty damn well.</p>
 *
 * <p>And so it should, because all it really is, is a nice little wrapper around CURL.</p>
 *
 * @package repository
 * @subpackage services.http
 */
class HttpGateway {
	
	private $curl;
	
	private $headers;
	
	private $responseHeaders;
	
	private $responseBody;
	
	private $failOnError;
	
	function __construct() {
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, array($this, 'parseHeader'));
		curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, array($this, 'parseBody'));
		$this->headers = array();
	}
	
	/**
	 * Throw an exception if the request encounters an HTTP error condition.
	 *
	 * <p>An error condition is considered to be:</p>
	 *
	 * <ul>
	 * 	<li>400-499 - Client error</li>
	 *	<li>500-599 - Server error</li>
	 * </ul>
	 *
	 * <p><em>Note that this doesn't use the builtin CURL_FAILONERROR option,
	 * as this fails fast, making the HTTP body and headers inaccessible.</em></p>
	 */
	function failOnError($option = true) {
		$this->failOnError = $option;
	}
	
	/**
	 * HTTP basic authentication.
	 */
	function authorize($username, $password) {
		curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");
	}
	
	/**
	 * Set a default timeout for the request.
	 */
	function setTimeout($timeout) {
		curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
	}
	
	/**
	 * Set a request header.
	 */
	function header($header, $value) {
		$this->headers[$header] = "$header: $value";
	}
	
	/**
	 * Initialize common settings for a new request.
	 */
	private function initializeRequest() {
		$this->responseBody = "";
		$this->responseHeaders = array();
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);		
	}
	
	/**
	 * Throw an exception if the response is not OK.
	 */
	private function checkResponse() {
		if (curl_errno($this->curl)) {
			throw new Exception(curl_error($this->curl), curl_errno($this->curl));
		}
		if ($this->failOnError) {
			$status = $this->getStatus();
			if ($status >= 400 && $status <= 499) {
				throw new Exception("Client Error", $status);
			} elseif ($status >= 500 && $status <= 599) {
				throw new Exception("Server Error", $status);
			}
		}
	}
	
	/**
	 * Make an HTTP GET request to the specified endpoint.
	 */
	function rawGet($uri) {
		$this->initializeRequest();
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_HTTPGET, true);
		curl_exec($this->curl);
		$this->checkResponse();
		return $this;	
	}
	
	/**
	 * Make an HTTP GET request and cache the response.
	 */
	function get($uri, $query=false) {
		if (is_array($query)) $uri .= "?".http_build_query($query);
		$this->rawGet($uri);
		return $this;
	}

	/**
	 * Make an HTTP POST request to the specified endpoint.
	 */	
	function post($uri, $data) {
		$this->initializeRequest();
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_POST, true);
		if (is_array($data)) {
			$data = http_build_query($data);
		} 
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		curl_exec($this->curl);
		$this->checkResponse();
		return $this;
	}

	/**
	 * Make an HTTP HEAD request to the specified endpoint.
	 */
	function head($uri) {
		$this->initializeRequest();
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_NOBODY, true); 
		curl_exec($this->curl);
		$this->checkResponse();
		return $this;
	}

	/**
	 * Make an HTTP PUT request to the specified endpoint.
	 */	
	function put($uri, $data) {
		$this->initializeRequest();
		$handle = tmpfile();
		fwrite($handle, $data);
		fseek($handle, 0);
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_setopt($this->curl, CURLOPT_PUT, true);
		curl_setopt($this->curl, CURLOPT_INFILE, $handle);
		curl_setopt($this->curl, CURLOPT_INFILESIZE, strlen($data));
		curl_exec($this->curl);
		$this->checkResponse();
		return $this;
	}

	/**
	 * Make an HTTP DELETE request to the specified endpoint.
	 */	
	function delete($uri, $data) {
		$this->initializeRequest();
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($this->curl, CURLOPT_URL, $uri);
		curl_exec($this->curl);
		$this->checkResponse();
		return $this;
	}
	
	/**
	 * Callback method collects body string from the response.
	 */
	private function parseBody($curl, $body) {
		$this->responseBody .= $body;
		return strlen($body);
	}
	
	/**
	 * Callback methods collects header lines from the response.
	 */
	private function parseHeader($curl, $headers) {
		$parts = explode(": ", $headers);
		if (isset($parts[1])) {
			$this->responseHeaders[$parts[0]] = trim($parts[1]);
		}
        return strlen($headers);
	}

	/**
	 * Access the status code of the response.
	 */	
	function getStatus() {
		return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	}
	
	/**
	 * Access the body of the response
	 */
	function getBody() {
		return $this->responseBody;
	}
	
	/**
	 * Access given header from the response.
	 */	
	function getHeader($header) {
		if (array_key_exists($header, $this->responseHeaders)) return $this->responseHeaders[$header];
	}
	
	/**
	 * Return the full list of response headers
	 */
	function getHeaders() {
		return $this->responseHeaders;
	}
	
	/**
	 * Ensure cURL is closed.
	 */
	function __destruct() {
		curl_close($this->curl);
	}
	
}

<?php
/**
 * A simple and lightweight cURL library with support for multiple requests in parallel.
 *
 * @package     Curl
 * @version     2.0
 * @author      Jonas Stendahl
 * @license     MIT License
 * @copyright   2013 Jonas Stendahl
 * @link        http://github.com/jyggen/curl
 */

namespace jyggen;

use jyggen\Curl\Dispatcher;
use jyggen\Curl\Session;

class Curl
{

	/**
	 * Static helper to do DELETE requests.
	 *
	 * @param  mixed $url
	 * @return array
	 */
	public static function delete($url)
	{

		if (!is_array($url)) {

			$urls = array($url => null);

		} else {

			foreach ($url as $value) {

				$urls[$value] = null;

			}

		}

		return static::makeRequest('DELETE', $urls);

	}

	/**
	 * Static helper to do GET requests.
	 *
	 * @param  mixed $url
	 * @return array
	 */
	public static function get($url)
	{

		if (!is_array($url)) {

			$urls = array($url => null);

		} else {

			foreach ($url as $value) {

				$urls[$value] = null;

			}

		}

		return static::makeRequest('GET', $urls);

	}

	/**
	 * Static helper to do POST requests.
	 *
	 * @param  mixed $url
	 * @param  array $data
	 * @return array
	 */
	public static function post($urls, array $data = null)
	{

		if (!is_array($urls)) {

			$urls = array($urls => $data);

		}

		return static::makeRequest('POST', $urls);

	}

	/**
	 * Static helper to do PUT requests.
	 *
	 * @param  mixed $urls
	 * @param  array $data
	 * @return array
	 */
	public static function put($urls, array $data = null)
	{

		if (!is_array($urls)) {

			$urls = array($urls => $data);

		}

		return static::makeRequest('PUT', $urls);

	}

	/**
	 * Setup and execute a HTTP request.
	 *
	 * @param  string $method
	 * @param  array  $urls
	 * @return array
	 */
	protected static function makeRequest($method, $urls)
	{

		// Create a new Dispatcher.
		$dispatcher = new Dispatcher;

		// Foreach $urls:
		foreach ($urls as $url => $data) {

			if($data !== null) {

				$data = http_build_query($data);

			}


			// Create a new Session.
			$session = new Session($url);

			// Follow any 3xx HTTP status code.
			$session->setOption(CURLOPT_FOLLOWLOCATION, true);

			if ($method === 'DELETE') {

				// Set request method to DELETE.
				$session->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

			} elseif ($method === 'POST') {

				// Add the POST data to the session.
				$session->setOption(CURLOPT_POST, true);
				$session->setOption(CURLOPT_POSTFIELDS, $data);

			} elseif ($method === 'PUT') {

				// Write the PUT data to memory.
				$fh = fopen('php://memory', 'rw');
				fwrite($fh, $data);
				rewind($fh);

				// Add the PUT data to the session.
				$session->setOption(CURLOPT_INFILE, $fh);
				$session->setOption(CURLOPT_INFILESIZE, mb_strlen($data, 'UTF-8'));
				$session->setOption(CURLOPT_PUT, true);

			} else {

				// Redundant, but reset the method to GET.
				$session->setOption(CURLOPT_HTTPGET, true);

			}

			// Add the session to the dispatcher.
			$dispatcher->add($session);

		}

		// Execute the request(s).
		$dispatcher->execute();

	}

}
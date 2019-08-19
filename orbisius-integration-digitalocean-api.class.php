<?php

/**
 * This php class interacts with DigitalOcean's API v2.
 * Class Orbisius_Integration_DigitalOcean_API
 * @author SVETOSLAV MARINOV (SLAVI) | http://orbisius.com
 * @copyright 2019-3000 All Rights Reserved.*
 */
/*
 * The MIT License

Copyright © 2019-3000 SVETOSLAV MARINOV

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the “Software”), to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of

the Software.
THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class Orbisius_Integration_DigitalOcean_API {
	private $ver = '1.0.2';

	/**
	 * @desc https://developers.digitalocean.com/documentation/v2/#domain-records
	 */
	const REC_TYPE_A = 'A';
	const REC_TYPE_MX = 'MX';
	const REC_TYPE_NS = 'NS';
	const REC_TYPE_CAA = 'CAA';
	const REC_TYPE_TXT = 'TXT';
	const REC_TYPE_SRV = 'SRV';
	const REC_TYPE_AAAA = 'AAAA';
	const REC_TYPE_CNAME = 'CNAME';

	private $api_token = '';
	private $verify_ssl = true;

	/**
	 * @return bool
	 */
	public function getVerifySsl() {
		return isset($this->verify_ssl) && !empty($this->verify_ssl);
	}

	/**
	 * @param bool $verify_ssl
	 */
	public function setVerifySsl($verify_ssl) {
		$this->verify_ssl = $verify_ssl;
	}

	private $api_base_url = 'https://api.digitalocean.com/v2';
	private $params = [];

	public function __construct($params = []) {
		$this->init($params);
	}

	/**
	 * @param array $params
	 * @throws Exception
	 */
	public function init($params = []) {
		$api_token = getenv('ORBISIUS_DO_INTEGRATION_API_TOKEN');

		if (!empty($api_token)) {
			// cool
		} elseif (!empty($params['token'])) {
			$api_token = $params['token'];
		} elseif (!empty($params['api_key'])) {
			$api_token = $params['api_key'];
		} elseif (!empty($params['key'])) {
			$api_token = $params['key'];
		}

		if (isset($params['verify_ssl'])) {
			$this->setVerifySsl($params['verify_ssl']);
		}

		$this->params = $params;

		if (!empty($api_token)) {
			$this->setApiToken($api_token);
		}
	}

	/**
	 * Loads and sets a token from (ini) file.
	 * @see https://certbot-dns-digitalocean.readthedocs.io/en/stable/#credentials
	 * @param string $file
	 * @return string
	 */
	public function loadApiTokenFromFile($file) {
		if (!is_file($file)) {
			return false;
		}

		$buff = file_get_contents($file, LOCK_SH);

		// We'll use this ini later for DNS stuff
		// # DigitalOcean API credentials used by Certbot
		//dns_digitalocean_token = 0000111122223333444455556666777788889999aaaabbbbccccddddeeeeffff

		if (preg_match('#(token|api_key|key)\s*=[\s\'\"]*([\w\-]+)#si', $buff, $matches)) {
			$api_token = $matches[2];
			$this->setApiToken($api_token);
		}

		return $this->getApiToken();
	}

	/**
	 * @return string
	 */
	public function getApiToken() {
		return $this->api_token;
	}

	/**
	 * @param string $api_token
	 */
	public function setApiToken($api_token) {
		$this->api_token = $api_token;
	}

	const METHOD_GET = 'GET';
	const METHOD_PUT = 'PUT';
	const METHOD_HEAD = 'HEAD';
	const METHOD_POST = 'POST';
	const METHOD_DELETE = 'DELETE';

	/**
	 * Calls DigitalOcean API
	 *
	 * @param string $api_segment
	 * @param array $data
	 * @param string $api_method
	 * @param array $res_arr
	 */
	function call($api_segment, $req_params = [], $api_method = '') {
		$api_token = $this->getApiToken();

		if (empty($api_token)) {
			throw new Exception("API Token was not passed or is invalid.");
		}

		$res_arr = [
			'msg' => '',
			'status' => 0,
			'data' => [],
		];

		$timeout = 15;
		$connect_timeout = 5;
		$api_token = $this->api_token;

		if (empty($api_method)) {
			// /records/123 get record meta info
			if (preg_match('#/\d+$#si', $api_method)) {
				if (empty($req_params)) { // no req data -> so need data
					$api_method = self::METHOD_HEAD;
				} else { // updating existing record
					$api_method = self::METHOD_PUT;
				}
			} else {
				$api_method = self::METHOD_GET;
			}
		} else {
			$api_method = strtoupper($api_method);
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->api_base_url . $api_segment);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $api_method);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);

		// Good practice to let people know who's accessing their servers.
		// See https://en.wikipedia.org/wiki/User_agent
		$email = empty($this->params['email']) ? 'n/a' : $this->params['email'];
		curl_setopt($ch, CURLOPT_USERAGENT, sprintf('Orbisius_DigitalOcean_Integration/1.0 (email: %s)', $email));

		// Set timeouts
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);

		// https://stackoverflow.com/questions/731117/error-using-php-curl-with-ssl-certificates
		$default_ca_cert_file = dirname(__FILE__) . '/cacert.pem';

		// Does the user want to turn off SSL certificate verification?
		// verify_ssl => 0,
		if (!$this->getVerifySsl()) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		} elseif (!empty($this->params['ca_cert_file'])) {
			if (!file_exists($this->params['ca_cert_file'])) {
				throw new Exception("The passed ca_cert_file file doesn't exist or is not readable.");
			}

			curl_setopt($ch, CURLOPT_CAINFO, $this->params['ca_cert_file']);
			curl_setopt($ch, CURLOPT_CAPATH, $this->params['ca_cert_file']);
		} elseif (file_exists($default_ca_cert_file)) {
			curl_setopt($ch, CURLOPT_CAINFO, $default_ca_cert_file);
			curl_setopt($ch, CURLOPT_CAPATH, $default_ca_cert_file);
		}

		if (!empty($req_params)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_params));
		}

		// Set auth headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json',
			"Cache-Control: no-cache",
			'Authorization: Bearer ' . $api_token,
		));

		$buffer = curl_exec($ch);

		// 200 is code from a regular request, 201 when a record is created, 204 is when it's deleted
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$res_arr['status'] = !empty($buffer) && in_array( $http_code, [ 200, 201, 204, ] );

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($buffer, 0, $header_size);
		$header = trim($header);
		$body = substr($buffer, $header_size);
		$body = trim($body);

		// https://www.php.net/manual/en/function.json-decode.php
		$flags = JSON_BIGINT_AS_STRING; // so there's no problem with numbers

		$res_arr['data']['raw_headers'] = trim($header);
		$res_arr['data'] = empty($body) ? [] : json_decode($body, true, $depth = 512, $flags);

		if (!empty($this->params['debug'])) { //  || empty($res_arr['status'])
			$info = curl_getinfo($ch);
			$res_arr['data']['debug'] = $info;
		}

		curl_close($ch);

		return $res_arr;
	}

	/**
	 * @param $api_segment
	 * @param array $data
	 * @return array
	 */
	public function create($api_segment, array $data ) {
		$res_arr = $this->call($api_segment, $data, Orbisius_Integration_DigitalOcean_API::METHOD_POST);
		return $res_arr;
	}

	/**
	 * @param $api_segment
	 * @return array
	 */
	public function get($api_segment) {
		$res_arr = $this->call($api_segment);
		return $res_arr;
	}

	/**
	 * @param $api_segment
	 * @return array
	 */
	public function delete($api_segment) {
		$res_arr = $this->call($api_segment, [], Orbisius_Integration_DigitalOcean_API::METHOD_DELETE);
		return $res_arr;
	}

	/**
	 * @param $api_segment
	 * @param array $data
	 * @return array
	 */
	public function update($api_segment, array $data ) {
		$res_arr = $this->call($api_segment, $data, Orbisius_Integration_DigitalOcean_API::METHOD_PUT);
		return $res_arr;
	}

	/**
	 * Checks if the type is valid.
	 * @param string $record_type
	 * @return bool
	 */
	public function isValidRecordType($record_type) {
		$record_types = [
			self::REC_TYPE_A,
			self::REC_TYPE_TXT,
			self::REC_TYPE_MX,
			self::REC_TYPE_NS,
			self::REC_TYPE_AAAA,
			self::REC_TYPE_CAA,
			self::REC_TYPE_SRV,
			self::REC_TYPE_CNAME,
		];

		return in_array($record_type, $record_types);
	}
}
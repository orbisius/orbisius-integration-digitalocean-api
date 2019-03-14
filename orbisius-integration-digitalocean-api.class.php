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
    private $api_token = '';
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
        if (empty($params['token']) || !preg_match('#^[a-z\d]+$#si', $params['token'])) {
            throw new Exception("API Token was not passed or is invalid.");
        }

        $this->api_token = $params['token'];
        $this->params = $params;
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
        $email = empty($this->params['email']) ? 'n/a' : empty($this->params['email'];
        curl_setopt($ch, CURLOPT_USERAGENT, sprintf('Orbisius_DigitalOcean_Integration/1.0 (email: %s)', $email));

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);

        // Does the user want to turn off SSL certificate verification?
        // verify_ssl => 0,
        if (isset($this->params['verify_ssl']) && empty($this->params['verify_ssl'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if (!empty($req_params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_params));
        }

        //Set your auth headers
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

        $res_arr['data']['raw_headers'] = trim($header);
        $res_arr['data'] = empty($body) ? [] : json_decode($body, true);

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
}
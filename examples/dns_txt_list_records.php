<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	//'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// List DNS records for example.com
$res_arr = $digital_ocean_api->get('/domains/example.com/records');
var_export($res_arr);

// Result
/*
array (
  'msg' => '',
  'status' => true,
  'data' =>
  array (
    'domain_records' =>
    array (
      0 =>
      array (
        'id' => 67311503,
        'type' => 'NS',
        'name' => '@',
        'data' => 'ns1.digitalocean.com',
        'priority' => NULL,
        'port' => NULL,
        'ttl' => 1800,
        'weight' => NULL,
        'flags' => NULL,
        'tag' => NULL,
      ),
      1 =>
      array (
        'id' => 67311504,
        'type' => 'NS',
        'name' => '@',
        'data' => 'ns2.digitalocean.com',
        'priority' => NULL,
        'port' => NULL,
        'ttl' => 1800,
        'weight' => NULL,
        'flags' => NULL,
        'tag' => NULL,
      ),
      2 =>
      array (
        'id' => 67311505,
        'type' => 'NS',
        'name' => '@',
        'data' => 'ns3.digitalocean.com',
        'priority' => NULL,
        'port' => NULL,
        'ttl' => 1800,
        'weight' => NULL,
        'flags' => NULL,
        'tag' => NULL,
      ),
      3 =>
      array (
        'id' => 67313117,
        'type' => 'A',
        'name' => '@',
        'data' => '163.172.29.68',
        'priority' => NULL,
        'port' => NULL,
        'ttl' => 3600,
        'weight' => NULL,
        'flags' => NULL,
        'tag' => NULL,
      ),
      4 =>
      array (
        'id' => 67313123,
        'type' => 'A',
        'name' => '*',
        'data' => '163.172.29.68',
        'priority' => NULL,
        'port' => NULL,
        'ttl' => 3600,
        'weight' => NULL,
        'flags' => NULL,
        'tag' => NULL,
      ),
    ),
    'links' =>
    array (
    ),
    'meta' =>
    array (
      'total' => 5,
    ),
  ),
)
 */
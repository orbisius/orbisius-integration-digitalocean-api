<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// https://developers.digitalocean.com/documentation/v2/#create-a-new-domain-record
$create_dns_records_params = [
    'type' => 'TXT',
    'name' => 'some-text-record',
    'data' => 'some_test_data',
];

// List DNS records for example.com
$res_arr = $digital_ocean_api->create('/domains/exampe.com/records', $create_dns_records_params);
var_export($res_arr);

// Result
/*
array (
  'msg' => '',
  'status' => true,
  'data' =>
  array (
    'domain_record' =>
    array (
      'id' => 67319473,
      'type' => 'TXT',
      'name' => 'some-text-record',
      'data' => 'some_test_data',
      'priority' => NULL,
      'port' => NULL,
      'ttl' => 1800,
      'weight' => NULL,
      'flags' => NULL,
      'tag' => NULL,
    ),
  ),
)
 */
<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// Update a DNS record for example.com
$update_dns_record_params = [
	'type' => Orbisius_Integration_DigitalOcean_API::REC_TYPE_TXT,
	'name' => 'some-text-record',
	'data' => 'some_more_test_data',
];

$res_arr = $digital_ocean_api->update('/domains/example.com/records/67319687', $update_dns_record_params);
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
      'id' => 67319687,
      'type' => 'TXT',
      'name' => 'some-text-record',
      'data' => 'some_more_test_data',
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
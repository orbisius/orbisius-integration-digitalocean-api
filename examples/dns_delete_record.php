<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	//'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// Deletes a DNS record for example.com
$res_arr = $digital_ocean_api->delete('/domains/example.com/records/67319473');
var_export($res_arr);

// Result
/*
array (
  'msg' => '',
  'status' => true,
  'data' =>
  array (
  ),
)
*/
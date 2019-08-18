<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	//'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// https://developers.digitalocean.com/documentation/v2/#create-a-new-domain-record
$create_dns_records_params = [
    'type' => Orbisius_Integration_DigitalOcean_API::REC_TYPE_CNAME,
    'name' => 'www.a100',
    'data' => 'a100.go359.me.', // <=== must end in a dot
];

// Create a DNS record for example.com
$res_arr = $digital_ocean_api->create('/domains/go359.me/records', $create_dns_records_params);
var_export($res_arr);

// Result
/*
 cname creation
array (
  'msg' => '',
  'status' => true,
  'data' =>
  array (
    'domain_record' =>
    array (
      'id' => 77618425,
      'type' => 'CNAME',
      'name' => 'www.a100',
      'data' => 'a100.go359.me',
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
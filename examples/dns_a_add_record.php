<?php

require_once dirname(__DIR__) . '/config.php';

$do_api_params = [
	'token' => ORBISIUS_DO_INTEGRATION_API_TOKEN,
	//'debug' => 1,
	//'verify_ssl' => 0,
];

$digital_ocean_api = new Orbisius_Integration_DigitalOcean_API($do_api_params);

// https://developers.digitalocean.com/documentation/v2/#create-a-new-domain-record
//$create_dns_records_params = [
//    'type' => Orbisius_Integration_DigitalOcean_API::REC_TYPE_A,
//    'name' => 'a100',
//    'data' => '139.162.188.217',
//];

$create_dns_records_params = [
    'type' => Orbisius_Integration_DigitalOcean_API::REC_TYPE_CNAME,
    'name' => 'www.a100',
    'data' => 'a100.go359.me.',
];

// Create a DNS record for example.com
$res_arr = $digital_ocean_api->create('/domains/go359.me/records', $create_dns_records_params);
var_export($res_arr);

// Result
/*
 a creation

 * C:\Copy\Dropbox\Dev\php-7.1.12-Win32-VC14-x64\php.exe C:\Copy\Dropbox\cloud\projects\clients\qsandbox2.com\htdocs\projects\orbisius-integration-digitalocean-api\examples\dns_add_A_record.php
array (
  'msg' => '',
  'status' => true,
  'data' =>
  array (
    'domain_record' =>
    array (
      'id' => 77618347,
      'type' => 'A',
      'name' => 'a100',
      'data' => '139.162.188.217',
      'priority' => NULL,
      'port' => NULL,
      'ttl' => 1800,
      'weight' => NULL,
      'flags' => NULL,
      'tag' => NULL,
    ),
  ),
)
Process finished with exit code 0


cname

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
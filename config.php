<?php

define('ORBISIUS_DO_INTEGRATION_BASE_DIR', __DIR__);

// Generate one from https://cloud.digitalocean.com/account/api/tokens
define('ORBISIUS_DO_INTEGRATION_API_TOKEN', '==== INSERT TOKEN HERE ====');

// list dns records
//$res_arr = $digital_ocean_api->call('/domains/wpsandbox.org
require_once ORBISIUS_DO_INTEGRATION_BASE_DIR . '/orbisius-integration-digitalocean-api.class.php';

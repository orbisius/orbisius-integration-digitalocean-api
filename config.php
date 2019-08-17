<?php

define('ORBISIUS_DO_INTEGRATION_BASE_DIR', __DIR__);

// Generate one from https://cloud.digitalocean.com/account/api/tokens
if (getenv('ORBISIUS_DO_INTEGRATION_API_TOKEN')) {
	define('ORBISIUS_DO_INTEGRATION_API_TOKEN', getenv('ORBISIUS_DO_INTEGRATION_API_TOKEN'));
} else {
	define('ORBISIUS_DO_INTEGRATION_API_TOKEN', '');
}

require_once ORBISIUS_DO_INTEGRATION_BASE_DIR . '/orbisius-integration-digitalocean-api.class.php';

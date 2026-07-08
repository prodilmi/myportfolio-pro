<?php
/**
 * Application Constants
 * @package MyPortfolioPro
 */

define('APP_NAME', 'MyPortfolioPro');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('BASE_URL', rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') . '/');
define('ASSETS_URL', BASE_URL . 'assets/');

define('SESSION_TIMEOUT', 3600);
define('SESSION_NAME', 'MyPortfolioPro');
define('PASSWORD_HASH_COST', 12);
define('ITEMS_PER_PAGE', 20);

define('UPLOAD_PATH', STORAGE_PATH . '/uploads/');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);

define('BROKERS', array('ibkr', 'xtb', 'etoro'));
define('CURRENCIES', array('USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD'));

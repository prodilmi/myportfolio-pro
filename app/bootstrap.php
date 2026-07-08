<?php
/**
 * Bootstrap Application
 * Autoload and initialize core components
 */

require_once __DIR__ . '/config/constants.php';

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load database config
if (!file_exists(CONFIG_PATH . '/database.php')) {
    die('Database configuration not found. Please copy config/database.php.example to config/database.php and update your credentials.');
}

$db_config = require CONFIG_PATH . '/database.php';

// Initialize Database singleton
$db = \App\Core\Database::getInstance($db_config);

// Start session
\App\Core\Session::start();

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
});

set_exception_handler(function ($exception) {
    error_log($exception->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo 'An error occurred. Please try again later.';
});

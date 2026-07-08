<?php
/**
 * Bootstrap Configuration
 */

define('BASE_URL', 'http://localhost/myportfolio-pro/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('API_URL', BASE_URL . 'api/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'myportfolio_pro');

// Encryption key
define('ENCRYPTION_KEY', hash('sha256', 'your-secret-encryption-key-change-this'));

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    
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

// Database Connection
require_once __DIR__ . '/app/Core/Database.php';
use App\Core\Database;

$db = Database::connect([
    'host' => DB_HOST,
    'user' => DB_USER,
    'password' => DB_PASS,
    'database' => DB_NAME
]);

// Helper function to sanitize output
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Helper function to redirect
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

<?php
/**
 * Main Index - Router
 */

define('APP_ROOT', __DIR__);
require_once APP_ROOT . '/app/bootstrap.php';

use App\Core\Auth;

$auth = new Auth($db);
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Public pages
$public_pages = ['login', 'register', 'home'];

// Redirect to login if not authenticated and trying to access protected page
if (!$auth->isAuthenticated() && !in_array($page, $public_pages)) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

// Route handler
switch ($page) {
    case 'home':
        require_once APP_ROOT . '/pages/home.php';
        break;
    case 'login':
        if ($auth->isAuthenticated()) {
            header('Location: ' . BASE_URL . 'index.php?page=dashboard');
            exit;
        }
        require_once APP_ROOT . '/pages/login.php';
        break;
    case 'register':
        if ($auth->isAuthenticated()) {
            header('Location: ' . BASE_URL . 'index.php?page=dashboard');
            exit;
        }
        require_once APP_ROOT . '/pages/register.php';
        break;
    case 'dashboard':
        require_once APP_ROOT . '/pages/dashboard.php';
        break;
    case 'portfolios':
        require_once APP_ROOT . '/pages/portfolios.php';
        break;
    case 'portfolio-detail':
        require_once APP_ROOT . '/pages/portfolio-detail.php';
        break;
    case 'trading-history':
        require_once APP_ROOT . '/pages/trading-history.php';
        break;
    case 'broker-integration':
        require_once APP_ROOT . '/pages/broker-integration.php';
        break;
    case 'notifications':
        require_once APP_ROOT . '/pages/notifications.php';
        break;
    case 'settings':
        require_once APP_ROOT . '/pages/settings.php';
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page not found';
        break;
}

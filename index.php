<?php
/**
 * Main Entry Point
 */

require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;

$response = new Response();
$page = Request::getQuery('page', 'home');

// Route to appropriate page
switch ($page) {
    case 'login':
        require 'pages/login.php';
        break;
    case 'register':
        require 'pages/register.php';
        break;
    case 'logout':
        $auth = new Auth($db);
        $auth->logout();
        $response->redirect(BASE_URL . 'index.php?page=home');
        break;
    case 'dashboard':
        $auth = new Auth($db);
        $auth->require();
        require 'pages/dashboard.php';
        break;
    case 'portfolio':
        $auth = new Auth($db);
        $auth->require();
        require 'pages/portfolio.php';
        break;
    case 'settings':
        $auth = new Auth($db);
        $auth->require();
        require 'pages/settings.php';
        break;
    default:
        require 'pages/home.php';
}

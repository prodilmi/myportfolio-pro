<?php
/**
 * Authentication API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Auth;
use App\Core\Request;

$auth = new Auth($db);
$action = Request::getQuery('action');
http_response_code(200);

switch ($action) {
    case 'login':
        $email = Request::getPost('email');
        $password = Request::getPost('password');
        
        if (!$email || !$password) {
            header('Location: ' . BASE_URL . 'index.php?page=login&error=' . urlencode('Email and password required'));
            exit;
        }
        
        $result = $auth->login($email, $password);
        if ($result['success']) {
            header('Location: ' . BASE_URL . 'index.php?page=dashboard');
        } else {
            header('Location: ' . BASE_URL . 'index.php?page=login&error=' . urlencode($result['message']));
        }
        exit;
        
    case 'register':
        $email = Request::getPost('email');
        $password = Request::getPost('password');
        $password_confirm = Request::getPost('password_confirm');
        $first_name = Request::getPost('first_name');
        $last_name = Request::getPost('last_name');
        
        if (!$email || !$password || !$first_name || !$last_name) {
            header('Location: ' . BASE_URL . 'index.php?page=register&error=' . urlencode('All fields are required'));
            exit;
        }
        
        if ($password !== $password_confirm) {
            header('Location: ' . BASE_URL . 'index.php?page=register&error=' . urlencode('Passwords do not match'));
            exit;
        }
        
        if (strlen($password) < 6) {
            header('Location: ' . BASE_URL . 'index.php?page=register&error=' . urlencode('Password must be at least 6 characters'));
            exit;
        }
        
        $result = $auth->register($email, $password, $first_name, $last_name);
        if ($result['success']) {
            header('Location: ' . BASE_URL . 'index.php?page=dashboard');
        } else {
            header('Location: ' . BASE_URL . 'index.php?page=register&error=' . urlencode($result['message']));
        }
        exit;
        
    case 'logout':
        $auth->logout();
        header('Location: ' . BASE_URL);
        exit;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}

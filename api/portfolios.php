<?php
/**
 * Portfolio API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;

$auth = new Auth($db);
if (!$auth->isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user = $auth->getUser();
$method = Request::getMethod();
$action = Request::getQuery('action');

$response = new Response();

switch ($method) {
    case 'GET':
        if ($action === 'list') {
            $portfolios = $db->findAll('portfolios', ['user_id' => $user['id']]);
            echo json_encode(['success' => true, 'data' => $portfolios]);
        } elseif ($action === 'get') {
            $id = Request::getQuery('id');
            $portfolio = $db->findOne('portfolios', ['id' => $id, 'user_id' => $user['id']]);
            if ($portfolio) {
                echo json_encode(['success' => true, 'data' => $portfolio]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Portfolio not found']);
            }
        }
        break;

    case 'POST':
        if ($action === 'create') {
            $data = json_decode(file_get_contents('php://input'), true);
            $data['user_id'] = $user['id'];
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $portfolio_id = $db->insert('portfolios', $data);
            echo json_encode(['success' => true, 'id' => $portfolio_id]);
        }
        break;

    case 'PUT':
        if ($action === 'update') {
            $id = Request::getQuery('id');
            $portfolio = $db->findOne('portfolios', ['id' => $id, 'user_id' => $user['id']]);
            
            if ($portfolio) {
                $data = json_decode(file_get_contents('php://input'), true);
                $data['updated_at'] = date('Y-m-d H:i:s');
                
                $db->update('portfolios', $data, ['id' => $id]);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Portfolio not found']);
            }
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $id = Request::getQuery('id');
            $portfolio = $db->findOne('portfolios', ['id' => $id, 'user_id' => $user['id']]);
            
            if ($portfolio) {
                $db->delete('portfolios', ['id' => $id]);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Portfolio not found']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

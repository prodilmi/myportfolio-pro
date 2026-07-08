<?php
/**
 * Holdings API Handler
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

switch ($method) {
    case 'GET':
        if ($action === 'list') {
            $portfolio_id = Request::getQuery('portfolio_id');
            
            // Verify user owns this portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(404);
                echo json_encode(['error' => 'Portfolio not found']);
                break;
            }
            
            $holdings = $db->findAll('holdings', ['portfolio_id' => $portfolio_id]);
            echo json_encode(['success' => true, 'data' => $holdings]);
        }
        break;

    case 'POST':
        if ($action === 'create') {
            $data = json_decode(file_get_contents('php://input'), true);
            $portfolio_id = $data['portfolio_id'];
            
            // Verify user owns this portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(404);
                echo json_encode(['error' => 'Portfolio not found']);
                break;
            }
            
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $holding_id = $db->insert('holdings', $data);
            echo json_encode(['success' => true, 'id' => $holding_id]);
        }
        break;

    case 'PUT':
        if ($action === 'update') {
            $id = Request::getQuery('id');
            $holding = $db->findOne('holdings', ['id' => $id]);
            
            if (!$holding) {
                http_response_code(404);
                echo json_encode(['error' => 'Holding not found']);
                break;
            }
            
            // Verify user owns the portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $holding['portfolio_id'], 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $db->update('holdings', $data, ['id' => $id]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $id = Request::getQuery('id');
            $holding = $db->findOne('holdings', ['id' => $id]);
            
            if (!$holding) {
                http_response_code(404);
                echo json_encode(['error' => 'Holding not found']);
                break;
            }
            
            // Verify user owns the portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $holding['portfolio_id'], 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                break;
            }
            
            $db->delete('holdings', ['id' => $id]);
            echo json_encode(['success' => true]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

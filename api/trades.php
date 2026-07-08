<?php
/**
 * Trading History API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Request;
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
            
            $trades = $db->findAll('trades', ['portfolio_id' => $portfolio_id], ['order_by' => 'trade_date DESC']);
            echo json_encode(['success' => true, 'data' => $trades]);
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
            
            $trade_id = $db->insert('trades', $data);
            echo json_encode(['success' => true, 'id' => $trade_id]);
        }
        break;

    case 'PUT':
        if ($action === 'update') {
            $id = Request::getQuery('id');
            $trade = $db->findOne('trades', ['id' => $id]);
            
            if (!$trade) {
                http_response_code(404);
                echo json_encode(['error' => 'Trade not found']);
                break;
            }
            
            // Verify user owns the portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $trade['portfolio_id'], 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $db->update('trades', $data, ['id' => $id]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $id = Request::getQuery('id');
            $trade = $db->findOne('trades', ['id' => $id]);
            
            if (!$trade) {
                http_response_code(404);
                echo json_encode(['error' => 'Trade not found']);
                break;
            }
            
            // Verify user owns the portfolio
            $portfolio = $db->findOne('portfolios', ['id' => $trade['portfolio_id'], 'user_id' => $user['id']]);
            if (!$portfolio) {
                http_response_code(403);
                echo json_encode(['error' => 'Forbidden']);
                break;
            }
            
            $db->delete('trades', ['id' => $id]);
            echo json_encode(['success' => true]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

<?php
/**
 * Analytics API Handler
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
$action = Request::getQuery('action');

switch ($action) {
    case 'summary':
        $portfolios = $db->findAll('portfolios', ['user_id' => $user['id']]);
        
        $total_invested = 0;
        $current_value = 0;
        
        foreach ($portfolios as $portfolio) {
            $total_invested += $portfolio['total_invested'];
            $current_value += $portfolio['current_value'];
        }
        
        $gain_loss = $current_value - $total_invested;
        $return_percent = $total_invested > 0 ? ($gain_loss / $total_invested) * 100 : 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_invested' => $total_invested,
                'current_value' => $current_value,
                'gain_loss' => $gain_loss,
                'return_percent' => $return_percent,
                'portfolio_count' => count($portfolios)
            ]
        ]);
        break;

    case 'performance':
        $portfolio_id = Request::getQuery('portfolio_id');
        $portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);
        
        if (!$portfolio) {
            http_response_code(404);
            echo json_encode(['error' => 'Portfolio not found']);
            break;
        }
        
        // Get performance data
        $performance = $db->findAll('performance_history', [
            'portfolio_id' => $portfolio_id
        ], ['order_by' => 'date ASC']);
        
        echo json_encode(['success' => true, 'data' => $performance]);
        break;

    case 'allocation':
        $portfolio_id = Request::getQuery('portfolio_id');
        $portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);
        
        if (!$portfolio) {
            http_response_code(404);
            echo json_encode(['error' => 'Portfolio not found']);
            break;
        }
        
        // Get holdings allocation
        $holdings = $db->findAll('holdings', ['portfolio_id' => $portfolio_id]);
        
        $allocation = [];
        foreach ($holdings as $holding) {
            $allocation[] = [
                'symbol' => $holding['symbol'],
                'quantity' => $holding['quantity'],
                'current_price' => $holding['current_price'],
                'value' => $holding['quantity'] * $holding['current_price'],
                'percent' => ($holding['quantity'] * $holding['current_price'] / $portfolio['current_value']) * 100
            ];
        }
        
        echo json_encode(['success' => true, 'data' => $allocation]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

<?php
/**
 * Broker Integration API Handler
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Request;
use App\Core\Auth;
use App\Core\Encryption;

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
            $brokers = $db->findAll('broker_connections', ['user_id' => $user['id']]);
            // Don't return sensitive data
            foreach ($brokers as &$broker) {
                unset($broker['api_key'], $broker['api_secret']);
            }
            echo json_encode(['success' => true, 'data' => $brokers]);
        }
        break;

    case 'POST':
        if ($action === 'connect') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            if (!isset($data['broker_name'], $data['account_number'], $data['api_key'], $data['api_secret'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            // Encrypt sensitive data
            $encryption = new Encryption();
            $data['user_id'] = $user['id'];
            $data['api_key'] = $encryption->encrypt($data['api_key']);
            $data['api_secret'] = $encryption->encrypt($data['api_secret']);
            $data['connected_at'] = date('Y-m-d H:i:s');
            $data['status'] = 'connected';
            
            $connection_id = $db->insert('broker_connections', $data);
            echo json_encode(['success' => true, 'id' => $connection_id]);
        } elseif ($action === 'sync') {
            $broker_id = Request::getQuery('broker_id');
            $connection = $db->findOne('broker_connections', ['id' => $broker_id, 'user_id' => $user['id']]);
            
            if (!$connection) {
                http_response_code(404);
                echo json_encode(['error' => 'Broker connection not found']);
                break;
            }
            
            // Simulate sync operation
            $db->update('broker_connections', 
                ['last_sync' => date('Y-m-d H:i:s')],
                ['id' => $broker_id]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Sync initiated for ' . $connection['broker_name'],
                'synced_at' => date('Y-m-d H:i:s')
            ]);
        }
        break;

    case 'DELETE':
        if ($action === 'disconnect') {
            $broker_id = Request::getQuery('broker_id');
            $connection = $db->findOne('broker_connections', ['id' => $broker_id, 'user_id' => $user['id']]);
            
            if (!$connection) {
                http_response_code(404);
                echo json_encode(['error' => 'Broker connection not found']);
                break;
            }
            
            $db->delete('broker_connections', ['id' => $broker_id]);
            echo json_encode(['success' => true, 'message' => 'Broker disconnected successfully']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

<?php
/**
 * Notifications API Handler
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
            $limit = Request::getQuery('limit', 50);
            $notifications = $db->findAll('notifications', 
                ['user_id' => $user['id']], 
                ['order_by' => 'created_at DESC', 'limit' => $limit]
            );
            echo json_encode(['success' => true, 'data' => $notifications]);
        } elseif ($action === 'unread') {
            $notifications = $db->findAll('notifications', 
                ['user_id' => $user['id'], 'read' => false]
            );
            echo json_encode(['success' => true, 'count' => count($notifications)]);
        }
        break;

    case 'POST':
        if ($action === 'mark_read') {
            $notification_id = Request::getQuery('notification_id');
            $notification = $db->findOne('notifications', ['id' => $notification_id, 'user_id' => $user['id']]);
            
            if (!$notification) {
                http_response_code(404);
                echo json_encode(['error' => 'Notification not found']);
                break;
            }
            
            $db->update('notifications', ['read' => true], ['id' => $notification_id]);
            echo json_encode(['success' => true]);
        } elseif ($action === 'mark_all_read') {
            $db->update('notifications', ['read' => true], ['user_id' => $user['id']]);
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        if ($action === 'delete') {
            $notification_id = Request::getQuery('notification_id');
            $notification = $db->findOne('notifications', ['id' => $notification_id, 'user_id' => $user['id']]);
            
            if (!$notification) {
                http_response_code(404);
                echo json_encode(['error' => 'Notification not found']);
                break;
            }
            
            $db->delete('notifications', ['id' => $notification_id]);
            echo json_encode(['success' => true]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

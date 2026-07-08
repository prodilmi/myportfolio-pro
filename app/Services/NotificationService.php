<?php
/**
 * Notification Service
 */

namespace App\Services;

class NotificationService {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create a notification
     */
    public function create($user_id, $title, $message, $type = 'info', $data = []) {
        return $this->db->insert('notifications', [
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => json_encode($data),
            'read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Send price alert
     */
    public function sendPriceAlert($user_id, $symbol, $currentPrice, $threshold) {
        $title = "Price Alert: $symbol";
        $message = "$symbol has reached $currentPrice (threshold: $threshold)";
        return $this->create($user_id, $title, $message, 'alert', [
            'symbol' => $symbol,
            'price' => $currentPrice,
            'threshold' => $threshold
        ]);
    }
    
    /**
     * Send performance alert
     */
    public function sendPerformanceAlert($user_id, $portfolio_id, $performance, $change_percent) {
        $title = "Portfolio Performance Update";
        $message = "Your portfolio has changed by " . number_format($change_percent, 2) . "%";
        return $this->create($user_id, $title, $message, 'info', [
            'portfolio_id' => $portfolio_id,
            'performance' => $performance,
            'change_percent' => $change_percent
        ]);
    }
    
    /**
     * Send trade confirmation
     */
    public function sendTradeConfirmation($user_id, $symbol, $type, $quantity, $price) {
        $title = "Trade Confirmation: $type $symbol";
        $message = "You have $type " . number_format($quantity, 2) . " shares of $symbol at $" . number_format($price, 2);
        return $this->create($user_id, $title, $message, 'success', [
            'symbol' => $symbol,
            'type' => $type,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }
    
    /**
     * Get unread count
     */
    public function getUnreadCount($user_id) {
        $notifications = $this->db->findAll('notifications', [
            'user_id' => $user_id,
            'read' => false
        ]);
        return count($notifications);
    }
    
    /**
     * Mark as read
     */
    public function markAsRead($notification_id) {
        return $this->db->update('notifications', 
            ['read' => true],
            ['id' => $notification_id]
        );
    }
    
    /**
     * Delete notification
     */
    public function delete($notification_id) {
        return $this->db->delete('notifications', ['id' => $notification_id]);
    }
}

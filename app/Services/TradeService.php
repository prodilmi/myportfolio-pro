<?php
/**
 * Trade Service
 */

namespace App\Services;

class TradeService {
    private $db;
    private $notificationService;
    
    public function __construct($database, NotificationService $notificationService = null) {
        $this->db = $database;
        $this->notificationService = $notificationService;
    }
    
    /**
     * Record a trade
     */
    public function recordTrade($portfolio_id, $symbol, $type, $quantity, $price, $fees = 0, $trade_date = null) {
        if (!$trade_date) {
            $trade_date = date('Y-m-d');
        }
        
        $trade_id = $this->db->insert('trades', [
            'portfolio_id' => $portfolio_id,
            'symbol' => strtoupper($symbol),
            'type' => strtoupper($type),
            'quantity' => $quantity,
            'price' => $price,
            'fees' => $fees,
            'trade_date' => $trade_date,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Update portfolio value
        $this->updatePortfolioValue($portfolio_id);
        
        return $trade_id;
    }
    
    /**
     * Update portfolio value based on trades
     */
    public function updatePortfolioValue($portfolio_id) {
        $trades = $this->db->findAll('trades', ['portfolio_id' => $portfolio_id]);
        
        $total_invested = 0;
        $holdings = [];
        
        foreach ($trades as $trade) {
            $symbol = $trade['symbol'];
            $total_cost = $trade['quantity'] * $trade['price'] + $trade['fees'];
            
            if (!isset($holdings[$symbol])) {
                $holdings[$symbol] = [
                    'quantity' => 0,
                    'total_cost' => 0
                ];
            }
            
            if ($trade['type'] === 'BUY') {
                $holdings[$symbol]['quantity'] += $trade['quantity'];
                $holdings[$symbol]['total_cost'] += $total_cost;
            } else {
                $holdings[$symbol]['quantity'] -= $trade['quantity'];
                // Remove from total cost proportionally
                if ($holdings[$symbol]['quantity'] >= 0) {
                    $avg_cost = $holdings[$symbol]['total_cost'] / ($holdings[$symbol]['quantity'] + $trade['quantity']);
                    $holdings[$symbol]['total_cost'] -= $trade['quantity'] * $avg_cost;
                }
            }
        }
        
        // Calculate total invested
        foreach ($holdings as $holding) {
            if ($holding['quantity'] > 0) {
                $total_invested += $holding['total_cost'];
            }
        }
        
        // TODO: Get current market prices and calculate current value
        $current_value = $total_invested; // Placeholder
        
        return $this->db->update('portfolios',
            [
                'total_invested' => $total_invested,
                'current_value' => $current_value,
                'updated_at' => date('Y-m-d H:i:s')
            ],
            ['id' => $portfolio_id]
        );
    }
    
    /**
     * Get realized gains/losses
     */
    public function getRealizedGains($portfolio_id) {
        $trades = $this->db->findAll('trades', ['portfolio_id' => $portfolio_id]);
        
        $realized_gains = 0;
        $cost_basis = [];
        
        foreach ($trades as $trade) {
            $symbol = $trade['symbol'];
            
            if (!isset($cost_basis[$symbol])) {
                $cost_basis[$symbol] = [];
            }
            
            if ($trade['type'] === 'BUY') {
                $cost_basis[$symbol][] = [
                    'quantity' => $trade['quantity'],
                    'price' => $trade['price'],
                    'fees' => $trade['fees']
                ];
            } else {
                // FIFO method
                $remaining_quantity = $trade['quantity'];
                while ($remaining_quantity > 0 && !empty($cost_basis[$symbol])) {
                    $lot = array_shift($cost_basis[$symbol]);
                    $quantity_sold = min($remaining_quantity, $lot['quantity']);
                    
                    $cost_per_share = $lot['price'] + ($lot['fees'] / $lot['quantity']);
                    $gain = ($trade['price'] * $quantity_sold) - ($cost_per_share * $quantity_sold) - $trade['fees'];
                    $realized_gains += $gain;
                    
                    $remaining_quantity -= $quantity_sold;
                    if ($lot['quantity'] > $quantity_sold) {
                        $lot['quantity'] -= $quantity_sold;
                        array_unshift($cost_basis[$symbol], $lot);
                    }
                }
            }
        }
        
        return $realized_gains;
    }
    
    /**
     * Get trades for a portfolio
     */
    public function getPortfolioTrades($portfolio_id, $limit = 50) {
        return $this->db->findAll('trades',
            ['portfolio_id' => $portfolio_id],
            ['order_by' => 'trade_date DESC', 'limit' => $limit]
        );
    }
}

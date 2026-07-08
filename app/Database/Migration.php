<?php
/**
 * Database Migration Runner
 */

namespace App\Database;

class Migration {
    private $db;
    
    public function __construct($connection) {
        $this->db = $connection;
    }
    
    /**
     * Run all migrations
     */
    public function runAll() {
        $this->createUsersTable();
        $this->createPortfoliosTable();
        $this->createHoldingsTable();
        $this->createTradesTable();
        $this->createBrokerConnectionsTable();
        $this->createNotificationsTable();
        $this->createPerformanceHistoryTable();
    }
    
    private function createUsersTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                avatar_url VARCHAR(255),
                email_verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createPortfoliosTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS portfolios (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                total_invested DECIMAL(15, 2) DEFAULT 0,
                current_value DECIMAL(15, 2) DEFAULT 0,
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createHoldingsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS holdings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                portfolio_id INT NOT NULL,
                symbol VARCHAR(50) NOT NULL,
                quantity DECIMAL(10, 4) NOT NULL,
                average_cost DECIMAL(15, 2),
                current_price DECIMAL(15, 2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createTradesTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS trades (
                id INT PRIMARY KEY AUTO_INCREMENT,
                portfolio_id INT NOT NULL,
                symbol VARCHAR(50) NOT NULL,
                type ENUM('BUY', 'SELL') NOT NULL,
                quantity DECIMAL(10, 4) NOT NULL,
                price DECIMAL(15, 2) NOT NULL,
                fees DECIMAL(10, 2) DEFAULT 0,
                profit_loss DECIMAL(15, 2),
                trade_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createBrokerConnectionsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS broker_connections (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                broker_name VARCHAR(100) NOT NULL,
                account_number VARCHAR(100) NOT NULL,
                api_key LONGTEXT NOT NULL,
                api_secret LONGTEXT NOT NULL,
                status VARCHAR(50) DEFAULT 'connected',
                connected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_sync TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createNotificationsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS notifications (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                type VARCHAR(50) DEFAULT 'info',
                data LONGTEXT,
                read BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_created (user_id, created_at),
                INDEX idx_user_read (user_id, read)
            )
        ";
        return $this->db->query($sql);
    }
    
    private function createPerformanceHistoryTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS performance_history (
                id INT PRIMARY KEY AUTO_INCREMENT,
                portfolio_id INT NOT NULL,
                date DATE NOT NULL,
                value DECIMAL(15, 2) NOT NULL,
                gain_loss DECIMAL(15, 2),
                gain_loss_percent DECIMAL(10, 2),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (portfolio_id) REFERENCES portfolios(id) ON DELETE CASCADE,
                UNIQUE KEY unique_portfolio_date (portfolio_id, date)
            )
        ";
        return $this->db->query($sql);
    }
}

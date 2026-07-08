<?php
/**
 * Authentication Class
 */

namespace App\Core;

class Auth {
    private $db;
    const SESSION_KEY = 'user_id';
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Register a new user
     */
    public function register($email, $password, $first_name, $last_name) {
        // Check if user exists
        $existing = $this->db->findOne('users', ['email' => $email]);
        if ($existing) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Create user
        $user_id = $this->db->insert('users', [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'first_name' => $first_name,
            'last_name' => $last_name
        ]);
        
        // Set session
        $_SESSION[self::SESSION_KEY] = $user_id;
        
        return ['success' => true, 'user_id' => $user_id];
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        $user = $this->db->findOne('users', ['email' => $email]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        $_SESSION[self::SESSION_KEY] = $user['id'];
        
        return ['success' => true, 'user_id' => $user['id']];
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        return isset($_SESSION[self::SESSION_KEY]);
    }
    
    /**
     * Get current user
     */
    public function getUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $this->db->findOne('users', ['id' => $_SESSION[self::SESSION_KEY]]);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        unset($_SESSION[self::SESSION_KEY]);
        session_destroy();
    }
}

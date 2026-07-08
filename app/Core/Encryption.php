<?php
/**
 * Encryption Service
 */

namespace App\Core;

class Encryption {
    private $key;
    private $method = 'AES-256-CBC';
    
    public function __construct() {
        $this->key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : hash('sha256', 'default-secret-key');
    }
    
    /**
     * Encrypt data
     */
    public function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
        $encrypted = openssl_encrypt($data, $this->method, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * Decrypt data
     */
    public function decrypt($data) {
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $this->method, $this->key, 0, $iv);
    }
}

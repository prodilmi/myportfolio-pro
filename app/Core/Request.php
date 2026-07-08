<?php
/**
 * Request Handler
 */

namespace App\Core;

class Request {
    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function getQuery($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    public static function getPost($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
    
    public static function getInput($key, $default = null) {
        $json = json_decode(file_get_contents('php://input'), true);
        return $json[$key] ?? $default;
    }
    
    public static function all() {
        if (self::getMethod() === 'POST') {
            return $_POST;
        }
        return $_GET;
    }
}

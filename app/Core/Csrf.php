<?php
/**
 * CSRF Token Protection
 * @package MyPortfolioPro\Core
 */

namespace App\Core;

class Csrf
{
    private const TOKEN_KEY = 'csrf_token';
    private const TOKEN_TIME = 'csrf_token_time';

    public static function generateToken(): string
    {
        Session::start();
        
        if (!Session::has(self::TOKEN_KEY)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::TOKEN_KEY, $token);
            Session::set(self::TOKEN_TIME, time());
        }
        
        return Session::get(self::TOKEN_KEY);
    }

    public static function getToken(): string
    {
        return self::generateToken();
    }

    public static function verifyToken(string $token): bool
    {
        Session::start();
        
        $stored = Session::get(self::TOKEN_KEY);
        $time = Session::get(self::TOKEN_TIME);
        
        if (!$stored || !$time) {
            return false;
        }
        
        // Token expires after 1 hour
        if (time() - $time > 3600) {
            return false;
        }
        
        return hash_equals($stored, $token);
    }

    public static function validate(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? $_GET[CSRF_TOKEN_NAME] ?? '';
        
        if (!self::verifyToken($token)) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}

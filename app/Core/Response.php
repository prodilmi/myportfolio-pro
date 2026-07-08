<?php
/**
 * Response Handler
 */

namespace App\Core;

class Response {
    public static function json($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
    
    public static function success($message, $data = []) {
        return self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }
    
    public static function error($message, $code = 400) {
        return self::json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}

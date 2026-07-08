<?php
/**
 * HTTP Request Handler
 * @package MyPortfolioPro\Core
 */

namespace App\Core;

class Request
{
    public static function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool
    {
        return self::getMethod() === 'POST';
    }

    public static function isGet(): bool
    {
        return self::getMethod() === 'GET';
    }

    public static function isPut(): bool
    {
        return self::getMethod() === 'PUT';
    }

    public static function isDelete(): bool
    {
        return self::getMethod() === 'DELETE';
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function getPost(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public static function getQuery(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function getInput(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function getJsonData(): array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    public static function getHeaders(): array
    {
        return getallheaders();
    }

    public static function getHeader(string $key, string $default = ''): string
    {
        $headers = getallheaders();
        return $headers[$key] ?? $default;
    }

    public static function getIp(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public static function getReferer(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '';
    }
}

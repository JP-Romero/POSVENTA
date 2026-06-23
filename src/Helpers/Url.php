<?php

namespace App\Helpers;

class Url
{
    public static function redirect(string $path): void
    {
        header("Location: " . self::to($path));
        exit;
    }

    public static function to(string $path): string
    {
        return URLROOT . '/' . ltrim($path, '/');
    }

    public static function current(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    public static function previous(): string
    {
        return $_SERVER['HTTP_REFERER'] ?? '/';
    }

    public static function asset(string $path): string
    {
        return URLROOT . '/public/' . ltrim($path, '/');
    }

    public static function action(string $controller, string $method = 'index', array $params = []): string
    {
        $path = strtolower($controller);
        
        if ($method !== 'index') {
            $path .= '/' . strtolower($method);
        }
        
        if (!empty($params)) {
            $path .= '/' . implode('/', $params);
        }
        
        return self::to($path);
    }
}

<?php

namespace App\Helpers;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function flash(string $key, $message, string $type = 'alert alert-info'): void
    {
        self::set("flash_{$key}", [
            'message' => $message,
            'type' => $type
        ]);
    }

    public static function getFlash(string $key): ?array
    {
        $flash = self::get("flash_{$key}");
        if ($flash) {
            self::remove("flash_{$key}");
        }
        return $flash;
    }

    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    public static function isAdmin(): bool
    {
        return self::get('user_rol') == 1;
    }

    public static function user(): ?object
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return (object) [
            'id' => self::get('user_id'),
            'usuario' => self::get('user_usuario'),
            'nombre' => self::get('user_nombre'),
            'rol' => self::get('user_rol'),
        ];
    }
}

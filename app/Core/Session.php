<?php

namespace LightMVC\Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function flash($key, $value = null): mixed
    {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
        } else {
            $value = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $value;
        }

        return null;
    }
}

<?php

namespace LightMVC\Core;

use Dotenv\Dotenv;
use LightMVC\Core\Container\Exception;

class Environment
{
    private static bool $isLoaded = false;

    public static function load(string $path): void
    {
        if (self::$isLoaded) {
            return;
        }

        if (!file_exists($path . '/.env')) {
            if (file_exists($path . '/.env.example')) {
                copy($path . '/.env.example', $path . '/.env');
            } else {
                throw new Exception('.env file is missing and no .env.example exists');
            }
        }

        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();

        $dotenv->required([
            'APP_NAME',
            'APP_ENV',
            'APP_KEY',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD'
        ]);

        self::$isLoaded = true;
    }

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_ENV[$key]);
    }
}

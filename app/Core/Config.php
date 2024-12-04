<?php

namespace LightMVC\Core;

class Config
{
    private static array $config = [];
    private static bool $isLoaded = false;

    public static function load(): void
    {
        if (self::$isLoaded) {
            return;
        }

        $configPath = config_path();
        $configFiles = glob($configPath . '/*.php');

        foreach ($configFiles as $file) {
            $key = basename($file, '.php');
            self::$config[$key] = require $file;
        }

        self::$isLoaded = true;
    }

    public static function get(string $key, $default = null)
    {
        self::load();

        $keys = explode('.', $key);
        $config = self::$config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public static function set(string $key, $value): void
    {
        self::load();

        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }
            $config = &$config[$segment];
        }

        $config = $value;
    }

    public static function has(string $key): bool
    {
        return static::get($key) !== null;
    }

    public static function all(): array
    {
        self::load();
        return self::$config;
    }
}

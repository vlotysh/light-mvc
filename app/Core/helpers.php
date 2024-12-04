<?php

use LightMVC\Core\Application;
use LightMVC\Core\Environment;
use LightMVC\Core\Http\FileResponse;
use LightMVC\Core\Http\JsonResponse;
use LightMVC\Core\Http\RedirectResponse;
use LightMVC\Core\Http\Response;

if (!function_exists('response')) {
    function response($content = '', $statusCode = 200, array $headers = [])
    {
        return new Response($content, $statusCode, $headers);
    }
}

if (!function_exists('json')) {
    function json($data = null, $statusCode = 200, array $headers = [])
    {
        return new JsonResponse($data, $statusCode, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302, array $headers = [])
    {
        return new RedirectResponse($url, $statusCode, $headers);
    }
}

if (!function_exists('file_response')) {
    function file_response(string $path, string $name = null, int $statusCode = 200, array $headers = [])
    {
        return new FileResponse($path, $name, $statusCode, $headers);
    }
}


if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('view_path')) {
    function view_path(string $path = ''): string
    {
        return base_path('views') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return Environment::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key = null, $default = null)
    {
        if (is_null($key)) {
            return \LightMVC\Core\Config::all();
        }

        return \LightMVC\Core\Config::get($key, $default);
    }
}

if (!function_exists('app')) {
    function app($abstract = null) {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract);
    }
}

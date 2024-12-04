<?php

namespace LightMVC\Core\Middleware;

class AuthMiddleware extends Middleware
{
    public function handle($request, callable $next)
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        return $next($request);
    }
}

<?php

namespace LightMVC\Core\Middleware;

abstract class Middleware
{
    abstract public function handle($request, callable $next);
}

<?php

namespace LightMVC\Core;

abstract class ServiceProvider
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    abstract public function register(): void;

    public function boot(): void
    {
    }
}

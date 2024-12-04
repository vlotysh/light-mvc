<?php

namespace LightMVC\ServiceProviders;

use LightMVC\Core\Config;
use LightMVC\Core\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Config::load();
    }
}

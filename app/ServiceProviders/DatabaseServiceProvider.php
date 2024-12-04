<?php

namespace LightMVC\ServiceProviders;

use LightMVC\Core\Database;
use LightMVC\Core\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Database::class, function () {
            return Database::getInstance();
        });
    }
}

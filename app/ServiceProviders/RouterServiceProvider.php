<?php

namespace LightMVC\ServiceProviders;

use LightMVC\Core\Routing\Router;
use LightMVC\Core\ServiceProvider;

class RouterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Router::class);
    }

    public function boot(): void
    {
        require base_path('routes/web.php');
    }
}

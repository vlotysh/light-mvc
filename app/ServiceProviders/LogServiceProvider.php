<?php

namespace LightMVC\ServiceProviders;

use LightMVC\Core\ErrorHandler;
use LightMVC\Core\Logger;
use LightMVC\Core\ServiceProvider;
use LightMVC\Core\View;

class LogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Logger::class, function () {
            return new Logger('storage/logs/app.log');
        });

        $this->app->singleton(ErrorHandler::class, function () {
            return new ErrorHandler(
                $this->app->make(Logger::class),
                $this->app->make(View::class),
                config('app.debug'),
            );
        });
    }

    public function boot(): void
    {
        $this->app->make(ErrorHandler::class);
    }
}

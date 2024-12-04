<?php


use LightMVC\Core\Application;
use LightMVC\Core\Environment;
use LightMVC\ServiceProviders\DatabaseServiceProvider;
use LightMVC\ServiceProviders\LogServiceProvider;
use LightMVC\ServiceProviders\RouterServiceProvider;
use LightMVC\ServiceProviders\ConfigServiceProvider;


Environment::load(dirname(__DIR__));

$app = Application::getInstance();
$app->registerProviders([
    ConfigServiceProvider::class,
    DatabaseServiceProvider::class,
    LogServiceProvider::class,
    RouterServiceProvider::class
]);

$app->boot();

return $app;
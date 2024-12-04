<?php

use LightMVC\Core\Routing\Router;

$router = Router::getInstance();
$router->get('/', 'IndexController@index');

$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@show');
//$router->get('/users', UserController::class . '@index');
//$router->post('/users', UserController::class . '@store');

<?php

use LightMVC\Core\Kernel;

require_once '../vendor/autoload.php';

$app = require_once __DIR__. '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = \LightMVC\Core\Http\Request::capture()
)->send();
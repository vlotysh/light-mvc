<?php

namespace LightMVC\Core;

use LightMVC\Core\Http\Request;
use LightMVC\Core\Http\Response;
use LightMVC\Core\Routing\Router;

class Kernel
{
    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request): Response
    {
        return Router::getInstance()->dispatch($request);
    }
}
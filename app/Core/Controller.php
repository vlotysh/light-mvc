<?php

namespace LightMVC\Core;

abstract class Controller
{
    protected function view($name, $data = [])
    {
        return response(View::getInstance()->render($name, $data));
    }
}

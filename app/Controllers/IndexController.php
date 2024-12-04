<?php

namespace LightMVC\Controllers;

use LightMVC\Core\Controller;
use LightMVC\Core\Http\Request;
use LightMVC\Core\Session;
use LightMVC\Core\Validator;
use LightMVC\Model\UserModel;
use LightMVC\Services\UserService;

class IndexController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index()
    {
        return $this->view('index', ['title' => 'Home']);
    }
}

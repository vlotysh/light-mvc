<?php

namespace LightMVC\Controllers;

use LightMVC\Core\Controller;
use LightMVC\Core\Http\Request;
use LightMVC\Core\Session;
use LightMVC\Core\Validator;
use LightMVC\Model\UserModel;
use LightMVC\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function show(Request $request, int $id, string $status = null)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response('User not found', 404);
        }

        // Если запрос требует JSON
        if (Request::wantsJson()) {
            return json([
                'status' => 'success',
                'data' => $user
            ]);
        }

        return response($this->view('users.show', ['user' => $user]));
    }

    public function store(Request $request)
    {
        $validator = new Validator();

        if (
            !$validator->validate($request->all(), [
            'name' => 'required|min:2',
            'email' => 'required|email'
            ])
        ) {
            return redirect('/users/create')
                ->with('errors', $validator->getErrors())
                ->with('old', $request->all());
        }

        $user = UserModel::create($request->all());

        return redirect('/users')
            ->with('success', 'User created successfully!');
    }

    public function index()
    {
        $users =  $this->userService->getAllUsers();

        return json([
            'status' => 'success',
            'data' => $users,
            'meta' => [
                'total' => count($users),
                'page' => 1
            ]
        ]);
    }
}

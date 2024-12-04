<?php

namespace LightMVC\Services;

use LightMVC\Repository\UserRepository;

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllUsers()
    {
        return $this->repository->all();
    }
}

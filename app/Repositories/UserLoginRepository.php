<?php

namespace App\Repositories;

use App\Models\UserLogin;
use App\Repositories\Interfaces\UserLoginRepositoryInterface;

class UserLoginRepository implements UserLoginRepositoryInterface
{

    protected $model;

    public function __construct(UserLogin $userLogin)
    {
        $this->model = $userLogin;
    }

    public function register(array $data): UserLogin
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?UserLogin
    {
        return $this->model->where('email', $email)->first();
    }
}

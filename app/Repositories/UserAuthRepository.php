<?php

namespace App\Repositories;

use App\Models\UserAuth;
use App\Repositories\Interfaces\UserAuthRepositoryInterface;

class UserAuthRepository implements UserAuthRepositoryInterface
{


    public function __construct
    (
        protected UserAuth $model
    ) {

    }

    public function register(array $data): UserAuth
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?UserAuth
    {
        return $this->model->where('email', $email)->first();
    }
}

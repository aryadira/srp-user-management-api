<?php

namespace App\Repositories;

use App\Models\UserAuth;
use App\Repositories\Interfaces\UserAuthRepositoryInterface;

class UserAuthRepository implements UserAuthRepositoryInterface
{


    public function __construct
    (
        protected UserAuth $userAuth
    ) {

    }

    public function register(array $data): UserAuth
    {
        return $this->userAuth->create($data);
    }

    public function findByEmail(string $email): ?UserAuth
    {
        return $this->userAuth->where('email', $email)->first();
    }

    public function findByUsername(string $username): ?UserAuth
    {
        return $this->userAuth->where('username', $username)->first();
    }
}

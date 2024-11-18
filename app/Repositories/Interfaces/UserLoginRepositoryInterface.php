<?php

namespace App\Repositories\Interfaces;

use App\Models\UserLogin;

interface UserLoginRepositoryInterface
{
    public function register(array $data): UserLogin;
    public function findByEmail(string $email): ?UserLogin;
}

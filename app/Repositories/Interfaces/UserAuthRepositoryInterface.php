<?php

namespace App\Repositories\Interfaces;

use App\Models\UserAuth;

interface UserAuthRepositoryInterface
{
    public function register(array $data): UserAuth;
    public function findByEmail(string $email): ?UserAuth;
    public function findByUsername(string $username): ?UserAuth;
}

<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $user
    ) {

    }

    public function all(): Collection
    {
        return $this->user->all();
    }

    public function find(string $id): ?User
    {
        return $this->user->find($id);
    }

    public function create(array $data): User
    {
        return $this->user->create($data);
    }

    public function update(string $id, array $data): ?User
    {
        $user = $this->user->find($id);

        if ($user) {
            $user->update($data);
        }

        return null;
    }

    public function delete(string $id): bool
    {
        return (bool) $this->user->destroy($id);
    }
}


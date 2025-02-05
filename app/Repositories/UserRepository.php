<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {

    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ?User
    {
        $user = $this->model->find($id);

        if ($user) {
            $user->update($data);
        }

        return null;
    }

    public function delete(string $id): bool
    {
        return (bool) $this->model->destroy($id);
    }
}


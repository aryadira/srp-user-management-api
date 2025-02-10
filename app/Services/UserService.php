<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAuth;
use App\Models\UserLog;
use App\Models\UserRole;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserAuthService $userAuthService
    ) {

    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function getAllCustomers(): Collection
    {
        return $this->userRepository->all()->whereNotIn('user_role_id', [1, 2])->values();
    }

    public function findUserById($id)
    {
        return $this->userRepository->find($id);
    }

    // jadi satu sama register di UserAuthService
    public function createUser($data)
    {
        return $this->userAuthService->register($data);
    }

    public function updateUser($user, $id)
    {
        $existingUser = $this->userRepository->find($id);

        if (!empty($existingUser)) {
            $existingUser->update($user);
        }

        return null;
    }

    public function softDeleteUser($id)
    {
        $existingUser = $this->userRepository->find($id);

        if (!$existingUser) {
            return false;
        }

        if ($existingUser->trashed()) {
            return false;
        }

        $userAuth = UserAuth::where('user_id', $existingUser->id)->first();

        if ($userAuth) {
            $this->userAuthService->generateAuthLogHistory($userAuth, 'soft deleted');
        }

        $existingUser->delete();

        return true;
    }

    public function forceDeleteUser($id)
    {
        $existingUser = $this->userRepository->find($id);

        if (!$existingUser) {
            return false;
        }

        $existingUser->forceDelete();

        return true;
    }

    public function searchUser($keyword)
    {
        return User::with('user_auth')
            ->where('fullname', 'LIKE', "%{$keyword}%")
            ->orWhereHas('user_auth', function ($query) use ($keyword) {
                $query->where('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('username', 'LIKE', "%{$keyword}%");
            })
            ->paginate(10);
    }

}

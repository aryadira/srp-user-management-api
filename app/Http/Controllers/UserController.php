<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Services\APIService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected APIService $apiService
    ) {
    }

    public function index(): Collection
    {
        return $this->userService->getAllUsers();
    }

    // add user without otp
    public function store(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $newUser = $this->userService->createUser($data);

        return $this->apiService->sendSuccess('User registeration succssfully', $newUser);
    }

    public function show(string $id)
    {
        return $this->userService->findUserById($id);
    }

    public function update(UserUpdateRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();

        $existingUser = $this->userService->findUserById($id);

        if (!$existingUser) {
            return $this->apiService->sendNotFound('User not found, failed to update!');
        }

        $this->userService->updateUser($data, $id);

        return $this->apiService->sendSuccess('User updated successfully!');
    }

    public function destroy(string $id, string $type)
    {
        if (!in_array($type, ['temporary', 'permanent'])) {
            return $this->apiService->sendError('Invalid delete type');
        }

        $isDeleted = false;

        switch ($type) {
            case 'temporary':
                $isDeleted = $this->temporaryDelete($id);
                break;
            case 'permanent':
                $isDeleted = $this->permanentDelete($id);
                break;
        }

        if (!$isDeleted) {
            return $this->apiService->sendNotFound("User not found or already deleted!");
        }

        return $this->apiService->sendSuccess("User $type deleted successfully!");
    }

    public function temporaryDelete(string $id)
    {
        return $this->userService->softDeleteUser($id);
    }

    public function permanentDelete(string $id)
    {
        return $this->userService->forceDeleteUser($id);
    }

}

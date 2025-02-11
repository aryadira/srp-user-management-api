<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Notifications\UserStatusChanged;
use App\Services\APIService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected APIService $apiService
    ) {
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return $this->apiService->sendSuccess('All users', $users);
    }

    // add user without otp
    public function store(UserStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        $newUser = $this->userService->createUser($data);

        return $this->apiService->sendSuccess('User registeration successfully', $newUser);
    }

    public function show(string $id)
    {
        $existingUser = $this->userService->findUserById($id);

        if (!$existingUser) {
            return $this->apiService->sendNotFound('User not found');
        }

        return $this->apiService->sendSuccess("User found", $existingUser);
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
                $isDeleted = $this->userService->softDeleteUser($id);
                break;
            case 'permanent':
                $isDeleted = $this->userService->forceDeleteUser($id);
                break;
        }

        if (!$isDeleted) {
            return $this->apiService->sendNotFound("User not found or already deleted!");
        }

        return $this->apiService->sendSuccess("User $type deleted successfully!");
    }

    public function search(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return $this->apiService->sendError("Keyword is required");
        }

        $users = $this->userService->searchUser($keyword);

        if ($users->isEmpty()) {
            return $this->apiService->sendError("No result found");
        }

        return $this->apiService->sendSuccess("Search result found...", $users);
    }

    public function assignRole($user, $roleId)
    {

    }

    public function changeUserStatus($id, string $status)
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return $this->apiService->sendNotFound("User not found");
        }

        $statusMap = [
            'activate' => ['is_active' => 1, 'message' => 'User is already activated!', 'success' => 'User activated successfully!'],
            'deactivate' => ['is_active' => 0, 'message' => 'User is already deactivated!', 'success' => 'User deactivated successfully!'],
            'block' => ['is_blocked' => 1, 'message' => 'User is already blocked!', 'success' => 'User blocked successfully!'],
            'unblock' => ['is_blocked' => 0, 'message' => 'User is already unblocked!', 'success' => 'User unblocked successfully!'],
        ];

        if (!isset($statusMap[$status])) {
            return $this->apiService->sendError('Invalid status');
        }

        $field = key($statusMap[$status]);
        $value = $statusMap[$status][$field];

        if ($user->$field == $value) {
            return $this->apiService->sendError($statusMap[$status]['message']);
        }

        $user->update([$field => $value]);

        $user->notify(new UserStatusChanged($status));

        return $this->apiService->sendSuccess($statusMap[$status]['success']);
    }

}

<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserLogin;
use App\Repositories\UserLoginRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserLoginService
{
    protected $userLoginRepository;
    protected $userRepository;

    public function __construct(UserLoginRepository $userLoginRepository, User $userRepository)
    {
        $this->userLoginRepository = $userLoginRepository;
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $username = Str::slug($data['fullname'], '_');
        $username = preg_replace('/[^a-z0-9_]/', '', $username);

        $data['password'] = Hash::make($data['password']);

        $userLogin = $this->userLoginRepository->register([
            'username' => $username,
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $userData = $this->userRepository->create([
            'user_login_id' => $userLogin->id,
            'user_role_id' => 1,
            'fullname' => $data['fullname'],
            'date_of_birth' => null,
            'gender' => null,
        ]);

        return compact('userLogin', 'userData');
    }

    public function authenticate($credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }

        return null;
    }

    public function logout()
    {
        Auth::logout();
    }
}

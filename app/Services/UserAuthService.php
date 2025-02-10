<?php

namespace App\Services;

use App\Models\UserAuth;
use App\Models\UserLog;
use App\Repositories\UserAuthRepository;
use App\Repositories\UserRepository;
use App\Traits\CommonUtilitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{
    use CommonUtilitiesTrait;


    public function __construct(
        protected UserAuthRepository $userAuthRepository,
        protected UserRepository $userRepository
    ) {
    }

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $randomNumberId = $this->generateRandomNumberId();
            $username = 'user' . $randomNumberId;

            $data['password'] = Hash::make($data['password']);

            $userData = $this->userRepository->create([
                'user_role_id' => $data['role'] ?? 3, // 3 = customer role default
                'fullname' => $data['fullname'],
                'date_of_birth' => null,
                'gender' => null,
            ]);

            $userAuth = $this->userAuthRepository->register([
                'user_id' => $userData->id,
                'username' => $username,
                'email' => $data['email'],
                'password' => $data['password'],
                'email_verified_at' => now(),
                'is_verified' => 1,
                'user_verified_at' => now()
            ]);

            $this->generateAuthLogHistory($userAuth, action: 'register');

            return compact('userAuth', 'userData');
        });
    }

    public function authenticate($credentials): string|null
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        if (!isset($email, $password)) {
            return null;
        }

        if (!Auth::guard('web')->attempt($credentials)) {
            return null;
        }

        $userAuth = $this->userAuthRepository->findByEmail($email);

        if (!$userAuth || !Hash::check($password, $userAuth->password)) {
            return null;
        }

        if (!$userAuth->is_verified == 1) {
            return null;
        }

        $this->generateAuthLogHistory($userAuth, 'login');

        $token = $userAuth->createToken('auth_token')->plainTextToken;

        return $token;
    }

    public function logout($authUser)
    {
        $this->generateAuthLogHistory($authUser, 'logout');

        return $authUser->currentAccessToken()->delete();
    }

    public function generateAuthLogHistory($userAuth, $action)
    {
        if ($userAuth) {
            $user_id = $userAuth->user_id;
            $username = $userAuth->username;

            $userAuth->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip()
            ]);

            UserLog::create([
                'user_id' => $user_id,
                'action' => $action,
                'description' => "User {$username} {$action}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return null;
    }

}

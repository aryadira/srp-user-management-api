<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAuth;
use App\Models\UserLog;
use App\Repositories\UserAuthRepository;
use App\Traits\CommonUtilitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{
    use CommonUtilitiesTrait;


    public function __construct(
        protected UserAuthRepository $userAuthRepository,
        protected User $userRepository
    ) {
    }

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $randomNumberId = $this->generateRandomNumberId();
            $username = 'user' . $randomNumberId;

            $data['password'] = Hash::make($data['password']);

            $userAuth = $this->userAuthRepository->register([
                'username' => $username,
                'email' => $data['email'],
                'password' => $data['password'],
                'email_verified_at' => now(),
                'is_verified' => 1,
                'user_verified_at' => now()
            ]);

            $userData = $this->userRepository->create([
                'user_auth_id' => $userAuth->id,
                'user_role_id' => $data['role'] ?? 3, // 3 = customer role default
                'fullname' => $data['fullname'],
                'date_of_birth' => null,
                'gender' => null,
            ]);

            $this->generateAuthLogHistory($userAuth, action: 'register');

            return compact('userAuth', 'userData');
        });
    }

    public function authenticate($credentials)
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        if (!isset($email, $password)) {
            return null;
        }

        if (!Auth::attempt($credentials)) {
            return null;
        }

        $userAuth = UserAuth::where('email', $email)->first();

        if (!$userAuth) {
            return null;
        }

        if (!$userAuth->is_verified == 0) {
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
            $userAuth->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip()
            ]);

            UserLog::create([
                'user_auth_id' => $userAuth->id,
                'action' => $action,
                'description' => $userAuth->username,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return null;
    }
}

<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAuth;
use App\Repositories\UserAuthRepository;
use App\Traits\CommonUtilitiesTrait;
use App\Traits\ApiTrait;
use Illuminate\Auth\Events\Registered;
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

            return compact('userAuth', 'userData');
        });
    }

    public function authenticate($credentials)
    {
        if (!isset($credentials['email'], $credentials['password'])) {
            return null;
        }

        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = UserAuth::where('email', $credentials['email'])->first();

        if (!$user) {
            return null;
        }

        if (!$user->is_verified == 0) {
            return null;
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $token;
    }

    public function logout($authUser)
    {
        return $authUser->currentAccessToken()->delete();
    }
}

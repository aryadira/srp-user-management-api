<?php

namespace App\Otp;

use App\Models\UserAuth;
use App\Services\UserAuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use SadiqSalau\LaravelOtp\Contracts\OtpInterface as Otp;

class UserRegistrationOtp implements Otp
{

    /**
     * Constructs Otp class
     */
    public function __construct(
        protected UserAuthService $userAuthService,
        protected array $data
    ) {
    }

    /**
     * Processes the Otp
     *
     * @return mixed
     */
    public function process()
    {
        UserAuth::unguarded(function () {
            return $this->userAuthService->register($this->data);
        });
    }
}

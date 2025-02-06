<?php

namespace App\Http\Controllers;

use App\Helpers\Api;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\UserAuth;
use App\Otp\UserRegistrationOtp;
use App\Repositories\UserAuthRepository;
use App\Services\APIService;
use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use SadiqSalau\LaravelOtp\Facades\Otp;

class UserAuthController extends Controller
{

    public function __construct(
        protected APIService $apiService,
        protected UserAuthService $userAuthService,
        protected UserAuthRepository $userAuthRepository
    ) {

    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $otp = Otp::identifier($data['email'])->send(
            new UserRegistrationOtp($this->userAuthService, $data),
            Notification::route(
                'mail',
                $data['email']
            )
        );

        if ($otp['status'] != Otp::OTP_SENT) {
            return $this->apiService->sendError(Otp::OTP_FAILED_MESSAGE);
        }

        return $this->apiService->sendSuccess(Otp::OTP_SENT_MESSAGE, $otp['status']);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated();

        $authenticate = $this->userAuthService->authenticate($data);

        if (!$authenticate) {
            return $this->apiService->sendUnauthorized('Invalid email or password');
        }

        return $this->apiService->sendSuccess('Login successfully!', [
            'token' => $authenticate
        ]);
    }

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return $this->apiService->sendUnauthorized();
        }

        $this->userAuthService->logout($request->user());

        return $this->apiService->sendSuccess('Logout successfully!');
    }
}


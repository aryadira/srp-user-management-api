<?php

namespace App\Http\Controllers;

use App\Helpers\Api;
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
            return $this->apiService->sendError('Failed to send OTP');
        }

        return $this->apiService->sendSuccess("OTP Sent!", $otp['status']);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'otpCode' => ['required', 'string']
        ]);

        $otp = Otp::identifier($request->email)->attempt($request->otpCode);

        if ($otp['status'] != Otp::OTP_PROCESSED) {
            return $this->apiService->sendForbidden("OTP is invalid!");
        }

        return $this->apiService->sendSuccess("OTP Verified", $this->userAuthRepository->findByEmail($request->email));
    }

    public function login(Request $request)
    {

    }
}


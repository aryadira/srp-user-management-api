<?php

namespace App\Http\Controllers;

use App\Http\Requests\OTPRequest;
use App\Repositories\UserAuthRepository;
use App\Services\APIService;
use App\Services\UserAuthService;
use Illuminate\Http\Request;
use SadiqSalau\LaravelOtp\Facades\Otp;

class OTPController extends Controller
{
    public function __construct(
        protected APIService $apiService,
        protected UserAuthService $userAuthService,
        protected UserAuthRepository $userAuthRepository
    ) {

    }
    public function verify(OTPRequest $request)
    {
        $request->validated();

        $otp = Otp::identifier($request->email)->attempt($request->code);

        if ($otp['status'] != Otp::OTP_PROCESSED) {
            return $this->apiService->sendForbidden("OTP is invalid!");
        }

        $newAuthUser = $this->userAuthRepository->findByEmail($request->email);

        return $this->apiService->sendSuccess("OTP Verified", $newAuthUser);
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255']
        ]);

        $otp = Otp::identifier($request->email)->update();

        if ($otp['status'] != Otp::OTP_SENT) {
            return $this->apiService->sendForbidden("Failed to send OTP!");
        }

        return $this->apiService->sendSuccess("OTP Sent!", $otp['status']);
    }
}

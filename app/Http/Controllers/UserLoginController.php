<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserLoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{
    protected $userLoginService;

    public function __construct(UserLoginService $userLoginService)
    {
        $this->userLoginService = $userLoginService;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $response = $this->userLoginService->register($data);

        return ApiResponse::response("success", "User registered successfully", $response, 201);
    }

    public function login(Request $request)
    {

    }
}

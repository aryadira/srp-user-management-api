<?php

use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('user/register', [UserAuthController::class, 'register'])->name('user.auth.register');
Route::post('user/login', [UserAuthController::class, 'login'])->name('user.auth.login');

Route::post('otp/verify', [OTPController::class, 'verify'])->name('otp.verify');
Route::post('otp/resend', [OTPController::class, 'resend'])->name('otp.resend');


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return User::limit(10)->get();
    });
});

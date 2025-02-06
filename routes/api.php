<?php

use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [UserAuthController::class, 'register'])->name('auth.register');
Route::post('auth/login', [UserAuthController::class, 'login'])->name('auth.login');

Route::post('otp/verify', [OTPController::class, 'verify'])->name('otp.verify');
Route::post('otp/resend', [OTPController::class, 'resend'])->name('otp.resend');


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return User::limit(10)->get();
    });

    Route::delete('auth/logout', [UserAuthController::class, 'logout'])->name('auth.logout');
});

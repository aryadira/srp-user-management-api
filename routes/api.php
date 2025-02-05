<?php

use App\Http\Controllers\UserAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return User::limit(10)->get();
});

Route::post('user/register', [UserAuthController::class, 'register'])->name('user.auth.register');
Route::post('user/login', [UserAuthController::class, 'login'])->name('user.auth.login');

Route::post('otp/verify', [UserAuthController::class, 'verifyOTP'])->name('user.auth.otp.verify');

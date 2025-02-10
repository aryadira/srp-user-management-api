<?php

use App\Http\Controllers\OTPController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [UserAuthController::class, 'register'])->name('register');
    Route::post('login', [UserAuthController::class, 'login'])->name('login');
    Route::delete('logout', [UserAuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
});

Route::prefix('otp')->name('otp.')->group(function () {
    Route::post('verify', [OTPController::class, 'verify'])->name('verify');
    Route::post('resend', [OTPController::class, 'resend'])->name('resend');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/search', [UserController::class, 'search'])->name('search');
            Route::get('{user}', [UserController::class, 'show'])->name('show');
            Route::put('{user}', [UserController::class, 'update'])->name('update');
            Route::delete('{user}/{type}/delete', [UserController::class, 'destroy'])->where('type', 'temporary|permanent')->name('destroy');
            Route::patch('{user}/{status}', [UserController::class, 'changeUserStatus'])->name('change.status');
        });
    });

    Route::middleware('role:customer')->group(function () {

    });
});

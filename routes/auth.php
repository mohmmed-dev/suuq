<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NameController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\OtpVerifyController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [RegisteredUserController::class, 'create'])
        ->name('login');

    Route::post('login', [RegisteredUserController::class, 'store']);

    Route::get('otp/verify', [OtpVerifyController::class, 'show'])
        ->name('otp.verify');

    Route::post('otp/verify', [OtpVerifyController::class, 'store'])->name('otp.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('name', [NameController::class, 'show'])
        ->name('name');

    Route::post('name', [NameController::class, 'store']);
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

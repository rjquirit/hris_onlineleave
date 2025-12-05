<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonnelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Password Reset
Route::post('/password/email', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// OTP
Route::post('/otp/generate', [AuthController::class, 'generateOTP']);
Route::post('/otp/verify', [AuthController::class, 'verifyOTP']);

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Email Verification
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerification']);

    Route::get('personnel/export', [PersonnelController::class, 'exportExcel']);
    Route::get('personnel/pdf', [PersonnelController::class, 'downloadPdf']);
    Route::apiResource('personnel', PersonnelController::class);

    Route::get('permissions', [App\Http\Controllers\RoleController::class, 'getAllPermissions']);
    Route::apiResource('roles', App\Http\Controllers\RoleController::class);

    Route::apiResource('users', App\Http\Controllers\UserController::class);
    Route::post('users/{id}/assign-role', [App\Http\Controllers\UserController::class, 'assignRole']);
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update']);
});

// Email Verification (public route with signed URL)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonnelController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('personnel/export', [PersonnelController::class, 'exportExcel']);
    Route::get('personnel/pdf', [PersonnelController::class, 'downloadPdf']);
    Route::apiResource('personnel', PersonnelController::class);

    Route::get('permissions', [App\Http\Controllers\RoleController::class, 'getAllPermissions']);
    Route::apiResource('roles', App\Http\Controllers\RoleController::class);

    Route::get('users', [App\Http\Controllers\UserController::class, 'index']);
    Route::post('users/{id}/assign-role', [App\Http\Controllers\UserController::class, 'assignRole']);
});

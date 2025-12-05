<?php

use App\Http\Controllers\CustomSocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Employee Dashboard - authenticated users only
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

// Override tyro-login's social auth routes to use our custom controller
// This integrates with our encrypted User model and EncryptionService
Route::get('auth/{provider}/redirect', [CustomSocialAuthController::class, 'redirect'])
    ->name('tyro-login.social.redirect');
Route::get('auth/{provider}/callback', [CustomSocialAuthController::class, 'callback'])
    ->name('tyro-login.social.callback');

// Tyro-Login automatically registers the /login route
// Explicitly name it for auth middleware redirects
Route::get('/login', function () {
    return view('tyro-login::login', ['layout' => config('tyro-login.layout', 'centered')]);
})->name('login');

Route::get('/personnel', function () {
    return view('personnel.index');
});

Route::get('/roles', function () {
    return view('roles.index');
});

Route::get('/users', function () {
    return view('users.index');
});

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');

Route::prefix('leave-card')->group(function () {
    Route::get('/{personnel_id}', [App\Http\Controllers\LeaveCardController::class, 'show'])->name('leave-card.show');
    Route::post('/', [App\Http\Controllers\LeaveCardController::class, 'store'])->name('leave-card.store');
    Route::put('/{id}', [App\Http\Controllers\LeaveCardController::class, 'update'])->name('leave-card.update');
    Route::delete('/{id}', [App\Http\Controllers\LeaveCardController::class, 'destroy'])->name('leave-card.destroy');
    Route::get('/export/excel/{personnel_id}', [App\Http\Controllers\LeaveCardController::class, 'exportExcel'])->name('leave-card.export.excel');
    Route::get('/export/pdf/{personnel_id}', [App\Http\Controllers\LeaveCardController::class, 'exportPdf'])->name('leave-card.export.pdf');
});

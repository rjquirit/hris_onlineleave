<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
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

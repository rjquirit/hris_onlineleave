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

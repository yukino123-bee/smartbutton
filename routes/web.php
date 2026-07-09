<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:NDRRMO'])->group(function () {
    Route::get('/ndrrmo', function () {
        return view('ndrrmo');
    })->name('ndrrmo.dashboard');
});

Route::middleware(['auth', 'role:Clinic'])->group(function () {
    Route::get('/clinic', function () {
        return view('clinic');
    })->name('clinic.dashboard');
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'NDRRMO') {
            return redirect('/ndrrmo');
        } elseif ($role === 'Clinic') {
            return redirect('/clinic');
        }
    }
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:NDRRMO'])->prefix('ndrrmo')->name('ndrrmo.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NdrrmoController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts', [\App\Http\Controllers\NdrrmoController::class, 'alerts'])->name('alerts');
    Route::get('/logs', [\App\Http\Controllers\NdrrmoController::class, 'logs'])->name('logs');
    Route::get('/map', [\App\Http\Controllers\NdrrmoController::class, 'map'])->name('map');
    Route::get('/devices', [\App\Http\Controllers\NdrrmoController::class, 'devices'])->name('devices');
    Route::post('/devices', [\App\Http\Controllers\NdrrmoController::class, 'storeDevice'])->name('devices.store');
    Route::put('/devices/{device}', [\App\Http\Controllers\NdrrmoController::class, 'updateDevice'])->name('devices.update');
    Route::delete('/devices/{device}', [\App\Http\Controllers\NdrrmoController::class, 'destroyDevice'])->name('devices.destroy');
    Route::get('/sms', [\App\Http\Controllers\NdrrmoController::class, 'sms'])->name('sms');
    Route::get('/reports', [\App\Http\Controllers\NdrrmoController::class, 'reports'])->name('reports');
});

Route::middleware(['auth', 'role:Clinic'])->prefix('clinic')->name('clinic.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ClinicController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts', [\App\Http\Controllers\ClinicController::class, 'alerts'])->name('alerts');
    Route::get('/incoming', [\App\Http\Controllers\ClinicController::class, 'incoming'])->name('incoming');
    Route::get('/logs', [\App\Http\Controllers\ClinicController::class, 'logs'])->name('logs');
    Route::get('/patients', [\App\Http\Controllers\ClinicController::class, 'patients'])->name('patients');
    Route::get('/reports', [\App\Http\Controllers\ClinicController::class, 'reports'])->name('reports');
});

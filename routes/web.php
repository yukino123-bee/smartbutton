<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ProfileController;

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
    Route::get('/stats-json', [\App\Http\Controllers\NdrrmoController::class, 'statsJson'])->name('stats-json');
    Route::get('/reports/export-excel', [\App\Http\Controllers\NdrrmoController::class, 'exportExcel'])->name('reports.export-excel');
    Route::post('/alerts/bulk-delete', [\App\Http\Controllers\NdrrmoController::class, 'bulkDeleteAlerts'])->name('alerts.bulk-delete');
    Route::post('/incidents/{incident}/acknowledge', [\App\Http\Controllers\NdrrmoController::class, 'acknowledgeIncident'])->name('incidents.acknowledge');
    Route::post('/incidents/{incident}/dispatch', [\App\Http\Controllers\NdrrmoController::class, 'dispatchIncident'])->name('incidents.dispatch');
    Route::post('/incidents/{incident}/resolve', [\App\Http\Controllers\NdrrmoController::class, 'resolveIncident'])->name('incidents.resolve');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'role:Clinic'])->prefix('clinic')->name('clinic.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ClinicController::class, 'dashboard'])->name('dashboard');
    Route::get('/alerts', [\App\Http\Controllers\ClinicController::class, 'alerts'])->name('alerts');
    Route::get('/incoming', [\App\Http\Controllers\ClinicController::class, 'incoming'])->name('incoming');
    Route::get('/logs', [\App\Http\Controllers\ClinicController::class, 'logs'])->name('logs');
    Route::get('/patients', [\App\Http\Controllers\ClinicController::class, 'patients'])->name('patients');
    Route::get('/reports', [\App\Http\Controllers\ClinicController::class, 'reports'])->name('reports');
    Route::get('/reports/export-excel', [\App\Http\Controllers\ClinicController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/stats-json', [\App\Http\Controllers\ClinicController::class, 'statsJson'])->name('stats-json');
    Route::post('/alerts/bulk-delete', [\App\Http\Controllers\ClinicController::class, 'bulkDeleteAlerts'])->name('alerts.bulk-delete');
    Route::delete('/alerts/{incident}', [\App\Http\Controllers\ClinicController::class, 'destroyAlert'])->name('alerts.destroy');
    Route::post('/incidents/{incident}/acknowledge', [\App\Http\Controllers\ClinicController::class, 'acknowledgeIncident'])->name('incidents.acknowledge');
    Route::post('/incidents/{incident}/resolve', [\App\Http\Controllers\ClinicController::class, 'resolveIncident'])->name('incidents.resolve');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

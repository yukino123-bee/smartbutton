<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/emergency', [\App\Http\Controllers\Api\DeviceController::class, 'emergency']);
Route::get('/device/status', [\App\Http\Controllers\Api\DeviceController::class, 'status']);
Route::post('/incidents/{incident}/acknowledge', [\App\Http\Controllers\Api\DeviceController::class, 'acknowledge']);
Route::post('/incidents/{incident}/dispatch', [\App\Http\Controllers\Api\DeviceController::class, 'dispatch']);
Route::post('/incidents/{incident}/resolve', [\App\Http\Controllers\Api\DeviceController::class, 'resolve']);
Route::post('/sms-webhook', [\App\Http\Controllers\Api\DeviceController::class, 'smsWebhook']);

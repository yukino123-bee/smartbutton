<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/emergency', [\App\Http\Controllers\Api\DeviceController::class, 'emergency']);
Route::post('/sms-webhook', [\App\Http\Controllers\Api\DeviceController::class, 'smsWebhook']);

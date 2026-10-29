<?php

use App\Http\Controllers\Api\DeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Device-facing endpoints; require X-Device-Token when DEVICE_API_TOKEN is set.
Route::middleware('device.token')->group(function () {
    Route::post('/emergency', [DeviceController::class, 'emergency']);
    Route::get('/device/status', [DeviceController::class, 'status']);
    Route::post('/sms-webhook', [DeviceController::class, 'smsWebhook']);
});

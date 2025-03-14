<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CyleHistoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes to Twilio

Route::prefix('twilio')->group(function () {
    Route::post('/send-otp', [LoginController::class, 'sendOtp']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
});

// API routes for mobile app 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('cycle-histories', [CyleHistoryController::class, 'apiStore']);
    // Other API routes for mobile app...
    Route::get('cycle-histories', [CyleHistoryController::class, 'getMyCycles']);
});
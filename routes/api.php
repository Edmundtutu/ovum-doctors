<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\CyleHistoryController;
use App\Http\Controllers\Api\Auth\PatientAuthController;
use App\Http\Controllers\Api\PatientAppointmentController;
use App\Http\Controllers\Ussd\v1\UssdController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes to Twilio

Route::prefix('twilio')->group(function () {
    Route::post('/send-otp', [LoginController::class, 'sendOtp']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
});

// Public routes - no authentication required
/**
 * This route wil be used by other apps/clients 
 * to authenticate then for access to the data in the central DB
 */
Route::post('/patient/login', [PatientAuthController::class, 'login']);


//Ussd Routes
Route::prefix('ussd')->group(function () {
    Route::post('/', [UssdController::class, 'ussdRequestHandler']);
    // routes for testing
    Route::post('/login', [UssdController::class, 'loginRequest']);
});

// Protected routes - require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Patient cycle history routes
    Route::apiResource('cycle-histories', CyleHistoryController::class);
    Route::post('cycle-histories/sync', [CyleHistoryController::class, 'syncroniseCycleHistoryData']);
    Route::post('cycle-histories/start-period', [CyleHistoryController::class, 'startPeriod']);
    Route::post('cycle-histories/end-period', [CyleHistoryController::class, 'endPeriod']);
    
    // Patient appointment routes
    Route::get('appointments', [PatientAppointmentController::class, 'index']);
    Route::post('appointments', [PatientAppointmentController::class, 'store']);
    Route::get('appointments/{id}', [PatientAppointmentController::class, 'show']);
    Route::post('appointments/{id}/cancel', [PatientAppointmentController::class, 'cancel']);
    Route::get('appointment-slots', [PatientAppointmentController::class, 'getAvailableSlots']);
});



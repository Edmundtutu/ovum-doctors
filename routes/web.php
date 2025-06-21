<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\APi\CyleHistoryController;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome'); // guest welcome page
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'attempt'])->name('login.attempt');
});

Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::get('appointments/today', [AppointmentController::class, 'today'])->name('appointments.today');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::get('/patients/{patient}/verify', [PatientController::class, 'showVerificationForm'])->name('patient.verify.form');
    Route::post('/patient-access/request', [LoginController::class, 'requestPatientAccess'])->name('patient.access.request');
    Route::post('/patient-access/verify', [LoginController::class, 'verifyPatientAccess'])->name('patient.access.verify');
    Route::get('patients/{patient}/appointments', [AppointmentController::class, 'getPatientAppointments'])->name('patients.appointments');
    Route::resource('visits', VisitController::class);
    
    // Nested Lab resources under Visits
    Route::resource('visits.labs', LabController::class);
    
    // Convenience routes for lab actions from patient context
    Route::get('patients/{patient}/labs', [LabController::class, 'index'])->name('patients.labs.index');
    Route::get('patients/{patient}/labs/create', [LabController::class, 'create'])->name('patients.labs.create');
    Route::get('patients/{patient}/labs/export-csv', [LabController::class, 'exportCsv'])->name('patients.labs.export-csv');
    Route::get('patients/{patient}/labs/export-pdf', [LabController::class, 'exportPdf'])->name('patients.labs.export-pdf');
    Route::get('visits/{visit}/labs/{lab}/export-pdf', [LabController::class, 'exportLabPdf'])->name('visits.labs.export-pdf');
    
    Route::get('patients/{patient}/cycle-history', [CyleHistoryController::class, 'forPatient'])
        ->name('patients.cycle-history');
}); 
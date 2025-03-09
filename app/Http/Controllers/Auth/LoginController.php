<?php

namespace App\Http\Controllers\Auth;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class LoginController extends Controller
{
    protected TwilioService $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function attempt(Request $request): JsonResponse
    {
        $request->validate([
            'clinic' => 'required|string',
            'doctor_name' => 'required|string',
            'auth_value' => 'required|string'
        ]);

        // Find the clinic
        $clinic = Clinic::where('name', $request->clinic)->first();
        if (!$clinic) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid clinic name'
            ], 422);
        }

        // Find the doctor
        $doctor = Doctor::where('clinic_id', $clinic->id)
            ->where('name', $request->doctor_name)
            ->first();
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid doctor name'
            ], 422);
        }

        // We now only support password login for doctors
        return $this->handlePasswordLogin($request, $doctor);
    }

    /**
     * Handle password-based login.
     */
    private function handlePasswordLogin(Request $request, Doctor $doctor): JsonResponse
    {
        if (!Hash::check($request->auth_value, $doctor->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password'
            ], 422);
        }

        // Log the user in and create session
        Auth::guard('doctor')->login($doctor);
        $request->session()->regenerate();
        session(['auth_type' => 'password']);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => route('dashboard')
        ]);
    }

    /**
     * Send an OTP to verify access to specific patient data.
     * This is called when a doctor attempts to access patient details.
     * 
     * @param Request $request Contains patient_id and patient's phone
     * @return JsonResponse
     */
    public function requestPatientAccess(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'phone' => 'required|string',
        ]);

        try {
            $patientId = $request->input('patient_id');
            
            // Send OTP to patient's phone
            $verification = $this->twilioService->sendOTP($request->input('phone'));
            
            // Store the patient ID we're trying to access in session
            session(['pending_patient_access' => $patientId]);
            
            return response()->json([
                'success' => true,
                'status' => $verification->status,
                'message' => 'OTP sent to patient. Please enter the code.',
                'patient_id' => $patientId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send OTP.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP and grant access to specific patient data.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPatientAccess(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'phone' => 'required|string',
            'code' => 'required|string',
        ]);

        $patientId = $request->input('patient_id');
        $pendingPatientId = session('pending_patient_access');

        // Verify we're accessing the same patient we requested OTP for
        if ($pendingPatientId != $patientId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid patient access request'
            ], 400);
        }

        try {
            $verificationCheck = $this->twilioService->verifyOTP(
                $request->input('phone'),
                $request->input('code')
            );

            if ($verificationCheck->status === 'approved') {
                // Grant access to this specific patient
                $authorizedPatients = session('authorized_patients', []);
                $authorizedPatients[$patientId] = now()->addHours(1); // Grant access for 1 hour
                session(['authorized_patients' => $authorizedPatients]);
                
                // Clear the pending access request
                session()->forget('pending_patient_access');

                return response()->json([
                    'success' => true,
                    'message' => 'Access granted to patient records',
                    'redirect' => route('patients.show', $patientId)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to verify OTP.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if doctor has access to specific patient data.
     * 
     * @param int $patientId
     * @return bool
     */
    public static function hasPatientAccess(int $patientId): bool
    {
        $authorizedPatients = session('authorized_patients', []);
        
        if (!isset($authorizedPatients[$patientId])) {
            return false;
        }
        
        // Check if access has expired
        $expiresAt = $authorizedPatients[$patientId];
        if (now()->gt($expiresAt)) {
            // Access expired, remove it
            unset($authorizedPatients[$patientId]);
            session(['authorized_patients' => $authorizedPatients]);
            return false;
        }
        
        return true;
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('doctor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

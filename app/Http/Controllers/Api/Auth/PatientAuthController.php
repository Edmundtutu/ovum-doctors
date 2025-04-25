<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    /**
     * Authenticate a patient and provide an API token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'email' => 'sometimes|required_without:phoneNumber|email|max:255',
            'passcode' => 'required|string|min:4',
            'device_name' => 'required|string|max:255',
            'phoneNumber' => 'sometimes|required_without:email|string|max:20'
        ]);

        try {
            // Find patient by email or phone
            $patient = $this->findPatient($validated);
            
            if (!$patient) {
                throw ValidationException::withMessages([
                    'email' => ['No account found with these credentials.'],
                ]);
            }

            // Verify passcode
            if (!Hash::check($validated['passcode'], $patient->passcode)) {
                throw ValidationException::withMessages([
                    'passcode' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Create API token
            $token = $patient->createToken($validated['device_name'], ['patient'])->plainTextToken;

            // Prepare patient data
            $patientData = [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'phone' => $patient->phone,
                'doctor_name' => optional($patient->doctor)->name,
                // Add other relevant fields
            ];

            return response()->json([
                'success' => true,
                'token' => $token,
                'patient' => $patientData
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Login error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500);
        }
    }

    /**
     * Find patient by email or phone
     * 
     * @param array $credentials
     * @return Patient|null
     */
    protected function findPatient(array $credentials): ?Patient
    {
        $query = Patient::query();
        
        if (!empty($credentials['email'])) {
            return $query->where('email', $credentials['email'])->first();
        }
        
        if (!empty($credentials['phoneNumber'])) {
            return $query->where('phone', $credentials['phoneNumber'])->first();
        }
        
        return null;
    }

    /**
     * Log the patient out (revoke the token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    
    /**
     * Get the authenticated patient's profile
     */
    public function profile(Request $request)
    {
        $patient = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'doctor_name' => $patient->doctor->name,
                // Add any other fields you want to include
            ]
        ]);
    }
} 
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    /**
     * Authenticate a patient and provide an API token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'passcode' => 'required',
            'device_name' => 'required',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (!$patient || !Hash::check($request->passcode, $patient->passcode)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create a token with abilities based on the patient's permissions
        $token = $patient->createToken($request->device_name, ['patient'])->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'email' => $patient->email,
                'doctor_name' => $patient->doctor->name,
                // Add other patient fields that may be required.
            ]
        ]);
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
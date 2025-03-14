<?php

namespace App\Http\Controllers;

use App\Models\CyleHistory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CyleHistoryController extends Controller
{
    /**
     * Display cycle history data for a specific patient.
     * This is for the doctor's console to view only.
     */
    public function forPatient(Patient $patient): JsonResponse
    {
        $cycleHistory = DB::table('cyle_histories')
            ->where('patient_id', $patient->id)
            ->select(['month', 'cycle_length', 'period_length', 'symptoms'])
            ->orderBy('month')
            ->get();

        return response()->json($cycleHistory);
    }

    /**
     * API endpoint to store cycle history from mobile app.
     * This would be secured with API authentication.
     */
    public function apiStore(Request $request): JsonResponse
    {
        // This would be an API endpoint used by the mobile application
        // For now, just returning a message about the intended usage
        return response()->json([
            'message' => 'This endpoint is reserved for the mobile application'
        ], 403);
    }

    /**
     * Display cycle history data for authenticated user.
     * This is for the patient's mobile app.
     */
    public function getMyCycles(Request $request): JsonResponse
    {
        $cycleHistory = DB::table('cyle_histories')
            ->where('patient_id', auth()->id())
            ->select(['month', 'cycle_length', 'period_length', 'symptoms'])
            ->orderBy('month')
            ->get();

        return response()->json($cycleHistory);
    }
}

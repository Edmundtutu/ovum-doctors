<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Models\CyleHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Filters\PatientAppointmentFiters\CycleHistoryFilter;

class CyleHistoryController extends Controller
{
    /**
     * Display cycle history data for a specific patient.
     * This is for the doctor's console to view only.
     */
    public function forPatient(Patient $patient): JsonResponse
    {
        $filter = new CycleHistoryFilter();
        $queryConditions = $filter->transform(request());
        
        $query = DB::table('cyle_histories')
            ->where('patient_id', $patient->id)
            ->select(['month', 'cycle_length', 'period_length', 'symptoms'])
            ->orderBy('month');

        foreach ($queryConditions as $condition) {
            if ($condition[1] === 'between') {
                $query->whereBetween($condition[0], $condition[2]);
            } else {
                $query->where($condition[0], $condition[1], $condition[2]);
            }
        }

        $cycleHistory = $query->get();
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
     * Get all cycle history entries for the authenticated patient
     */
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        $filter = new CycleHistoryFilter();
        $queryConditions = $filter->transform($request);
        
        $query = CyleHistory::where('patient_id', $patient->id)
            ->orderBy('month', 'desc');

        // Provide for  the within filter condtion if it exsits in index request
        foreach ($queryConditions as $condition) {
            if ($condition[1] === 'between') {
                $query->whereBetween($condition[0], $condition[2]);
            } else {
                $query->where($condition[0], $condition[1], $condition[2]);
            }
        }

        $cycleHistories = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $cycleHistories
        ]);
    }

    
    /**
     * Store a new cycle history entry
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|date_format:Y-m',
            'cycle_length' => 'required|integer',
            'period_length' => 'required|integer',
            'symptoms' => 'nullable|array',
            'symptoms.*' => 'string',
            // Add any other fields you need
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $cycleHistory = new CyleHistory([
            'patient_id' => Auth::id(),
            'month' => $request->month,
            'cycle_length' => $request->cycle_length,
            'period_length' => $request->period_length,
            'symptoms' => $request->symptoms,
            // Add any other fields
        ]);
        
        $cycleHistory->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Cycle history created successfully',
            'data' => $cycleHistory
        ], 201);
    }
    
    /**
     * Get a specific cycle history entry
     */
    public function show(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        $patientId = $patient->id;
        $cycleHistory = CyleHistory::where('id', $id)
            ->where('patient_id', $patientId)
            ->first();
            
        if (!$cycleHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
        
        if ($cycleHistory->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $cycleHistory
        ]);
    }
    
    /**
     * Update a cycle history entry
     */
    public function update(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        $patientId = $patient->id;
        $cycleHistory = CyleHistory::where('id', $id)
            ->where('patient_id', $patientId)
            ->first();
            
        if (!$cycleHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
        
        if ($cycleHistory->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'cycle_length' => 'sometimes|integer',
            'period_length' => 'sometimes|integer',
            'symptoms' => 'nullable|string',
            // Add other fields
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $cycleHistory->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Cycle history updated successfully',
            'data' => $cycleHistory
        ]);
    }
    
    /**
     * Delete a cycle history entry
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        $patientId = $patient->id;
        $cycleHistory = CyleHistory::where('id', $id)
            ->where('patient_id', $patientId)
            ->first();
            
        if (!$cycleHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }
        
        if ($cycleHistory->patient_id !== $patient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $cycleHistory->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Cycle history deleted successfully'
        ]);
    }

    /**
     * Function that will perform a sync with the cycle history data of the user app
     * Uses a batch processing to post many instances of CycleHistory data comming in form the request
     * Employs "Modified First Wins" syncronisation mechanisms for distributed systems
     * @param Request $request
     * @return void
     */
    public function syncroniseCycleHistoryData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'records' => 'required|array',
            'records.*.id' => 'nullable|string',
            'records.*.month' => 'required|date_format:Y-m',
            'records.*.cycle_length' => 'required|integer',
            'records.*.period_length' => 'required|integer',
            'records.*.symptoms' => 'nullable|array',
            'records.*.updated_at' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $patient = $request->user();
        $patientId = $patient->id;
        $processed = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($request->records as $record) {
            $existingRecord = null;
            
            if (!empty($record['id'])) {
                $existingRecord = CyleHistory::where('id', $record['id'])
                    ->where('patient_id', $patientId)
                    ->first();
            } else {
                $existingRecord = CyleHistory::where('patient_id', $patientId)
                    ->where('month', $record['month'])
                    ->first();
            }
            
            // Modified First Wins strategy
            if ($existingRecord) {
                $serverUpdatedAt = new \DateTime($existingRecord->updated_at);
                $clientUpdatedAt = new \DateTime($record['updated_at']);
                
                if ($clientUpdatedAt > $serverUpdatedAt) {
                    $existingRecord->update($record);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                CyleHistory::create([
                    'patient_id' => $patientId,
                    'month' => $record['month'],
                    'cycle_length' => $record['cycle_length'],
                    'period_length' => $record['period_length'],
                    'symptoms' => $record['symptoms'] ?? [],
                    'updated_at' => $record['updated_at'],
                ]);
                $created++;
            }
            $processed++;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Synchronization completed',
            'statistics' => [
                'processed' => $processed,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped
            ]
        ]);
    }
}

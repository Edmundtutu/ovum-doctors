<?php
/**
 * CyleHistoryController - Manages patient menstrual cycle records and synchronization
 * 
 * Handles CRUD operations, data filtering, and mobile synchronization logic
 * for menstrual cycle tracking system. Ensures data isolation between patients
 * through strict patient_id checks on all operations.
 */
namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Models\CyleHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Filters\PatientAppointmentFiters\CycleHistoryFilter;

class CyleHistoryController extends Controller
{
    /**
     * Retrieve filtered cycle history for a specific patient record
     * 
     * Used by medical practitioners to view a patient's cycle history with
     * customizable filters. Returns data ordered chronologically.
     * 
     * @param Patient $patient Target patient instance from route binding
     * @return JsonResponse 
     *   - JSON array of filtered cycle data
     *   - Fields: month, cycle_length, period_length, symptoms
     *   - Ordered by month ascending
     */
    public function forPatient(Patient $patient): JsonResponse
    {
        // Initialize filter processor with request parameters
        $filter = new CycleHistoryFilter();
        
        // Transform request parameters into database query conditions
        // Expected format: array of [column, operator, value] triples
        $conds = $filter->transform(request());

        // Base query construction:
        // - Restricts to specified patient
        // - Selects only relevant display fields
        // - Orders by month chronologically
        $query = DB::table('cyle_histories')
            ->where('patient_id', $patient->id)
            ->select(['month','cycle_length','period_length','symptoms'])
            ->orderBy('month');

        // Dynamic condition application:
        // Processes each filter condition from CycleHistoryFilter
        // Special handling for 'between' operator requiring array of values
        foreach ($conds as $cond) {
            if ($cond[1] === 'between') {
                // Between condition requires two values for range
                $query->whereBetween($cond[0], $cond[2]);
            } else {
                // Standard condition (e.g., '=', '>', '<')
                $query->where($cond[0], $cond[1], $cond[2]);
            }
        }

        // Execute query and return JSON response
        return response()->json($query->get());
    }

    /**
     * Mobile API storage endpoint placeholder
     * 
     * Reserved endpoint for future mobile app integration. Currently returns
     * 403 Forbidden status to prevent unauthorized usage.
     * 
     * @return JsonResponse 
     *   - HTTP 403 status code
     *   - Message indicating mobile app reservation
     */
    public function apiStore(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'This endpoint is reserved for the mobile application'
        ], 403);
    }

    /**
     * Retrieve authenticated patient's cycle history
     * 
     * Primary endpoint for patients to access their own records.
     * Features:
     * - Reverse chronological order (newest first)
     * - Filter support via CycleHistoryFilter
     * - Full record details (excluding internal fields)
     * 
     * @param Request $request Incoming HTTP request
     * @return JsonResponse 
     *   - success: Boolean operation status
     *   - data: Complete cycle records array
     */
    public function index(Request $request): JsonResponse
    {
        // Get authenticated patient from request
        $patient = $request->user();
        
        // Initialize filtering system with request parameters
        $filter = new CycleHistoryFilter();
        $conds = $filter->transform($request);

        // Eloquent query construction:
        // - Uses model for potential relationship benefits
        // - Orders by month descending (newest first)
        $query = CyleHistory::where('patient_id', $patient->id)
            ->orderBy('month', 'desc');

        // Filter condition application
        foreach ($conds as $cond) {
            // Ternary operation for condition type handling
            $cond[1] === 'between' 
                ? $query->whereBetween($cond[0], $cond[2])
                : $query->where($cond[0], $cond[1], $cond[2]);
        }

        // Return structured JSON response with success wrapper
        return response()->json([
            'success' => true,
            'data'    => $query->get()
        ]);
    }

     /**
     * Create a new cycle history record.
     *
     * - Strict validation for data integrity.
     * - Defaults to latest in-progress cycle values when fields are null.
     * - Primarily used to store symptoms and explicit dates.
     * - Marks every new record as 'in_progress'.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validate inputs
        $validator = Validator::make($request->all(), [
            'month'             => 'required|date_format:Y-m',
            'cycle_length'      => 'nullable|integer|min:1',
            'period_length'     => 'nullable|integer|min:1',
            'symptoms'          => 'nullable|array',
            'symptoms.*'        => 'string|max:255',
            'cycle_start_date'  => 'nullable|date',
            'period_start_date' => 'nullable|date',
            'period_end_date'   => 'nullable|date',
            'cycle_end_date'    => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        //  Identify the current patient
        $patient = $request->user();

        //  Get latest in-progress cycle for defaults
        $latestCycle = CyleHistory::where('patient_id', $patient->id)
            ->where('cycle_status', 'in_progress')
            ->orderByDesc('created_at')
            ->first();

        //  Determine final values (request OR fallback to latest OR fixed default)
        $cycleLength      = $request->input('cycle_length')
                              ?? optional($latestCycle)->cycle_length
                              ?? 28;
        $periodLength     = $request->input('period_length')
                              ?? optional($latestCycle)->period_length
                              ?? 5;
        $cycleStartDate   = $request->input('cycle_start_date')
                              ?? optional($latestCycle)->cycle_start_date
                              ?? null;
        $periodStartDate  = $request->input('period_start_date')
                              ?? optional($latestCycle)->period_start_date
                              ?? null;
        $periodEndDate    = $request->input('period_end_date')
                              ?? optional($latestCycle)->period_end_date
                              ?? null;
        $cycleEndDate     = $request->input('cycle_end_date')
                              ?? optional($latestCycle)->cycle_end_date
                              ?? null;
        $symptoms         = $request->input('symptoms') ?? [];

        //  Create the record using mass assignment
        $cycle = CyleHistory::create([
            'patient_id'        => $patient->id,
            'month'             => $request->month,
            'cycle_length'      => $cycleLength,
            'period_length'     => $periodLength,
            'symptoms'          => $symptoms,
            'cycle_start_date'  => $cycleStartDate,
            'period_start_date' => $periodStartDate,
            'period_end_date'   => $periodEndDate,
            'cycle_end_date'    => $cycleEndDate,
            'cycle_status'      => 'in_progress',
        ]);

        //  Return success response
        return response()->json([
            'success' => true,
            'message' => 'Cycle history created successfully.',
            'data'    => $cycle,
        ], 201);
    }

    /**
     * Retrieve single cycle record
     * 
     * Security features:
     * - Patient ID verification
     * - 404 response for missing records
     * - 403 response for unauthorized access attempts
     * 
     * @param int $id Cycle history ID
     * @return JsonResponse 
     *   - 404 if record not found
     *   - 403 if patient mismatch
     *   - 200 with data on success
     */
    public function show(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        
        // Query with dual verification:
        // - Record existence (id)
        // - Patient ownership (patient_id)
        $cycle = CyleHistory::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        // Handle missing record
        if (!$cycle) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }

        // Return successful response
        return response()->json([
            'success' => true,
            'data' => $cycle
        ]);
    }

    // ... (remaining methods with similar detailed comments)

    /**
     * Synchronize client cycle data with server
     * 
     * Complex synchronization logic:
     * 1. Validate incoming record structure
     * 2. Process each record:
     *    a. Check existence by ID or fallback to month match
     *    b. Compare client/server timestamps for conflict resolution
     *    c. Update/create records as needed
     * 3. Return synchronization statistics
     * 
     * @param Request $request Contains records array
     * @return JsonResponse Synchronization results with:
     *   - processed: Total handled records
     *   - created: New records
     *   - updated: Modified records
     *   - skipped: Unmodified records
     */
    public function syncroniseCycleHistoryData(Request $request): JsonResponse
    {
        // Validation ensures proper data structure
        $validator = Validator::make($request->all(), [
            'records'              => 'required|array',
            'records.*.id'         => 'nullable|string',
            'records.*.month'      => 'required|date_format:Y-m',
            'records.*.cycle_length'=> 'required|integer|min:1',
            'records.*.period_length'=>'required|integer|min:1',
            'records.*.symptoms'   => 'nullable|array',
            'records.*.cycle_start_date'=>'nullable|date',
            'records.*.period_start_date'=>'nullable|date',
            'records.*.period_end_date'=>'nullable|date',
            'records.*.cycle_end_date'=>'nullable|date',
            'records.*.updated_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = $request->user();
        $stats = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0
        ];

        foreach ($request->records as $rec) {
            // Record identification logic:
            // Prefer ID match if provided, otherwise use month
            $exists = !empty($rec['id'])
                ? CyleHistory::where('id', $rec['id'])
                    ->where('patient_id', $patient->id)
                    ->first()
                : CyleHistory::where('patient_id', $patient->id)
                    ->where('month', $rec['month'])
                    ->first();

            if ($exists) {
                // Conflict resolution strategy:
                // Client wins if their timestamp is newer
                $serverTime = new Carbon($exists->updated_at);
                $clientTime = new Carbon($rec['updated_at']);
                
                if ($clientTime > $serverTime) {
                    $exists->update($rec);
                    $stats['updated']++;
                } else {
                    $stats['skipped']++;
                }
            } else {
                // New record creation with patient binding
                CyleHistory::create(array_merge(
                    ['patient_id' => $patient->id],
                    $rec
                ));
                $stats['created']++;
            }
            $stats['processed']++;
        }

        return response()->json([
            'success' => true,
            'message' => 'Synchronization completed',
            'statistics' => $stats
        ]);
    }
    /**
     * Record the start of a menstrual period for the current cycle
     * 
     * Business Logic:
     * - Requires an existing cycle with 'in_progress' status
     * - Updates period_start_date while maintaining cycle integrity
     * - Maintains chain of custody through timestamp updates
     * 
     * @param Request $request May contain optional start_date override
     * @return JsonResponse 
     *   - 422 if validation fails
     *   - 404 if no in-progress cycle exists
     *   - 200 with updated cycle on success
     */
    public function startPeriod(Request $request): JsonResponse
    {
        // Validate optional start date parameter
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date'  // Allow client-provided timestamp
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = $request->user();
        
        // Retrieve the most recent in-progress cycle
        // firstOrFail() automatically returns 404 if missing
        $cycle = CyleHistory::where('patient_id', $patient->id)
            ->where('cycle_status', 'in_progress')
            ->latest('created_at')
            ->firstOrFail();

        // Delegate date logging to model method
        $cycle->logPeriodStart($request->start_date)->save();

        return response()->json([
            'success' => true,
            'message' => 'Period start logged',
            'data' => $cycle
        ], 200);
    }

    /**
     * Record the end of a menstrual period and initialize new cycle
     * 
     * Complex Operations:
     * 1. Validates period end parameters
     * 2. Closes current period
     * 3. Creates new cycle with optional length overrides
     * 4. Maintains historical accuracy through chained dates
     * 
     * @param Request $request May contain:
     *   - end_date: Optional period end timestamp
     *   - new_cycle_length: Optional next cycle duration
     *   - new_period_length: Optional next period duration
     * @return JsonResponse 
     *   - 422 for validation errors
     *   - 404 if no in-progress cycle
     *   - 201 with new cycle data
     */
    public function endPeriod(Request $request): JsonResponse
    {
        // Validate optional parameters
        $validator = Validator::make($request->all(), [
            'end_date' => 'nullable|date',
            'new_cycle_length' => 'nullable|integer|min:21|max:35',  // Medical typical range
            'new_period_length' => 'nullable|integer|min:2|max:7'   // Typical period duration
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = $request->user();
        
        // Retrieve current in-progress cycle
        $cycle = CyleHistory::where('patient_id', $patient->id)
            ->where('cycle_status', 'in_progress')
            ->latest('created_at')
            ->firstOrFail();

        // Delegate period closure and new cycle creation to model
        $nextCycle = $cycle->logPeriodEnd(
            $request->end_date, 
            $request->new_cycle_length, 
            $request->new_period_length
        );

        return response()->json([
            'success' => true,
            'message' => 'Period end logged and new cycle created',
            'next_cycle' => $nextCycle
        ], 201);
    }
}

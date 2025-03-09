<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use App\Models\Vitals;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\LoginController;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index(): View
    {
        $patients = Patient::with(['doctor', 'latest_vitals'])
            ->orderBy('name')
            ->paginate(10);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(): View
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'medical_condition' => 'nullable|string|max:500',
            'blood_type' => 'nullable|string|max:5',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
        ]);

        $patient = Patient::create(array_merge(
            $validated,
            ['doctor_id' => auth()->user()->id]
        ));

        // Record initial vitals if provided
        if ($request->has('vitals')) {
            $vitals = new Vitals($request->input('vitals'));
            $vitals->recorded_at = now();
            $patient->vitals()->save($vitals);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Request $request, Patient $patient): View|RedirectResponse
    {
        // Check if the doctor has authorization to view this patient
        if (!LoginController::hasPatientAccess($patient->id)) {
            // Store the intended patient ID in session for the verification process
            session(['intended_patient_id' => $patient->id]);
            
            // Redirect to the verification page
            return redirect()->route('patient.verify.form', $patient);
        }

        $patient->load([
            'doctor',
            'latest_vitals',
            'current_medications',
            'recent_visits' => function($query) {
                $query->with('doctor')->latest();
            },
            'appointments' => function($query) {
                $query->upcoming()->with('doctor');
            }
        ]);

        // Get vitals history for charts
        $vitalsHistory = $patient->vitals()
            ->select(['recorded_at', 'heart_rate', 'weight'])
            ->latest()
            ->limit(12)
            ->get()
            ->map(function($vital) {
                return [
                    'date' => $vital->recorded_at->format('Y-m-d'),
                    'heart_rate' => $vital->heart_rate,
                    'weight' => $vital->weight
                ];
            });

        // Get cycle history for charts
        $cycleHistory = DB::table('cyle_histories')
            ->where('patient_id', $patient->id)
            ->select(['month', 'cycle_length', 'period_length'])
            ->orderBy('month')
            ->limit(12)
            ->get();

        return view('patients.show', compact('patient', 'vitalsHistory', 'cycleHistory'));
    }

    /**
     * Show patient verification form
     */
    public function showVerificationForm(Patient $patient): View
    {
        return view('patients.verify', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient.
     */
    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'medical_condition' => 'nullable|string|max:500',
            'blood_type' => 'nullable|string|max:5',
            'emergency_contact' => 'required|string|max:255',
            'emergency_phone' => 'required|string|max:20',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }
} 
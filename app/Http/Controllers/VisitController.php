<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Vitals;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    /**
     * Display a listing of the visits.
     */
    public function index(): View
    {
        $visits = Visit::with(['patient', 'doctor'])
            ->latest('visit_date')
            ->paginate(10);
            
        return view('visits.index', compact('visits'));
    }

    /**
     * Store a newly created visit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visited_at' => 'required|date',
            'type' => 'required|string',
            'chief_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up' => 'nullable|date_format:Y-m-d',
        ]);

        // Create the visit
        $visit = Visit::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => Auth::id(),
            'visit_date' => $validated['visited_at'],
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment_plan' => $validated['treatment'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'follow_up_date' => $validated['follow_up'] ?? null,
            'status' => 'completed'
        ]);

        // Record vitals if provided
        if ($request->has('vitals')) {
            $vitals = new Vitals($request->input('vitals'));
            $vitals->recorded_at = now();
            $vitals->patient_id = $validated['patient_id'];
            $visit->vitals()->save($vitals);
        }

        return redirect()
            ->route('patients.show', $validated['patient_id'])
            ->with('success', 'Visit recorded successfully.');
    }

    /**
     * Display the specified visit.
     */
    public function show(Visit $visit): View
    {
        $visit->load(['patient', 'doctor', 'vitals']);
        
        return view('visits.show', compact('visit'));
    }

    /**
     * Show the form for editing the specified visit.
     */
    public function edit(Visit $visit): View
    {
        $visit->load(['patient', 'vitals']);
        
        return view('visits.edit', compact('visit'));
    }

    /**
     * Update the specified visit.
     */
    public function update(Request $request, Visit $visit): RedirectResponse
    {
        $validated = $request->validate([
            'visited_at' => 'required|date',
            'type' => 'required|string',
            'chief_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up' => 'nullable|string',
        ]);

        $visit->update([
            'visit_date' => $validated['visited_at'],
            'chief_complaint' => $validated['chief_complaint'] ?? null,
            'diagnosis' => $validated['diagnosis'] ?? null,
            'treatment_plan' => $validated['treatment'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'follow_up_date' => $validated['follow_up'] ?? null,
        ]);

        // Update vitals if provided
        if ($request->has('vitals') && $visit->vitals) {
            $visit->vitals->update($request->input('vitals'));
        } elseif ($request->has('vitals')) {
            $vitals = new Vitals($request->input('vitals'));
            $vitals->recorded_at = now();
            $vitals->patient_id = $visit->patient_id;
            $visit->vitals()->save($vitals);
        }

        return redirect()
            ->route('visits.show', $visit)
            ->with('success', 'Visit updated successfully.');
    }

    /**
     * Remove the specified visit.
     */
    public function destroy(Visit $visit): RedirectResponse
    {
        $patientId = $visit->patient_id;
        $visit->delete();

        return redirect()
            ->route('patients.show', $patientId)
            ->with('success', 'Visit deleted successfully.');
    }
} 
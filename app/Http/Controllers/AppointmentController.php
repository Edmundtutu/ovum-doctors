<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(): View
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->upcoming()
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show today's appointments.
     */
    public function today(): View
    {
        $appointments = Appointment::with(['patient', 'doctor'])
            ->today()
            ->orderBy('start_time')
            ->get();

        return view('appointments.today', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(): View
    {
        $patients = Patient::orderBy('name')->get(['id', 'name']);
        return view('appointments.create', compact('patients'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'type' => 'required|string|max:50',
                'reason' => 'required|string|max:500',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check for conflicting appointments
            $conflicts = $this->checkForConflicts(
                $validated['appointment_date'],
                $validated['start_time'],
                $validated['end_time']
            );

            if ($conflicts) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot conflicts with an existing appointment.'
                    ], 422);
                }

                return back()
                    ->withInput()
                    ->withErrors(['conflict' => 'This time slot conflicts with an existing appointment.']);
            }

            $appointment = Appointment::create(array_merge(
                $validated,
                [
                    'doctor_id' => auth()->guard('doctor')->id(),
                    'status' => 'scheduled'
                ]
            ));

            $appointment->load(['patient', 'doctor']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment created successfully',
                    'event' => [
                        'id' => $appointment->id,
                        'title' => $appointment->patient->name,
                        'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->start_time->format('H:i:s'),
                        'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->end_time->format('H:i:s'),
                        'allDay' => false
                    ],
                    'modalContent' => view('appointments.partials.show-modal-content', compact('appointment'))->render(),
                    'redirect' => route('appointments.index')
                ]);
            }

            return redirect()
                ->route('appointments.index')
                ->with('success', 'Appointment created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient', 'doctor']);
        
        // if request is an ajax for dynamically loading appointment details! then show the Modal
        if(request()->ajax()) {
            return view('appointments.partials.show-modal-content', compact('appointment'));
        }
        
        return view('appointments.show', compact('appointment'));
        
    }

    /**
     * Fetch all appointments for a particular patient.
     */
    public function getPatientAppointments(Request $request, Patient $patient): JsonResponse
    {
        $appointments = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        $events = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => 'Dr. ' . $appointment->doctor->name,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->start_time->format('H:i:s'),
                'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->end_time->format('H:i:s'),
                'allDay' => false,
                'backgroundColor' => $this->getStatusColor($appointment->status),
                'borderColor' => $this->getStatusColor($appointment->status),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'status' => $appointment->status,
                    'type' => $appointment->type,
                    'reason' => $appointment->reason
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Get color based on appointment status
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'confirmed' => '#28a745', // green
            'pending' => '#ffc107',   // yellow
            'cancelled' => '#dc3545', // red
            'completed' => '#6c757d', // gray
            default => '#007bff'      // blue
        };
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment): View
    {
        $patients = Patient::orderBy('name')->get(['id', 'name']);
        
        // If it's an AJAX request, return just the form content
        if (request()->ajax()) {
            return view('appointments.partials.edit-modal-content', compact('appointment', 'patients'));
        }
        
        // Otherwise return the full edit page
        return view('appointments.edit', compact('appointment', 'patients'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse|JsonResponse
    {
        try {   
            $validated = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'appointment_date' => 'required|date|after_or_equal:today',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'type' => 'required|string|max:50',
                'reason' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
                'status' => 'required|in:pending,confirmed,completed,cancelled'
            ]);

            // Check for conflicting appointments (excluding this appointment)
            $conflicts = $this->checkForConflicts(
                $validated['appointment_date'],
                $validated['start_time'],
                $validated['end_time'],
                $appointment->id
            );

            if ($conflicts) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot conflicts with an existing appointment.'
                    ], 422);
                }
                
                return back()
                    ->withInput()
                    ->withErrors(['conflict' => 'This time slot conflicts with an existing appointment.']);
            }

            $appointment->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment updated successfully',
                    'appointment' => $appointment->fresh(['patient', 'doctor']),
                    'event' => [
                        'id' => $appointment->id,
                        'title' => $appointment->patient->name,
                        'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->start_time->format('H:i:s'),
                        'end' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->end_time->format('H:i:s'),
                        'allDay' => false
                    ],
                    'debug' => [
                        'is_ajax' => $request->ajax(),
                        'wants_json' => $request->wantsJson(),
                        'accepts_json' => $request->acceptsJson(),
                        'request_path' => $request->path(),
                    ]
                ]);
            }

            return redirect()
                ->route('appointments.show', $appointment)
                ->with('success', 'Appointment updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment): RedirectResponse|JsonResponse
    {
        $appointment->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully'
            ]);
        }

        return redirect()
            ->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Check for conflicting appointments.
     */
    private function checkForConflicts(string $date, string $start, string $end, ?int $excludeId = null): bool
    {
        $query = Appointment::where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function($query) use ($start, $end) {
                        $query->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Handle appointment rescheduling via AJAX.
     */
    public function reschedule(Request $request, Appointment $appointment): JsonResponse
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        $conflicts = $this->checkForConflicts(
            $request->date('start')->format('Y-m-d'),
            $request->date('start')->format('H:i'),
            $request->date('end')->format('H:i'),
            $appointment->id
        );

        if ($conflicts) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot conflicts with an existing appointment.'
            ], 422);
        }

        $appointment->update([
            'appointment_date' => $request->date('start')->format('Y-m-d'),
            'start_time' => $request->date('start')->format('H:i'),
            'end_time' => $request->date('end')->format('H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully',
            'appointment' => $appointment->fresh(['patient', 'doctor'])
        ]);
    }
} 
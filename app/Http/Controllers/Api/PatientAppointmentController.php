<?php

namespace App\Http\Controllers\Api;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Filters\PatientAppointmentFiters\AppointmentFilter;

class PatientAppointmentController extends Controller
{
    /**
     * Get all appointments for the authenticated patient
     */
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user();
        $filter = new AppointmentFilter();
        $queryConditions = $filter->transform($request);
        
        $query = Appointment::with(['doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc');

        foreach ($queryConditions as $condition) {
            if ($condition[1] === 'between') {
                $query->whereBetween($condition[0], $condition[2]);
            } else {
                $query->where($condition[0], $condition[1], $condition[2]);
            }
        }

        $appointments = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    
    /**
     * Get available appointment slots for a specific doctor or all doctors
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'nullable|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // This assumes you have a method to get available slots
        // We might need to implement this logic based on doctor working hours
        // and existing appointments
        // Or even make use of the Scheducling Algorithm that Irene Developed as a library
        
        // Simple example jsut for testing.:
        $query = Doctor::with('workingHours');
        
        if ($request->has('doctor_id')) {
            $query->where('id', $request->doctor_id);
        }
        
        
        $doctors = $query->get();
        $availableSlots = [];
        
        foreach ($doctors as $doctor) {
            // Get working hours for the requested date
            $dayOfWeek = date('w', strtotime($request->date));
            $workingHoursForDay = $doctor->workingHours->where('day', $dayOfWeek)->first();
            
            if ($workingHoursForDay && $workingHoursForDay->is_working) {
                // Get existing appointments
                $existingAppointments = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $request->date)
                    ->get();
                
                // Calculate available slots
                // (This is a simplified example)
                $doctorSlots = [
                    'doctor_id' => $doctor->id,
                    'doctor_name' => $doctor->name,
                    'date' => $request->date,
                    'slots' => $this->calculateAvailableTimeSlots(
                        $workingHoursForDay->start,
                        $workingHoursForDay->end,
                        $existingAppointments
                    )
                ];
                
                $availableSlots[] = $doctorSlots;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $availableSlots
        ]);
    }
    
    /**
     * Book a new appointment for the patient
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|string|max:50',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Check for conflicting appointments
        $conflictingAppointments = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })
            ->exists();
            
        if ($conflictingAppointments) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is no longer available'
            ], 422);
        }
        
        $patient = $request->user();
        $patientId = $patient->id;
        
        $appointment = new Appointment([
            'doctor_id' => $request->doctor_id,
            'patient_id' => $patientId,
            'appointment_date' => $request->appointment_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'status' => 'pending',
            'reason' => $request->reason,
            'notes' => $request->notes,
        ]);
        
        $appointment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully',
            'data' => $appointment
        ], 201);
    }
    
    /**
     * Get details of a specific appointment
     */
    public function show(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        $patientId = $patient->id;
        
        $appointment = Appointment::with(['doctor'])
            ->where('id', $id)
            ->where('patient_id', $patientId)
            ->first();
            
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }
    
    /**
     * Cancel an appointment
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $patient = $request->user();
        $patientId = $patient->id;
        
        $appointment = Appointment::where('id', $id)
            ->where('patient_id', $patientId)
            ->where('status', '!=', 'completed')
            ->where('appointment_date', '>', now()->addHours(24)->toDateString())
            ->first();
            
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found or cannot be cancelled'
            ], 404);
        }
        
        $appointment->status = 'cancelled';
        $appointment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully'
        ]);
    }
    
    /**
     * Helper method to calculate available time slots
     */
    private function calculateAvailableTimeSlots($startTime, $endTime, $existingAppointments, $slotDuration = 30)
    {
        // Convert times to minutes since midnight for easier calculation
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        
        // Create all possible slots
        $slots = [];
        for ($time = $startMinutes; $time < $endMinutes; $time += $slotDuration) {
            $slotStart = $this->minutesToTime($time);
            $slotEnd = $this->minutesToTime($time + $slotDuration);
            
            $isAvailable = true;
            
            // Check against existing appointments
            foreach ($existingAppointments as $appointment) {
                $apptStart = $this->timeToMinutes($appointment->start_time->format('H:i:s'));
                $apptEnd = $this->timeToMinutes($appointment->end_time->format('H:i:s'));
                
                if (($time >= $apptStart && $time < $apptEnd) || 
                    ($time + $slotDuration > $apptStart && $time + $slotDuration <= $apptEnd) ||
                    ($time <= $apptStart && $time + $slotDuration >= $apptEnd)) {
                    $isAvailable = false;
                    break;
                }
            }
            
            if ($isAvailable) {
                $slots[] = [
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd
                ];
            }
        }
        
        return $slots;
    }
    
    /**
     * Convert time string to minutes since midnight
     */
    private function timeToMinutes($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);
        return $hours * 60 + $minutes;
    }
    
    /**
     * Convert minutes since midnight to time string
     */
    private function minutesToTime($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d:00', $hours, $mins);
    }
}

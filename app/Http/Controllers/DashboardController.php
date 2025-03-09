<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        $data = [
            // Today's appointments count
            'todayAppointments' => Appointment::today()->count(),
            
            // Appointment increase percentage (compared to last week)
            'appointmentIncrease' => $this->calculateAppointmentIncrease(),
            
            // Completed appointments today
            'completedAppointments' => Appointment::today()
                ->where('status', 'completed')
                ->count(),
            
            // Completion rate
            'completionRate' => $this->calculateCompletionRate(),
            
            // Pending appointments
            'pendingAppointments' => Appointment::today()
                ->where('status', 'pending')
                ->count(),
            
            // Next appointment time
            'nextAppointmentIn' => $this->getNextAppointmentTime(),
            
            // Cancelled appointments
            'cancelledAppointments' => Appointment::today()
                ->where('status', 'cancelled')
                ->count(),
            
            // Today's patients
            'todayPatients' => Patient::whereHas('appointments', function($query) {
                $query->today();
            })->with(['appointments' => function($query) {
                $query->today();
            }])->get(),
            
            // Calendar events
            'calendarEvents' => $this->getCalendarEvents(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Calculate the increase in appointments compared to last week.
     */
    private function calculateAppointmentIncrease(): float
    {
        $thisWeek = Appointment::whereBetween('appointment_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $lastWeek = Appointment::whereBetween('appointment_date', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();

        if ($lastWeek === 0) {
            return 100;
        }

        return round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1);
    }

    /**
     * Calculate today's appointment completion rate.
     */
    private function calculateCompletionRate(): float
    {
        $total = Appointment::today()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = Appointment::today()
            ->where('status', 'completed')
            ->count();

        return round(($completed / $total) * 100, 1);
    }

    /**
     * Get the time until the next appointment.
     */
    private function getNextAppointmentTime(): string
    {
        $nextAppointment = Appointment::where('appointment_date', '>=', now())
            ->where('status', 'pending')
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->first();

        if (!$nextAppointment) {
            return 'No pending appointments';
        }

        return $nextAppointment->start_time->diffForHumans();
    }

    /**
     * Get calendar events for the dashboard.
     */
    private function getCalendarEvents(): array
    {
        return Appointment::with(['patient'])
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->name . ' - ' . $appointment->type,
                    'start' => $appointment->start_time->toDateTimeString(),
                    'end' => $appointment->end_time->toDateTimeString(),
                    'className' => 'appointment-' . $appointment->status
                ];
            })
            ->toArray();
    }
} 
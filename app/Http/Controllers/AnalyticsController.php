<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics dashboard.
     */
    public function index(): View
    {
        $data = [
            // Appointment statistics
            'appointmentStats' => $this->getAppointmentStats(),
            
            // Patient demographics
            'patientDemographics' => $this->getPatientDemographics(),
            
            // Visit trends
            'visitTrends' => $this->getVisitTrends(),
            
            // Cycle analytics
            'cycleAnalytics' => $this->getCycleAnalytics(),
        ];

        return view('analytics.index', $data);
    }

    /**
     * Get appointment statistics.
     */
    private function getAppointmentStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return [
            'total' => Appointment::whereBetween('appointment_date', [
                $startOfMonth,
                $endOfMonth
            ])->count(),

            'completed' => Appointment::whereBetween('appointment_date', [
                $startOfMonth,
                $endOfMonth
            ])->where('status', 'completed')->count(),

            'cancelled' => Appointment::whereBetween('appointment_date', [
                $startOfMonth,
                $endOfMonth
            ])->where('status', 'cancelled')->count(),

            'byDay' => Appointment::whereBetween('appointment_date', [
                $startOfMonth,
                $endOfMonth
            ])
            ->select(DB::raw('DATE(appointment_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray(),

            'byType' => Appointment::whereBetween('appointment_date', [
                $startOfMonth,
                $endOfMonth
            ])
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray()
        ];
    }

    /**
     * Get patient demographics.
     */
    private function getPatientDemographics(): array
    {
        return [
            'ageGroups' => Patient::select(
                DB::raw('
                    CASE
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 20 THEN "Under 20"
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 30 THEN "20-29"
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 40 THEN "30-39"
                        WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 50 THEN "40-49"
                        ELSE "50+"
                    END as age_group
                '),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('age_group')
            ->get()
            ->pluck('count', 'age_group')
            ->toArray(),

            'medicalConditions' => Patient::whereNotNull('medical_condition')
                ->select('medical_condition', DB::raw('COUNT(*) as count'))
                ->groupBy('medical_condition')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->pluck('count', 'medical_condition')
                ->toArray()
        ];
    }

    /**
     * Get visit trends.
     */
    private function getVisitTrends(): array
    {
        $lastYear = Carbon::now()->subYear();

        return [
            'byMonth' => Visit::where('visit_date', '>=', $lastYear)
                ->select(
                    DB::raw('DATE_FORMAT(visit_date, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray(),

            'byType' => Visit::where('visit_date', '>=', $lastYear)
                ->select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->orderByDesc('count')
                ->get()
                ->pluck('count', 'type')
                ->toArray()
        ];
    }

    /**
     * Get cycle analytics.
     */
    private function getCycleAnalytics(): array
    {
        return DB::table('cycle_records')
            ->select(
                DB::raw('AVG(cycle_length) as avg_cycle_length'),
                DB::raw('AVG(period_length) as avg_period_length'),
                DB::raw('MIN(cycle_length) as min_cycle_length'),
                DB::raw('MAX(cycle_length) as max_cycle_length'),
                DB::raw('COUNT(*) as total_cycles')
            )
            ->where('recorded_at', '>=', Carbon::now()->subYear())
            ->first()
            ->toArray();
    }
} 
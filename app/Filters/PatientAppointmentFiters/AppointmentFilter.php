<?php
namespace App\Filters\PatientAppointmentFiters;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class AppointmentFilter extends ApiFilter
{
    protected $allowedparams = [
        'appointment_date' => ['eq', 'lt', 'lte', 'gt', 'gte', 'within'],
        'status' => ['eq'],
        'doctor_id' => ['eq']
    ];

    protected $column_Map = [
        'appointment_date' => 'appointment_date',
        'status' => 'status',
        'doctor_id' => 'doctor_id'
    ];
}
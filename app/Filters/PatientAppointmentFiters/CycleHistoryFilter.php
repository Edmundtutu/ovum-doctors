<?php
namespace App\Filters\PatientAppointmentFiters;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class CycleHistoryFilter extends ApiFilter
{
    protected $allowedparams = [
        'month' => ['eq', 'within'],
        'cycle_length' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'period_length' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'cycle_start_date' => ['eq', 'lt', 'lte', 'gt', 'gte', 'within'],
        'cycle_end_date' => ['eq', 'lt', 'lte', 'gt', 'gte', 'within'],
        'period_start_date' => ['eq', 'lt', 'lte', 'gt', 'gte', 'within'],
        'period_end_date' => ['eq', 'lt', 'lte', 'gt', 'gte', 'within']
    ];

    protected $column_Map = [
        'month' => 'month',
        'cycle_length' => 'cycle_length',
        'period_length' => 'period_length',
        'cycle_start_date' => 'cycle_start_date',
        'cycle_end_date' => 'cycle_end_date',
        'period_start_date' => 'period_start_date',
        'period_end_date' => 'period_end_date'
    ];
}

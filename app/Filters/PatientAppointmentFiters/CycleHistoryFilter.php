<?php
namespace App\Filters\PatientAppointmentFiters;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class CycleHistoryFilter extends ApiFilter
{
    protected $allowedparams = [
        'month' => ['eq', 'within'],
        'cycle_length' => ['eq', 'lt', 'lte', 'gt', 'gte'],
        'period_length' => ['eq', 'lt', 'lte', 'gt', 'gte']
    ];

    protected $column_Map = [
        'month' => 'month',
        'cycle_length' => 'cycle_length',
        'period_length' => 'period_length'
    ];
}
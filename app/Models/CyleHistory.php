<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CyleHistory extends Model
{
    /** @use HasFactory<\Database\Factories\CyleHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'month',
        'cycle_length',
        'period_length',
        'symptoms', // This will store an array of symptoms
    ];

    protected $casts = [
        'month' => 'date',  // If this is a date
        'cycle_length' => 'integer',
        'period_length' => 'integer',
        'symptoms' => 'array',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Vitals extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'patient_id',
        'visit_id',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
        'oxygen_saturation',
        'recorded_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recorded_at' => 'datetime',
        'bmi' => 'float',
        'temperature' => 'float',
        'weight' => 'float',
        'height' => 'float',
    ];

    /**
     * Get the patient that the vitals belong to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the visit that the vitals belong to.
     */
    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /**
     * Calculate BMI based on weight and height.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($vitals) {
            if ($vitals->weight && $vitals->height) {
                // Calculate BMI: weight (kg) / (height (m))^2
                $heightInMeters = $vitals->height / 100; // Convert cm to m
                $vitals->bmi = round($vitals->weight / ($heightInMeters * $heightInMeters), 2);
            }
        });
    }

    /**
     * Get the BMI category.
     */
    public function getBmiCategoryAttribute(): string
    {
        if (!$this->bmi) {
            return 'Not calculated';
        }

        if ($this->bmi < 18.5) {
            return 'Underweight';
        } elseif ($this->bmi < 25) {
            return 'Normal weight';
        } elseif ($this->bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    /**
     * Format blood pressure for display.
     */
    public function getFormattedBloodPressureAttribute(): string
    {
        return str_replace('/', ' / ', $this->blood_pressure);
    }

    /**
     * Get the time elapsed since the vitals were recorded.
     */
    public function getTimeElapsedAttribute(): string
    {
        return Carbon::parse($this->recorded_at)->diffForHumans();
    }
} 
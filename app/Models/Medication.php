<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Medication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'patient_id',
        'name',
        'dosage',
        'frequency',
        'start_date',
        'end_date',
        'instructions',
        'prescribed_by',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the patient that the medication belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor who prescribed the medication.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'prescribed_by');
    }

    /**
     * Scope a query to only include active medications.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', Carbon::today());
                    });
    }

    /**
     * Scope a query to only include discontinued medications.
     */
    public function scopeDiscontinued($query)
    {
        return $query->where('status', 'discontinued')
                    ->orWhere(function($q) {
                        $q->where('status', 'active')
                          ->where('end_date', '<', Carbon::today());
                    });
    }

    /**
     * Check if the medication is currently active.
     */
    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (!$this->end_date) {
            return true;
        }

        return Carbon::parse($this->end_date)->isFuture();
    }

    /**
     * Get the duration of medication in days.
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }

        return Carbon::parse($this->start_date)->diffInDays($this->end_date);
    }

    /**
     * Get the remaining days of medication.
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->end_date || !$this->is_active) {
            return null;
        }

        return Carbon::today()->diffInDays($this->end_date, false);
    }
} 
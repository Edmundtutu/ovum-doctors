<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class Visit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'visit_date',
        'chief_complaint',
        'diagnosis',
        'treatment_plan',
        'notes',
        'follow_up_date',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'visit_date' => 'datetime',
        'follow_up_date' => 'date',
    ];

    /**
     * Get the doctor associated with the visit.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient associated with the visit.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the vitals record associated with the visit.
     */
    public function vitals(): HasOne
    {
        return $this->hasOne(Vitals::class);
    }

    /**
     * Scope a query to only include today's visits.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', Carbon::today());
    }

    /**
     * Scope a query to only include visits within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('visit_date', [$startDate, $endDate]);
    }

    /**
     * Check if the visit has a scheduled follow-up.
     */
    public function hasFollowUp(): bool
    {
        return !is_null($this->follow_up_date);
    }

    /**
     * Get the time elapsed since the visit.
     */
    public function getTimeElapsedAttribute(): string
    {
        return Carbon::parse($this->visit_date)->diffForHumans();
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
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
        'appointment_date',
        'start_time',
        'end_time',
        'type',
        'status',
        'notes',
        'reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('appointment_date')
                    ->orderBy('start_time');
    }

    /**
     * Scope a query to only include today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('start_time');
    }

    /**
     * Check if the appointment is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->appointment_date->isFuture();
    }

    /**
     * Check if the appointment is past.
     */
    public function getIsPastAttribute(): bool
    {
        return $this->appointment_date->isPast();
    }

    /**
     * Check if the appointment is today.
     */
    public function getIsTodayAttribute(): bool
    {
        return $this->appointment_date->isToday();
    }
} 
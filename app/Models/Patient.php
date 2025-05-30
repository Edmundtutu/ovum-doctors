<?php

namespace App\Models;

use App\Models\CyleHistory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'doctor_id',
        'name',
        'date_of_birth',
        'email',
        'phone',
        'address',
        'medical_condition',
        'blood_type',
        'emergency_contact',
        'emergency_phone',
        'passcode', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Automatically hash the passcode when setting it
    public function setPasscodeAttribute($value)
    {
        if ($value) {
            $this->attributes['passcode'] = Hash::make($value);
        }
    }

    /**
     * Get the doctor that the patient belongs to.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the cyle histories for the patient.
     */
    public function cyle_histories(): HasOne
    {
        return $this->hasOne(CyleHistory::class)->latestOfMany();
    }

    /**
     * Get the latest vitals for the patient.
     */
    public function latest_vitals(): HasOne
    {
        return $this->hasOne(Vitals::class)->latestOfMany();
    }

    /**
     * Get all vitals history for the patient.
     */
    public function vitals(): HasMany
    {
        return $this->hasMany(Vitals::class);
    }

    /**
     * Get all lab results for the patient through visits.
     */
    public function labs(): HasManyThrough
    {
        return $this->hasManyThrough(Lab::class, Visit::class);
    }

    /**
     * Get the latest visit with lab results for the patient.
     */
    public function latest_visit_with_labs(): HasOne
    {
        return $this->hasOne(Visit::class)
            ->has('labs')
            ->with('labs')
            ->latest();
    }

    /**
     * Get the current medications for the patient.
     */
    public function current_medications(): HasMany
    {
        return $this->hasMany(Medication::class)->where('status', 'active');
    }

    /**
     * Get all medications for the patient.
     */
    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    /**
     * Get the recent visits for the patient.
     */
    public function recent_visits(): HasMany
    {
        return $this->hasMany(Visit::class)->latest()->limit(5);
    }

    /**
     * Get all visits for the patient.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    /**
     * Get the age attribute.
     */
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }
    
}
?>
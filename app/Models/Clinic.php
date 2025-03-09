<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'email',
        'license_number',
    ];

    /**
     * Get the doctors that belong to the clinic.
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
} 
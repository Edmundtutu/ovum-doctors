<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_type',
        'subject_id',
        'type',
        'description',
        'performed_by',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $fillable = [
        'visit_id', 'respiratory_rate', 'hemoglobin',
        'hcg_initial', 'hcg_followup', 'fsh', 'lh',
        'fsh_lh_ratio', 'waist_hip_ratio', 'tsh', 'amh',
        'prolactin', 'vitamin_d3', 'progesterone', 'rbs',
        'bp_systolic', 'bp_diastolic', 'total_follicles',
        'avg_fallopian_size', 'endometrium',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    // Optional: compute FSH/LH ratio if you prefer an accessor
    public function getFshLhRatioAttribute($value)
    {
        return $value ?: round($this->fsh / $this->lh, 2);
    }
}

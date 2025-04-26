<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enable authorization for lab updates
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'respiratory_rate'    => 'sometimes|integer|between:8,30',
            'hemoglobin'          => 'sometimes|numeric|between:8,18',
            'hcg_initial'         => 'sometimes|numeric|min:0',
            'hcg_followup'        => 'nullable|numeric|min:0',
            'fsh'                 => 'sometimes|numeric|min:0',
            'lh'                  => 'sometimes|numeric|min:0',
            'fsh_lh_ratio'        => 'sometimes|numeric|between:0.1,10.0',
            'waist_hip_ratio'     => 'sometimes|numeric|between:0.5,2.0',
            'tsh'                 => 'sometimes|numeric|min:0',
            'amh'                 => 'sometimes|numeric|min:0',
            'prolactin'           => 'sometimes|numeric|min:0',
            'vitamin_d3'          => 'sometimes|numeric|min:0',
            'progesterone'        => 'sometimes|numeric|min:0',
            'rbs'                 => 'sometimes|numeric|min:0',
            'bp_systolic'         => 'sometimes|integer|between:70,200',
            'bp_diastolic'        => 'sometimes|integer|between:40,120',
            'total_follicles'     => 'sometimes|integer|min:0',
            'avg_fallopian_size'  => 'sometimes|numeric|min:0',
            'endometrium'         => 'sometimes|numeric|min:0',
        ];
    }
}

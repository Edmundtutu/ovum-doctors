<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Enable authorization for lab creation
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
            'respiratory_rate'    => 'required|integer',
            'hemoglobin'          => 'required|numeric',
            'hcg_initial'         => 'required|numeric|min:0',
            'hcg_followup'        => 'nullable|numeric|min:0',
            'fsh'                 => 'required|numeric|min:0',
            'lh'                  => 'required|numeric|min:0',
            'fsh_lh_ratio'        => 'required|numeric|between:0.1,10.0',
            'waist_hip_ratio'     => 'required|numeric|between:0.5,2.0',
            'tsh'                 => 'required|numeric|min:0',
            'amh'                 => 'required|numeric|min:0',
            'prolactin'           => 'required|numeric|min:0',
            'vitamin_d3'          => 'required|numeric|min:0',
            'progesterone'        => 'required|numeric|min:0',
            'rbs'                 => 'required|numeric|min:0',
            'bp_systolic'         => 'required|integer',
            'bp_diastolic'        => 'required|integer',
            'total_follicles'     => 'required|integer|min:0',
            'avg_fallopian_size'  => 'required|numeric|min:0',
            'endometrium'         => 'required|numeric|min:0',
        ];
    }
}

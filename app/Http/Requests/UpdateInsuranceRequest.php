<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInsuranceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $insuranceId = $this->input('id');

        return [
            'id' => ['required', 'integer', 'exists:insurances,id'],
            'insurance_code' => ['required', 'string', 'max:255', Rule::unique('insurances', 'insurance_code')->ignore($insuranceId)],
            'discount_percentage' => ['required', 'numeric', 'between:0,100'],
            'Company_rate' => ['required', 'numeric', 'between:0,100'],
            'name' => ['required', 'string', 'max:255', Rule::unique('insurance_translations', 'name')->where('locale', app()->getLocale())->ignore($insuranceId, 'insurance_id')],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', Rule::in([0, 1, '0', '1'])],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInsuranceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'insurance_code' => ['required', 'string', 'max:255', 'unique:insurances,insurance_code'],
            'discount_percentage' => ['required', 'numeric', 'between:0,100'],
            'Company_rate' => ['required', 'numeric', 'between:0,100'],
            'name' => ['required', 'string', 'max:255', Rule::unique('insurance_translations', 'name')->where('locale', app()->getLocale())],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

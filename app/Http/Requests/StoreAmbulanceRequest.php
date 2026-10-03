<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAmbulanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'car_number' => ['required', 'string', 'max:50'],
            'car_model' => ['required', 'string', 'max:100'],
            'car_year_made' => ['required', 'integer', 'between:1900,' . (now()->year + 1)],
            'car_type' => ['required', Rule::in([1, 2])],
            'driver_name' => ['required', 'string', 'max:255', Rule::unique('ambulance_translations', 'driver_name')->where('locale', app()->getLocale())],
            'driver_license_number' => ['required', 'string', 'max:100'],
            'driver_phone' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{6,30}$/'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

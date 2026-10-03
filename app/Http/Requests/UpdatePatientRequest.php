<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $patientId = $this->input('id');

        return [
            'id' => ['required', 'integer', 'exists:patients,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($patientId)],
            'Phone' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{6,30}$/', Rule::unique('patients', 'Phone')->ignore($patientId)],
            'Date_Birth' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'Gender' => ['required', 'integer', Rule::in([1, 2])],
            'Blood_Group' => ['required', Rule::in(['O-', 'O+', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])],
            'Address' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

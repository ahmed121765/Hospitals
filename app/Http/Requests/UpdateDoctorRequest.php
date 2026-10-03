<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $doctorId = $this->route('Doctor') ? $this->route('Doctor')->id : $this->input('id');

        return [
            'id' => ['required', 'integer', 'exists:doctors,id'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('doctors', 'email')->ignore($doctorId)],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{6,30}$/', Rule::unique('doctors', 'phone')->ignore($doctorId)],
            'name' => ['required', 'string', 'max:255'],
            'appointments' => ['required', 'array', 'min:1'],
            'appointments.*' => ['required', 'integer', 'distinct', 'exists:appointments,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'number_of_statements' => ['sometimes', 'required', 'integer', 'min:0', 'max:10000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

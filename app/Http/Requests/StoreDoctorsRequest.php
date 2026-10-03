<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('doctors', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9\s().-]{6,30}$/', Rule::unique('doctors', 'phone')],
            'name' => ['required', 'string', 'max:255'],
            'appointments' => ['required', 'array', 'min:1'],
            'appointments.*' => ['required', 'integer', 'distinct', 'exists:appointments,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

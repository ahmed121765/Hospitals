<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('section_translations', 'name')->where('locale', app()->getLocale())],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

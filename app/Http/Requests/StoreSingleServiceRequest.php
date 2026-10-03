<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSingleServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('Service_translations', 'name')->where('locale', app()->getLocale())],
            'price' => ['required', 'numeric', 'between:0,999999.99'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

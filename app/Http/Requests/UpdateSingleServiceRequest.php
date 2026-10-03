<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSingleServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $serviceId = $this->input('id');

        return [
            'id' => ['required', 'integer', 'exists:Services,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('Service_translations', 'name')->where('locale', app()->getLocale())->ignore($serviceId, 'Service_id')],
            'price' => ['required', 'numeric', 'between:0,999999.99'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ];
    }
}

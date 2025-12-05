<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CompleteSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'country_id' => ['required', 'exists:countries,id'],
            'source_ids' => ['required', 'array', 'min:1'],
            'source_ids.*' => ['required', 'exists:sources,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'country_id.required' => 'يجب اختيار الدولة',
            'country_id.exists' => 'الدولة المحددة غير موجودة',
            'source_ids.required' => 'يجب اختيار مصدر واحد على الأقل',
            'source_ids.min' => 'يجب اختيار مصدر واحد على الأقل',
            'source_ids.*.exists' => 'أحد المصادر المحددة غير موجود',
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    public function rules(): array
    {
        $countryId = $this->route('country');

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('countries')->ignore($countryId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:3',
                Rule::unique('countries')->ignore($countryId),
            ],
            'flag' => ['nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'الاسم بالعربية مطلوب',
            'name_en.required' => 'الاسم بالإنجليزية مطلوب',
            'slug.required' => 'الرابط المختصر مطلوب',
            'slug.unique' => 'الرابط المختصر مستخدم مسبقاً',
            'code.unique' => 'كود الدولة مستخدم مسبقاً',
        ];
    }
}



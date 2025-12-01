<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BroadcastNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
            'filter' => ['sometimes', 'array'],
            'filter.country_id' => ['nullable', 'exists:countries,id'],
            'filter.category_id' => ['nullable', 'exists:categories,id'],
            'filter.source_id' => ['nullable', 'exists:sources,id'],
            'data' => ['sometimes', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإشعار مطلوب',
            'body.required' => 'محتوى الإشعار مطلوب',
        ];
    }
}



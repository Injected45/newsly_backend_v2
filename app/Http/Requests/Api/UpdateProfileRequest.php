<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'country_id' => ['sometimes', 'nullable', 'exists:countries,id'],
            'language' => ['sometimes', 'string', 'in:ar,en'],
            'timezone' => ['sometimes', 'string', 'timezone'],
            'settings' => ['sometimes', 'array'],
            'settings.notifications_enabled' => ['sometimes', 'boolean'],
            'settings.breaking_only' => ['sometimes', 'boolean'],
            'settings.dnd_start' => ['sometimes', 'nullable', 'date_format:H:i'],
            'settings.dnd_end' => ['sometimes', 'nullable', 'date_format:H:i'],
        ];
    }
}



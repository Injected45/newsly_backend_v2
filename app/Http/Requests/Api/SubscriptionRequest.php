<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_id' => ['nullable', 'exists:sources,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'notifications_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                // At least one subscription type must be provided
                if (!$this->source_id && !$this->category_id && !$this->country_id) {
                    $validator->errors()->add(
                        'subscription',
                        'At least one of source_id, category_id, or country_id is required'
                    );
                }
            },
        ];
    }
}



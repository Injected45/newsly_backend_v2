<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sourceId = $this->route('source');

        return [
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'alpha_dash',
                Rule::unique('sources')->ignore($sourceId),
            ],
            'rss_url' => ['required', 'url', 'max:2048'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'country_id' => ['required', 'exists:countries,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'logo' => ['nullable', 'string', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_breaking_source' => ['sometimes', 'boolean'],
            'fetch_interval_seconds' => ['sometimes', 'integer', 'min:60', 'max:86400'],
            'language' => ['sometimes', 'string', 'in:ar,en,fr'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => 'الاسم بالعربية مطلوب',
            'name_en.required' => 'الاسم بالإنجليزية مطلوب',
            'slug.required' => 'الرابط المختصر مطلوب',
            'slug.unique' => 'الرابط المختصر مستخدم مسبقاً',
            'rss_url.required' => 'رابط RSS مطلوب',
            'rss_url.url' => 'رابط RSS غير صحيح',
            'country_id.required' => 'الدولة مطلوبة',
            'country_id.exists' => 'الدولة غير موجودة',
            'fetch_interval_seconds.min' => 'الحد الأدنى للفاصل الزمني 60 ثانية',
            'fetch_interval_seconds.max' => 'الحد الأقصى للفاصل الزمني 24 ساعة',
        ];
    }
}




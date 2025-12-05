<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'website_url' => $this->website_url,
            'language' => $this->language,
            'is_breaking_source' => $this->is_breaking_source,
            'country' => new CountryResource($this->whenLoaded('country')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'articles_count' => $this->whenCounted('articles'),
            'subscribers_count' => $this->whenCounted('subscriptions'),
            'last_fetched_at' => $this->last_fetched_at?->toISOString(),
            'is_active' => $this->is_active,
        ];
    }
}




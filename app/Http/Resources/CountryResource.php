<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'flag' => $this->flag,
            'sources_count' => $this->whenCounted('sources'),
            'is_active' => $this->is_active,
        ];
    }
}



<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->getType(),
            'notifications_enabled' => $this->notifications_enabled,
            'source' => new SourceResource($this->whenLoaded('source')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'country' => new CountryResource($this->whenLoaded('country')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}



<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->when($request->routeIs('articles.show'), $this->content),
            'link' => $this->link,
            'image_url' => $this->image_url,
            'author' => $this->author,
            'tags' => $this->tags,
            'is_breaking' => $this->is_breaking,
            'is_featured' => $this->is_featured,
            'language' => $this->language,
            'views_count' => $this->views_count,
            'published_at' => $this->published_at?->toISOString(),
            'source' => new SourceResource($this->whenLoaded('source')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'country' => new CountryResource($this->whenLoaded('country')),
            'is_read' => $this->when(isset($this->additional['is_read']), $this->additional['is_read'] ?? false),
            'is_bookmarked' => $this->when(isset($this->additional['is_bookmarked']), $this->additional['is_bookmarked'] ?? false),
        ];
    }
}




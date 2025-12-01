<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SourceResource;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    /**
     * List sources with filters
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => ['nullable', 'exists:countries,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Source::with(['country', 'category'])
            ->withCount('articles');

        // Apply filters
        if ($request->has('country_id')) {
            $query->byCountry($request->country_id);
        }

        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('active')) {
            if ($request->boolean('active')) {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        } else {
            $query->active();
        }

        $perPage = $request->integer('per_page', config('news.pagination.default_per_page'));
        $sources = $query->orderBy('name_en')->paginate($perPage);

        return $this->paginated($sources, SourceResource::class);
    }

    /**
     * Get source details
     */
    public function show(Source $source): JsonResponse
    {
        $source->load(['country', 'category']);
        $source->loadCount('articles', 'subscriptions');

        return $this->success(new SourceResource($source));
    }
}



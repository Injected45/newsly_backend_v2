<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * List all active categories
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Cache::remember('categories:active', config('news.cache.categories_ttl'), function () {
            return Category::active()
                ->ordered()
                ->withCount(['sources' => fn($q) => $q->active()])
                ->get();
        });

        return $this->success(CategoryResource::collection($categories));
    }

    /**
     * Get category details
     */
    public function show(Category $category): JsonResponse
    {
        if (!$category->is_active) {
            return $this->error('Category not found', 404);
        }

        $category->loadCount(['sources' => fn($q) => $q->active()]);

        return $this->success(new CategoryResource($category));
    }
}



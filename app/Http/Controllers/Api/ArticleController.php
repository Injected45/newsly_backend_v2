<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * List articles with filters
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => ['nullable', 'exists:countries,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'since' => ['nullable', 'date'],
            'breaking' => ['nullable', 'boolean'],
            'search' => ['nullable', 'string', 'min:2', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Article::with(['source:id,name_ar,name_en,logo', 'category:id,name_ar,name_en'])
            ->latest('published_at');

        // Apply filters
        if ($request->filled('country_id')) {
            $query->byCountry($request->country_id);
        }

        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        if ($request->filled('source_id')) {
            $query->bySource($request->source_id);
        }

        if ($request->filled('since')) {
            $query->publishedAfter($request->date('since'));
        }

        if ($request->has('breaking')) {
            if ($request->boolean('breaking')) {
                $query->breaking();
            }
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = $request->integer('per_page', config('news.pagination.default_per_page'));
        $articles = $query->paginate($perPage);

        return $this->paginated($articles, ArticleResource::class);
    }

    /**
     * Get latest articles (shorthand)
     */
    public function latest(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => ['nullable', 'exists:countries,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Article::with(['source:id,name_ar,name_en,logo', 'category:id,name_ar,name_en'])
            ->latest('published_at');

        if ($request->filled('country_id')) {
            $query->byCountry($request->country_id);
        }

        $limit = $request->integer('limit', 20);
        $articles = $query->take($limit)->get();

        return $this->success(ArticleResource::collection($articles));
    }

    /**
     * Get breaking news
     */
    public function breaking(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => ['nullable', 'exists:countries,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $query = Article::with(['source:id,name_ar,name_en,logo'])
            ->breaking()
            ->latest('published_at');

        if ($request->filled('country_id')) {
            $query->byCountry($request->country_id);
        }

        $limit = $request->integer('limit', 10);
        $articles = $query->take($limit)->get();

        return $this->success(ArticleResource::collection($articles));
    }

    /**
     * Get article details
     */
    public function show(Article $article): JsonResponse
    {
        $article->load(['source', 'category', 'country']);
        $article->incrementViews();

        $data = new ArticleResource($article);

        // Add read/bookmark status if authenticated
        if ($user = auth('sanctum')->user()) {
            $data->additional([
                'is_read' => $article->isReadBy($user),
                'is_bookmarked' => $article->isBookmarkedBy($user),
            ]);
        }

        return $this->success($data);
    }

    /**
     * Mark article as read
     */
    public function markRead(Request $request): JsonResponse
    {
        $request->validate([
            'article_id' => ['required', 'exists:articles,id'],
        ]);

        $article = Article::findOrFail($request->article_id);
        $request->user()->markArticleAsRead($article);

        return $this->success(null, 'Article marked as read');
    }

    /**
     * Get user's read history
     */
    public function readHistory(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $articles = $request->user()
            ->readArticles()
            ->with(['source:id,name_ar,name_en,logo'])
            ->latest('article_reads.read_at')
            ->paginate($perPage);

        return $this->paginated($articles, ArticleResource::class);
    }
}




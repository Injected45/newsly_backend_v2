<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * List user bookmarks
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 20);

        $bookmarks = $request->user()
            ->bookmarks()
            ->with(['source:id,name_ar,name_en,logo', 'category:id,name_ar,name_en'])
            ->latest('bookmarks.created_at')
            ->paginate($perPage);

        return $this->paginated($bookmarks, ArticleResource::class);
    }

    /**
     * Add bookmark
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'article_id' => ['required', 'exists:articles,id'],
        ]);

        $article = Article::findOrFail($request->article_id);
        $request->user()->bookmarkArticle($article);

        return $this->success(null, 'Article bookmarked', 201);
    }

    /**
     * Remove bookmark
     */
    public function destroy(Request $request, Article $article): JsonResponse
    {
        $request->user()->removeBookmark($article);

        return $this->success(null, 'Bookmark removed');
    }
}




<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['source', 'category', 'country']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('breaking')) {
            $query->where('is_breaking', $request->boolean('breaking'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        $articles = $query->latest('published_at')->paginate(20);
        
        $sources = Source::active()->orderBy('name_en')->get();
        $countries = Country::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        return view('admin.articles.index', compact(
            'articles', 'sources', 'countries', 'categories'
        ));
    }

    public function show(Article $article)
    {
        $article->load(['source', 'category', 'country']);

        return view('admin.articles.show', compact('article'));
    }

    public function toggleBreaking(Article $article)
    {
        $article->update(['is_breaking' => !$article->is_breaking]);

        return back()->with('success', 'تم تحديث حالة الخبر العاجل');
    }

    public function toggleFeatured(Article $article)
    {
        $article->update(['is_featured' => !$article->is_featured]);

        return back()->with('success', 'تم تحديث حالة الخبر المميز');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'تم حذف المقال بنجاح');
    }
}




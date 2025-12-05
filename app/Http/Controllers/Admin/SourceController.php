<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSourceRequest;
use App\Jobs\FetchRssJob;
use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Source::with(['country', 'category'])
            ->withCount('articles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        $sources = $query->latest()->paginate(15);
        $countries = Country::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        return view('admin.sources.index', compact('sources', 'countries', 'categories'));
    }

    public function create()
    {
        $countries = Country::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        return view('admin.sources.create', compact('countries', 'categories'));
    }

    public function store(StoreSourceRequest $request)
    {
        $data = $request->validated();
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_en']);
        }

        $source = Source::create($data);

        return redirect()->route('admin.sources.index')
            ->with('success', 'تم إضافة المصدر بنجاح');
    }

    public function show(Source $source)
    {
        $source->load(['country', 'category']);
        $source->loadCount('articles', 'subscriptions');
        
        $recentArticles = $source->articles()
            ->latest('published_at')
            ->take(20)
            ->get();

        $fetchLogs = $source->fetchLogs()
            ->latest()
            ->take(20)
            ->get();

        return view('admin.sources.show', compact('source', 'recentArticles', 'fetchLogs'));
    }

    public function edit(Source $source)
    {
        $countries = Country::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        return view('admin.sources.edit', compact('source', 'countries', 'categories'));
    }

    public function update(StoreSourceRequest $request, Source $source)
    {
        $source->update($request->validated());

        return redirect()->route('admin.sources.index')
            ->with('success', 'تم تحديث المصدر بنجاح');
    }

    public function destroy(Source $source)
    {
        $source->delete();

        return redirect()->route('admin.sources.index')
            ->with('success', 'تم حذف المصدر بنجاح');
    }

    public function toggleActive(Source $source)
    {
        $source->update(['is_active' => !$source->is_active]);

        return back()->with('success', 'تم تحديث حالة المصدر');
    }

    public function fetchNow(Source $source)
    {
        FetchRssJob::dispatch($source);

        return back()->with('success', 'تم جدولة عملية الجلب');
    }
}




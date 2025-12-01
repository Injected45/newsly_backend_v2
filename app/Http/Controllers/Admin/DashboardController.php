<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Country;
use App\Models\FetchLog;
use App\Models\Source;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'users_today' => User::whereDate('created_at', today())->count(),
            'articles' => Article::count(),
            'articles_today' => Article::whereDate('created_at', today())->count(),
            'sources' => Source::count(),
            'active_sources' => Source::active()->count(),
            'countries' => Country::count(),
            'categories' => Category::count(),
        ];

        // Recent articles
        $recentArticles = Article::with('source:id,name_ar,name_en')
            ->latest('published_at')
            ->take(10)
            ->get();

        // Fetch stats (last 24 hours)
        $fetchStats = FetchLog::where('created_at', '>=', now()->subDay())
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Articles per day (last 7 days)
        $articlesPerDay = Article::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Top sources by articles
        $topSources = Source::withCount(['articles' => fn($q) => $q->where('created_at', '>=', now()->subDay())])
            ->orderByDesc('articles_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentArticles',
            'fetchStats',
            'articlesPerDay',
            'topSources'
        ));
    }
}



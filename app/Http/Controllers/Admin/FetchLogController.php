<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FetchLog;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FetchLogController extends Controller
{
    public function index(Request $request)
    {
        $query = FetchLog::with('source:id,name_ar,name_en');

        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->latest('created_at')->paginate(50);

        // Stats
        $stats = FetchLog::where('created_at', '>=', now()->subDay())
            ->select('status', DB::raw('count(*) as count'), DB::raw('avg(runtime_ms) as avg_runtime'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $sources = Source::orderBy('name_en')->get();

        return view('admin.fetch-logs.index', compact('logs', 'stats', 'sources'));
    }
}



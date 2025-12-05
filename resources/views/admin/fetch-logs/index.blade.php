@extends('admin.layouts.app')

@section('title', 'سجلات الجلب')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4">
        <div class="text-sm text-slate-400 mb-1">ناجح</div>
        <div class="text-2xl font-bold text-emerald-400">{{ $stats->get('success')?->count ?? 0 }}</div>
        <div class="text-xs text-slate-500">متوسط {{ round($stats->get('success')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4">
        <div class="text-sm text-slate-400 mb-1">بدون تغيير</div>
        <div class="text-2xl font-bold text-sky-400">{{ $stats->get('not_modified')?->count ?? 0 }}</div>
        <div class="text-xs text-slate-500">متوسط {{ round($stats->get('not_modified')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4">
        <div class="text-sm text-slate-400 mb-1">خطأ</div>
        <div class="text-2xl font-bold text-red-400">{{ $stats->get('error')?->count ?? 0 }}</div>
        <div class="text-xs text-slate-500">متوسط {{ round($stats->get('error')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4">
        <div class="text-sm text-slate-400 mb-1">انتهاء الوقت</div>
        <div class="text-2xl font-bold text-amber-400">{{ $stats->get('timeout')?->count ?? 0 }}</div>
        <div class="text-xs text-slate-500">متوسط {{ round($stats->get('timeout')?->avg_runtime ?? 0) }}ms</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4 mb-6">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="source_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
            <option value="">كل المصادر</option>
            @foreach($sources as $source)
                <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                    {{ $source->name_en }}
                </option>
            @endforeach
        </select>
        
        <select name="status" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
            <option value="">كل الحالات</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>ناجح</option>
            <option value="not_modified" {{ request('status') === 'not_modified' ? 'selected' : '' }}>بدون تغيير</option>
            <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>خطأ</option>
            <option value="timeout" {{ request('status') === 'timeout' ? 'selected' : '' }}>انتهاء الوقت</option>
        </select>
        
        <input type="date" name="date" value="{{ request('date') }}" 
               class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
        
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600 flex-1">تصفية</button>
            <a href="{{ route('admin.fetch-logs.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">إلغاء</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-800">
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المصدر</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">HTTP</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المدة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المقالات</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الوقت</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-white">{{ $log->source?->name_en ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @switch($log->status)
                                @case('success')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">ناجح</span>
                                    @break
                                @case('not_modified')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-400">304</span>
                                    @break
                                @case('error')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400" title="{{ $log->error_message }}">خطأ</span>
                                    @break
                                @case('timeout')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">انتهاء الوقت</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400">{{ $log->http_status ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400">{{ number_format($log->runtime_ms) }}ms</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->articles_found > 0)
                                <span class="text-slate-300">
                                    {{ $log->articles_created }}/{{ $log->articles_found }}
                                </span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400 text-sm">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">لا توجد سجلات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
        <div class="p-4 border-t border-slate-700/50">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'سجلات الجلب')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card p-4">
        <div class="text-sm text-dark-400 mb-1">ناجح</div>
        <div class="text-2xl font-bold text-emerald-400">{{ $stats->get('success')?->count ?? 0 }}</div>
        <div class="text-xs text-dark-500">متوسط {{ round($stats->get('success')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="card p-4">
        <div class="text-sm text-dark-400 mb-1">بدون تغيير</div>
        <div class="text-2xl font-bold text-primary-400">{{ $stats->get('not_modified')?->count ?? 0 }}</div>
        <div class="text-xs text-dark-500">متوسط {{ round($stats->get('not_modified')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="card p-4">
        <div class="text-sm text-dark-400 mb-1">خطأ</div>
        <div class="text-2xl font-bold text-red-400">{{ $stats->get('error')?->count ?? 0 }}</div>
        <div class="text-xs text-dark-500">متوسط {{ round($stats->get('error')?->avg_runtime ?? 0) }}ms</div>
    </div>
    <div class="card p-4">
        <div class="text-sm text-dark-400 mb-1">انتهاء الوقت</div>
        <div class="text-2xl font-bold text-amber-400">{{ $stats->get('timeout')?->count ?? 0 }}</div>
        <div class="text-xs text-dark-500">متوسط {{ round($stats->get('timeout')?->avg_runtime ?? 0) }}ms</div>
    </div>
</div>

<!-- Filters -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="source_id" class="input">
            <option value="">كل المصادر</option>
            @foreach($sources as $source)
                <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                    {{ $source->name_en }}
                </option>
            @endforeach
        </select>
        
        <select name="status" class="input">
            <option value="">كل الحالات</option>
            <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>ناجح</option>
            <option value="not_modified" {{ request('status') === 'not_modified' ? 'selected' : '' }}>بدون تغيير</option>
            <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>خطأ</option>
            <option value="timeout" {{ request('status') === 'timeout' ? 'selected' : '' }}>انتهاء الوقت</option>
        </select>
        
        <input type="date" name="date" value="{{ request('date') }}" class="input">
        
        <div class="flex gap-2">
            <button type="submit" class="btn-secondary flex-1">تصفية</button>
            <a href="{{ route('admin.fetch-logs.index') }}" class="btn-secondary px-4">إلغاء</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>المصدر</th>
                    <th>الحالة</th>
                    <th>HTTP</th>
                    <th>المدة</th>
                    <th>المقالات</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="text-white">{{ $log->source?->name_en ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @switch($log->status)
                                @case('success')
                                    <span class="badge badge-success">ناجح</span>
                                    @break
                                @case('not_modified')
                                    <span class="badge badge-info">304</span>
                                    @break
                                @case('error')
                                    <span class="badge badge-danger" title="{{ $log->error_message }}">خطأ</span>
                                    @break
                                @case('timeout')
                                    <span class="badge badge-warning">انتهاء الوقت</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <span class="text-dark-400">{{ $log->http_status ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="text-dark-400">{{ number_format($log->runtime_ms) }}ms</span>
                        </td>
                        <td>
                            @if($log->articles_found > 0)
                                <span class="text-dark-300">
                                    {{ $log->articles_created }}/{{ $log->articles_found }}
                                </span>
                            @else
                                <span class="text-dark-500">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark-400 text-sm">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-dark-400">لا توجد سجلات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
        <div class="p-4 border-t border-dark-700/50">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection



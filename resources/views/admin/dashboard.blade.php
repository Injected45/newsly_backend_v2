@extends('admin.layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Users Card -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">+{{ $stats['users_today'] }} اليوم</span>
        </div>
        <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['users']) }}</h3>
        <p class="text-slate-400 text-sm">المستخدمون</p>
    </div>
    
    <!-- Articles Card -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-cyan-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-400">+{{ $stats['articles_today'] }} اليوم</span>
        </div>
        <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['articles']) }}</h3>
        <p class="text-slate-400 text-sm">المقالات</p>
    </div>
    
    <!-- Sources Card -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                </svg>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $stats['active_sources'] == $stats['sources'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                {{ $stats['active_sources'] }} نشط
            </span>
        </div>
        <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['sources']) }}</h3>
        <p class="text-slate-400 text-sm">المصادر</p>
    </div>
    
    <!-- Countries Card -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-3xl font-bold mb-1">{{ number_format($stats['countries']) }}</h3>
        <p class="text-slate-400 text-sm">الدول</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Articles Chart -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold mb-6">المقالات خلال الأسبوع</h3>
        <div class="h-64">
            <canvas id="articlesChart"></canvas>
        </div>
    </div>
    
    <!-- Fetch Stats -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold mb-6">حالة الجلب (24 ساعة)</h3>
        <div class="h-64">
            <canvas id="fetchChart"></canvas>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Articles -->
    <div class="lg:col-span-2 bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold">أحدث المقالات</h3>
                <a href="{{ route('admin.articles.index') }}" class="text-sky-400 hover:text-sky-300 text-sm">
                    عرض الكل →
                </a>
            </div>
        </div>
        <div class="divide-y divide-slate-700/50">
            @forelse($recentArticles as $article)
                <div class="p-4 hover:bg-slate-800/30 transition-colors">
                    <div class="flex gap-4">
                        @if($article->image_url)
                            <img src="{{ $article->image_url }}" alt="" class="w-20 h-16 object-cover rounded-lg flex-shrink-0">
                        @else
                            <div class="w-20 h-16 bg-slate-700 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-white truncate mb-1">{{ $article->title }}</h4>
                            <div class="flex items-center gap-2 text-sm text-slate-400">
                                <span>{{ $article->source?->name_en }}</span>
                                <span>•</span>
                                <span>{{ $article->published_at?->diffForHumans() }}</span>
                                @if($article->is_breaking)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">عاجل</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    لا توجد مقالات حتى الآن
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Top Sources -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <h3 class="text-lg font-bold">أكثر المصادر نشاطاً</h3>
            <p class="text-sm text-slate-400 mt-1">خلال 24 ساعة</p>
        </div>
        <div class="p-4 space-y-4">
            @forelse($topSources as $source)
                <div class="flex items-center gap-3">
                    @if($source->logo)
                        <img src="{{ $source->logo }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-lg flex items-center justify-center text-white font-bold">
                            {{ substr($source->name_en, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-white truncate">{{ $source->name_en }}</h4>
                        <p class="text-sm text-slate-400">{{ $source->articles_count }} مقال</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-400 py-4">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Articles Chart
    const articlesCtx = document.getElementById('articlesChart').getContext('2d');
    const articlesData = @json($articlesPerDay);
    
    new Chart(articlesCtx, {
        type: 'line',
        data: {
            labels: Object.keys(articlesData),
            datasets: [{
                label: 'المقالات',
                data: Object.values(articlesData),
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0ea5e9',
                pointBorderColor: '#0ea5e9',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(71, 85, 105, 0.3)'
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(71, 85, 105, 0.3)'
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                }
            }
        }
    });
    
    // Fetch Stats Chart
    const fetchCtx = document.getElementById('fetchChart').getContext('2d');
    const fetchData = @json($fetchStats);
    
    new Chart(fetchCtx, {
        type: 'doughnut',
        data: {
            labels: ['ناجح', 'بدون تغيير', 'خطأ', 'انتهاء الوقت'],
            datasets: [{
                data: [
                    fetchData.success || 0,
                    fetchData.not_modified || 0,
                    fetchData.error || 0,
                    fetchData.timeout || 0
                ],
                backgroundColor: [
                    '#10b981',
                    '#0ea5e9',
                    '#ef4444',
                    '#f59e0b'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        padding: 20,
                        font: {
                            family: 'Tajawal'
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

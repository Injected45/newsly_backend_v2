@extends('admin.layouts.app')

@section('title', 'تفاصيل المصدر')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ $source->website_url }}" target="_blank" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            زيارة الموقع
        </a>
        <a href="{{ route('admin.sources.edit', $source) }}" class="bg-gradient-to-l from-sky-600 to-sky-500 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/30 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            تعديل
        </a>
        <a href="{{ route('admin.sources.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">
            رجوع
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Source Header -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
        <div class="flex items-start gap-6">
            @if($source->logo)
                <img src="{{ $source->logo }}" alt="{{ $source->name_en }}" class="w-24 h-24 rounded-2xl object-cover">
            @else
                <div class="w-24 h-24 bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl flex items-center justify-center text-white font-bold text-3xl">
                    {{ substr($source->name_en, 0, 1) }}
                </div>
            @endif
            
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-white">{{ $source->name_ar }}</h1>
                    @if($source->is_active)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">نشط</span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400">معطل</span>
                    @endif
                    @if($source->is_breaking_source)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">🔴 عاجل</span>
                    @endif
                </div>
                <p class="text-slate-400 text-lg mb-4">{{ $source->name_en }}</p>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    @if($source->country)
                        <span>{{ $source->country->flag }} {{ $source->country->name_ar }}</span>
                    @endif
                    @if($source->category)
                        <span>•</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $source->category->color }}20; color: {{ $source->category->color }}">
                            {{ $source->category->name_ar }}
                        </span>
                    @endif
                    <span>•</span>
                    <span>{{ $source->language }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">إجمالي المقالات</div>
            <div class="text-3xl font-bold text-white">{{ number_format($source->articles_count) }}</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">آخر جلب</div>
            <div class="text-lg font-bold text-white">
                @if($source->last_fetched_at)
                    {{ $source->last_fetched_at->diffForHumans() }}
                @else
                    <span class="text-slate-500">-</span>
                @endif
            </div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">فاصل الجلب</div>
            <div class="text-lg font-bold text-white">{{ $source->fetch_interval_seconds }}s</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">الحالة</div>
            <div class="text-lg font-bold">
                @if($source->is_active)
                    <span class="text-emerald-400">✓ نشط</span>
                @else
                    <span class="text-red-400">✗ معطل</span>
                @endif
            </div>
        </div>
    </div>
    
    <!-- URLs -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">الروابط</h3>
        <div class="space-y-3">
            <div>
                <label class="block text-sm text-slate-400 mb-1">RSS Feed</label>
                <a href="{{ $source->rss_url }}" target="_blank" class="text-sky-400 hover:text-sky-300 break-all">
                    {{ $source->rss_url }}
                </a>
            </div>
            
            @if($source->website_url)
                <div>
                    <label class="block text-sm text-slate-400 mb-1">الموقع الإلكتروني</label>
                    <a href="{{ $source->website_url }}" target="_blank" class="text-sky-400 hover:text-sky-300 break-all">
                        {{ $source->website_url }}
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Articles -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">أحدث المقالات</h3>
                <a href="{{ route('admin.articles.index', ['source_id' => $source->id]) }}" class="text-sky-400 hover:text-sky-300 text-sm">
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
                            <a href="{{ route('admin.articles.show', $article) }}" class="font-medium text-white hover:text-sky-400 line-clamp-2 mb-1 block">
                                {{ $article->title }}
                            </a>
                            <div class="flex items-center gap-2 text-sm text-slate-400">
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
                    لا توجد مقالات من هذا المصدر
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">إجراءات سريعة</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <form action="{{ route('admin.sources.fetch-now', $source) }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-sky-600 hover:bg-sky-500 rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    جلب الأخبار الآن
                </button>
            </form>
            
            <form action="{{ route('admin.sources.toggle-active', $source) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 {{ $source->is_active ? 'bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30' : 'bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30' }} rounded-xl transition-all duration-200">
                    @if($source->is_active)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تعطيل المصدر
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        تفعيل المصدر
                    @endif
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


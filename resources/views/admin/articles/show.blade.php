@extends('admin.layouts.app')

@section('title', 'تفاصيل المقالة')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ $article->link }}" target="_blank" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            فتح المقال الأصلي
        </a>
        <a href="{{ route('admin.articles.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">
            رجوع
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Article Header -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden mb-6">
        @if($article->image_url)
            <div class="relative h-96">
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent"></div>
                
                <!-- Badges on Image -->
                <div class="absolute top-6 right-6 flex gap-2">
                    @if($article->is_breaking)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-500/90 text-white backdrop-blur-sm">
                            🔴 عاجل
                        </span>
                    @endif
                    @if($article->is_featured)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-amber-500/90 text-white backdrop-blur-sm">
                            ⭐ مميز
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        <div class="p-8">
            <!-- Title -->
            <h1 class="text-3xl font-bold text-white mb-4 leading-relaxed">{{ $article->title }}</h1>
            
            <!-- Meta Info -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400 mb-6 pb-6 border-b border-slate-700/50">
                <div class="flex items-center gap-2">
                    @if($article->source?->logo)
                        <img src="{{ $article->source->logo }}" alt="" class="w-6 h-6 rounded">
                    @endif
                    <span class="text-white font-medium">{{ $article->source?->name_ar }}</span>
                </div>
                
                <span>•</span>
                
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $article->published_at?->format('Y-m-d H:i') }}
                </div>
                
                <span>•</span>
                
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    {{ number_format($article->views_count) }} مشاهدة
                </div>
                
                @if($article->source?->country)
                    <span>•</span>
                    <span>{{ $article->source->country->flag }} {{ $article->source->country->name_ar }}</span>
                @endif
            </div>
            
            <!-- Summary -->
            @if($article->summary)
                <div class="bg-slate-900/50 border border-slate-700/50 rounded-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">الملخص</h3>
                    <p class="text-slate-300 leading-relaxed">{{ $article->summary }}</p>
                </div>
            @endif
            
            <!-- Content -->
            @if($article->content)
                <div class="prose prose-invert prose-slate max-w-none">
                    <div class="text-slate-300 leading-relaxed space-y-4">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Additional Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Article Details -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">معلومات المقالة</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">المعرف</span>
                    <span class="text-white font-mono">#{{ $article->id }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">GUID</span>
                    <span class="text-slate-500 text-xs truncate max-w-xs" title="{{ $article->guid }}">{{ Str::limit($article->guid, 40) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">Checksum</span>
                    <span class="text-slate-500 text-xs font-mono">{{ Str::limit($article->checksum, 20) }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">تاريخ الجلب</span>
                    <span class="text-white">{{ $article->fetched_at?->format('Y-m-d H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center py-2">
                    <span class="text-slate-400">تاريخ الإضافة</span>
                    <span class="text-white">{{ $article->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <h3 class="text-lg font-bold text-white mb-4">إجراءات سريعة</h3>
            <div class="space-y-3">
                <!-- Toggle Breaking -->
                <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full flex items-center justify-between px-4 py-3 bg-slate-700 hover:bg-slate-600 rounded-xl transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ $article->is_breaking ? 'text-amber-400' : 'text-slate-400' }}" fill="{{ $article->is_breaking ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>{{ $article->is_breaking ? 'إلغاء عاجل' : 'تعيين عاجل' }}</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
                
                <!-- Toggle Featured -->
                <form action="{{ route('admin.articles.toggle-featured', $article) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full flex items-center justify-between px-4 py-3 bg-slate-700 hover:bg-slate-600 rounded-xl transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 {{ $article->is_featured ? 'text-amber-400' : 'text-slate-400' }}" fill="{{ $article->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <span>{{ $article->is_featured ? 'إلغاء مميز' : 'تعيين مميز' }}</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
                
                <!-- View Original -->
                <a href="{{ $article->link }}" target="_blank" class="w-full flex items-center justify-between px-4 py-3 bg-sky-600 hover:bg-sky-500 rounded-xl transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span>المقال الأصلي</span>
                    </div>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
                
                <!-- Delete -->
                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المقالة؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full flex items-center justify-between px-4 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-xl transition-all duration-200 border border-red-500/30">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>حذف المقالة</span>
                        </div>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@extends('admin.layouts.app')

@section('title', 'المقالات')

@section('content')
<!-- Filters -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث في العنوان..." class="input">
        
        <select name="source_id" class="input">
            <option value="">كل المصادر</option>
            @foreach($sources as $source)
                <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>
                    {{ $source->name_en }}
                </option>
            @endforeach
        </select>
        
        <select name="country_id" class="input">
            <option value="">كل الدول</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                    {{ $country->name_ar }}
                </option>
            @endforeach
        </select>
        
        <select name="breaking" class="input">
            <option value="">الكل</option>
            <option value="1" {{ request('breaking') === '1' ? 'selected' : '' }}>عاجل فقط</option>
        </select>
        
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="input" placeholder="من تاريخ">
        
        <div class="flex gap-2">
            <button type="submit" class="btn-secondary flex-1">تصفية</button>
            <a href="{{ route('admin.articles.index') }}" class="btn-secondary px-4">إلغاء</a>
        </div>
    </form>
</div>

<!-- Articles Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($articles as $article)
        <div class="card overflow-hidden group">
            <div class="relative h-48">
                @if($article->image_url)
                    <img src="{{ $article->image_url }}" alt="" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-dark-700 to-dark-800 flex items-center justify-center">
                        <svg class="w-16 h-16 text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                
                <!-- Overlay Actions -->
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                    <a href="{{ route('admin.articles.show', $article) }}" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                    <a href="{{ $article->link }}" target="_blank" class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/30 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                
                <!-- Badges -->
                <div class="absolute top-3 right-3 flex gap-2">
                    @if($article->is_breaking)
                        <span class="badge badge-danger">عاجل</span>
                    @endif
                    @if($article->is_featured)
                        <span class="badge badge-warning">مميز</span>
                    @endif
                </div>
            </div>
            
            <div class="p-4">
                <h3 class="font-medium text-white line-clamp-2 mb-2" title="{{ $article->title }}">
                    {{ $article->title }}
                </h3>
                
                <div class="flex items-center justify-between text-sm text-dark-400 mb-4">
                    <span>{{ $article->source?->name_en }}</span>
                    <span>{{ $article->published_at?->diffForHumans() }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-xs text-dark-500">{{ number_format($article->views_count) }} مشاهدة</span>
                    
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.articles.toggle-breaking', $article) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-dark-400 hover:text-amber-400 transition-colors" title="{{ $article->is_breaking ? 'إلغاء عاجل' : 'تعيين عاجل' }}">
                                <svg class="w-5 h-5 {{ $article->is_breaking ? 'text-amber-400' : '' }}" fill="{{ $article->is_breaking ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-dark-400 hover:text-red-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full card p-12 text-center">
            <svg class="w-16 h-16 text-dark-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
            <p class="text-dark-400">لا توجد مقالات</p>
        </div>
    @endforelse
</div>

@if($articles->hasPages())
    <div class="mt-6">
        {{ $articles->links() }}
    </div>
@endif
@endsection



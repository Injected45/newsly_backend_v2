@extends('admin.layouts.app')

@section('title', 'المصادر')

@section('header-actions')
    <a href="{{ route('admin.sources.create') }}" class="bg-gradient-to-l from-sky-600 to-sky-500 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إضافة مصدر
    </a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4 mb-6">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث..." 
               class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
        
        <select name="country_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
            <option value="">كل الدول</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                    {{ $country->name_ar }}
                </option>
            @endforeach
        </select>
        
        <select name="category_id" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
            <option value="">كل الفئات</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name_ar }}
                </option>
            @endforeach
        </select>
        
        <select name="active" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
            <option value="">كل الحالات</option>
            <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>نشط</option>
            <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>معطل</option>
        </select>
        
        <div class="flex gap-2">
            <button type="submit" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600 flex-1">تصفية</button>
            <a href="{{ route('admin.sources.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">إلغاء</a>
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
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الدولة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الفئة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المقالات</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">آخر جلب</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($sources as $source)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($source->logo)
                                    <img src="{{ $source->logo }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ substr($source->name_en, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-white">{{ $source->name_ar }}</p>
                                    <p class="text-sm text-slate-400">{{ $source->name_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $source->country?->name_ar }}</td>
                        <td class="px-6 py-4">
                            @if($source->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-400">
                                    {{ $source->category->name_ar }}
                                </span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ number_format($source->articles_count) }}</td>
                        <td class="px-6 py-4">
                            @if($source->last_fetched_at)
                                <span class="text-slate-400 text-sm">{{ $source->last_fetched_at->diffForHumans() }}</span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.sources.toggle-active', $source) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $source->is_active ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $source->is_active ? 'نشط' : 'معطل' }}
                                    </button>
                                </form>
                                @if($source->is_breaking_source)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">عاجل</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.sources.show', $source) }}" class="text-slate-400 hover:text-sky-400 transition-colors" title="عرض">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.sources.edit', $source) }}" class="text-slate-400 hover:text-sky-400 transition-colors" title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.sources.fetch-now', $source) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-emerald-400 transition-colors" title="جلب الآن">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.sources.destroy', $source) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400">لا توجد مصادر</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($sources->hasPages())
        <div class="p-4 border-t border-slate-700/50">
            {{ $sources->links() }}
        </div>
    @endif
</div>
@endsection

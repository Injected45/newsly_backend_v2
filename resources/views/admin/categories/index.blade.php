@extends('admin.layouts.app')

@section('title', 'الفئات')

@section('header-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إضافة فئة
    </a>
@endsection

@section('content')
<!-- Search -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث عن فئة..." class="input">
        </div>
        <button type="submit" class="btn-secondary">بحث</button>
        @if(request('search'))
            <a href="{{ route('admin.categories.index') }}" class="btn-secondary">إلغاء</a>
        @endif
    </form>
</div>

<!-- Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    @forelse($categories as $category)
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: {{ $category->color }}20">
                    <svg class="w-6 h-6" style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $category->is_active ? 'نشط' : 'معطل' }}
                    </button>
                </form>
            </div>
            
            <h3 class="font-bold text-white mb-1">{{ $category->name_ar }}</h3>
            <p class="text-sm text-dark-400 mb-4">{{ $category->name_en }}</p>
            
            <div class="flex items-center justify-between text-sm">
                <span class="text-dark-400">{{ $category->sources_count }} مصدر</span>
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-dark-400 hover:text-primary-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    @if($category->sources_count == 0)
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-dark-400 hover:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full card p-12 text-center">
            <p class="text-dark-400">لا توجد فئات</p>
        </div>
    @endforelse
</div>

@if($categories->hasPages())
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
@endif
@endsection



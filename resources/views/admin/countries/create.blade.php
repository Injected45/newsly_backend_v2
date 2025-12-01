@extends('admin.layouts.app')

@section('title', 'إضافة دولة')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="p-6 border-b border-dark-700/50">
            <h2 class="text-lg font-bold">إضافة دولة جديدة</h2>
        </div>
        
        <form action="{{ route('admin.countries.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="input" required>
                    @error('name_ar')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" class="input" required>
                    @error('name_en')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">الرابط المختصر (slug) *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="input" placeholder="egypt" required>
                    @error('slug')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">كود الدولة</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="input" placeholder="EG" maxlength="3">
                    @error('code')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">العلم (إيموجي)</label>
                    <input type="text" name="flag" value="{{ old('flag') }}" class="input" placeholder="🇪🇬">
                    @error('flag')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">الترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="input" min="0">
                    @error('sort_order')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 rounded border-dark-600 bg-dark-900 text-primary-500 focus:ring-primary-500/20">
                    <span class="text-dark-300">نشط</span>
                </label>
            </div>
            
            <div class="flex items-center gap-4 pt-4 border-t border-dark-700/50">
                <button type="submit" class="btn-primary">حفظ</button>
                <a href="{{ route('admin.countries.index') }}" class="btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection



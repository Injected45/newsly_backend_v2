@extends('admin.layouts.app')

@section('title', 'تعديل مصدر')

@section('content')
<div class="max-w-3xl">
    <div class="card">
        <div class="p-6 border-b border-dark-700/50">
            <h2 class="text-lg font-bold">تعديل: {{ $source->name_ar }}</h2>
        </div>
        
        <form action="{{ route('admin.sources.update', $source) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $source->name_ar) }}" class="input" required>
                    @error('name_ar')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $source->name_en) }}" class="input" required>
                    @error('name_en')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label class="label">رابط RSS *</label>
                <input type="url" name="rss_url" value="{{ old('rss_url', $source->rss_url) }}" class="input" required>
                @error('rss_url')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">الرابط المختصر (slug) *</label>
                    <input type="text" name="slug" value="{{ old('slug', $source->slug) }}" class="input" required>
                    @error('slug')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">رابط الموقع</label>
                    <input type="url" name="website_url" value="{{ old('website_url', $source->website_url) }}" class="input">
                    @error('website_url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">الدولة *</label>
                    <select name="country_id" class="input" required>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id', $source->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->flag }} {{ $country->name_ar }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">الفئة</label>
                    <select name="category_id" class="input">
                        <option value="">بدون فئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $source->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name_ar }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">رابط اللوجو</label>
                    <input type="url" name="logo" value="{{ old('logo', $source->logo) }}" class="input">
                    @error('logo')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="label">فاصل الجلب (بالثواني)</label>
                    <input type="number" name="fetch_interval_seconds" value="{{ old('fetch_interval_seconds', $source->fetch_interval_seconds) }}" class="input" min="60" max="86400">
                    @error('fetch_interval_seconds')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="label">اللغة</label>
                    <select name="language" class="input">
                        <option value="ar" {{ old('language', $source->language) == 'ar' ? 'selected' : '' }}>العربية</option>
                        <option value="en" {{ old('language', $source->language) == 'en' ? 'selected' : '' }}>English</option>
                        <option value="fr" {{ old('language', $source->language) == 'fr' ? 'selected' : '' }}>Français</option>
                    </select>
                </div>
            </div>
            
            <div class="flex flex-col gap-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $source->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-dark-600 bg-dark-900 text-primary-500 focus:ring-primary-500/20">
                    <span class="text-dark-300">نشط</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_breaking_source" value="1" {{ old('is_breaking_source', $source->is_breaking_source) ? 'checked' : '' }} class="w-5 h-5 rounded border-dark-600 bg-dark-900 text-primary-500 focus:ring-primary-500/20">
                    <span class="text-dark-300">مصدر أخبار عاجلة</span>
                </label>
            </div>
            
            <div class="flex items-center gap-4 pt-4 border-t border-dark-700/50">
                <button type="submit" class="btn-primary">تحديث</button>
                <a href="{{ route('admin.sources.index') }}" class="btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection




@extends('admin.layouts.app')

@section('title', 'تعديل دولة')

@section('content')
<div class="max-w-2xl">
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-700/50">
            <h2 class="text-lg font-bold">تعديل: {{ $country->name_ar }}</h2>
        </div>
        
        <form action="{{ route('admin.countries.update', $country) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">الاسم بالعربية *</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $country->name_ar) }}" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200" required>
                    @error('name_ar')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $country->name_en) }}" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200" required>
                    @error('name_en')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">الرابط المختصر (slug) *</label>
                    <input type="text" name="slug" value="{{ old('slug', $country->slug) }}" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200" required>
                    @error('slug')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">كود الدولة</label>
                    <input type="text" name="code" value="{{ old('code', $country->code) }}" placeholder="EG" maxlength="3" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
                    @error('code')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">العلم (إيموجي)</label>
                    <input type="text" name="flag" value="{{ old('flag', $country->flag) }}" placeholder="🇪🇬" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
                    @error('flag')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">الترتيب</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $country->sort_order) }}" min="0" 
                           class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
                    @error('sort_order')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $country->is_active) ? 'checked' : '' }} 
                           class="w-5 h-5 rounded border-slate-600 bg-slate-900 text-sky-500 focus:ring-sky-500/20">
                    <span class="text-slate-300">نشط</span>
                </label>
            </div>
            
            <div class="flex items-center gap-4 pt-4 border-t border-slate-700/50">
                <button type="submit" class="bg-gradient-to-l from-sky-600 to-sky-500 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5">تحديث</button>
                <a href="{{ route('admin.countries.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

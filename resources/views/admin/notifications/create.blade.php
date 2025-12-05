@extends('admin.layouts.app')

@section('title', 'إرسال إشعار')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="p-6 border-b border-dark-700/50">
            <h2 class="text-lg font-bold">إرسال إشعار عام</h2>
            <p class="text-sm text-dark-400 mt-1">سيتم إرسال الإشعار لجميع المستخدمين أو حسب الفلاتر المحددة</p>
        </div>
        
        <form action="{{ route('admin.notifications.broadcast') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label class="label">العنوان *</label>
                <input type="text" name="title" value="{{ old('title') }}" class="input" required placeholder="عنوان الإشعار">
                @error('title')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="label">المحتوى *</label>
                <textarea name="body" rows="4" class="input" required placeholder="محتوى الإشعار">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="p-4 bg-dark-900 rounded-xl space-y-4">
                <h3 class="font-medium text-dark-300">فلاتر اختيارية</h3>
                <p class="text-sm text-dark-500">اتركها فارغة لإرسال الإشعار للجميع</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">الدولة</label>
                        <select name="filter[country_id]" class="input">
                            <option value="">الكل</option>
                            @foreach(\App\Models\Country::active()->get() as $country)
                                <option value="{{ $country->id }}">{{ $country->flag }} {{ $country->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="label">الفئة</label>
                        <select name="filter[category_id]" class="input">
                            <option value="">الكل</option>
                            @foreach(\App\Models\Category::active()->get() as $category)
                                <option value="{{ $category->id }}">{{ $category->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-4 pt-4 border-t border-dark-700/50">
                <button type="submit" class="btn-primary" onclick="return confirm('هل أنت متأكد من إرسال هذا الإشعار؟')">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    إرسال الإشعار
                </button>
                <a href="{{ route('admin.notifications.index') }}" class="btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection




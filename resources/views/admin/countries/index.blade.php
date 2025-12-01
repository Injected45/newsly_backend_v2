@extends('admin.layouts.app')

@section('title', 'الدول')

@section('header-actions')
    <a href="{{ route('admin.countries.create') }}" class="btn-primary">
        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إضافة دولة
    </a>
@endsection

@section('content')
<!-- Search -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث عن دولة..." class="input">
        </div>
        <button type="submit" class="btn-secondary">بحث</button>
        @if(request('search'))
            <a href="{{ route('admin.countries.index') }}" class="btn-secondary">إلغاء</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>الدولة</th>
                    <th>الكود</th>
                    <th>المصادر</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($country->flag)
                                    <span class="text-2xl">{{ $country->flag }}</span>
                                @endif
                                <div>
                                    <p class="font-medium text-white">{{ $country->name_ar }}</p>
                                    <p class="text-sm text-dark-400">{{ $country->name_en }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $country->code ?? $country->slug }}</span>
                        </td>
                        <td>{{ $country->sources_count }}</td>
                        <td>
                            <form action="{{ route('admin.countries.toggle-active', $country) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="badge {{ $country->is_active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $country->is_active ? 'نشط' : 'معطل' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.countries.edit', $country) }}" class="text-dark-400 hover:text-primary-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                @if($country->sources_count == 0)
                                    <form action="{{ route('admin.countries.destroy', $country) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-dark-400 hover:text-red-400 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-dark-400">لا توجد دول</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($countries->hasPages())
        <div class="p-4 border-t border-dark-700/50">
            {{ $countries->links() }}
        </div>
    @endif
</div>
@endsection



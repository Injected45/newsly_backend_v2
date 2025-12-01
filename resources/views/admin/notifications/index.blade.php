@extends('admin.layouts.app')

@section('title', 'الإشعارات')

@section('header-actions')
    <a href="{{ route('admin.notifications.create') }}" class="btn-primary">
        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إرسال إشعار
    </a>
@endsection

@section('content')
<!-- Filters -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <select name="status" class="input flex-1">
            <option value="">كل الحالات</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>مرسل</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فشل</option>
        </select>
        <button type="submit" class="btn-secondary">تصفية</button>
        @if(request('status'))
            <a href="{{ route('admin.notifications.index') }}" class="btn-secondary">إلغاء</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>المستخدم</th>
                    <th>المقال</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notifications as $notification)
                    <tr>
                        <td>
                            <span class="text-white">{{ Str::limit($notification->title, 50) }}</span>
                        </td>
                        <td>
                            @if($notification->user)
                                <span class="text-dark-300">{{ $notification->user->name }}</span>
                            @else
                                <span class="badge badge-info">عام</span>
                            @endif
                        </td>
                        <td>
                            @if($notification->article)
                                <span class="text-dark-400 text-sm">{{ Str::limit($notification->article->title, 30) }}</span>
                            @else
                                <span class="text-dark-500">-</span>
                            @endif
                        </td>
                        <td>
                            @switch($notification->status)
                                @case('pending')
                                    <span class="badge badge-warning">قيد الانتظار</span>
                                    @break
                                @case('sent')
                                    <span class="badge badge-info">مرسل</span>
                                    @break
                                @case('delivered')
                                    <span class="badge badge-success">تم التسليم</span>
                                    @break
                                @case('failed')
                                    <span class="badge badge-danger" title="{{ $notification->error_message }}">فشل</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <span class="text-dark-400 text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-dark-400">لا توجد إشعارات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($notifications->hasPages())
        <div class="p-4 border-t border-dark-700/50">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection



@extends('admin.layouts.app')

@section('title', 'الإشعارات')

@section('header-actions')
    <a href="{{ route('admin.notifications.create') }}" class="bg-gradient-to-l from-sky-600 to-sky-500 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/30 hover:-translate-y-0.5 inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إرسال إشعار
    </a>
@endsection

@section('content')
<!-- Filters -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <select name="status" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200 flex-1">
            <option value="">كل الحالات</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>مرسل</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فشل</option>
        </select>
        <button type="submit" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">تصفية</button>
        @if(request('status'))
            <a href="{{ route('admin.notifications.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">إلغاء</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-800">
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">العنوان</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المقال</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($notifications as $notification)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-white">{{ Str::limit($notification->title, 50) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($notification->user)
                                <span class="text-slate-300">{{ $notification->user->name }}</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-400">عام</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($notification->article)
                                <span class="text-slate-400 text-sm">{{ Str::limit($notification->article->title, 30) }}</span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @switch($notification->status)
                                @case('pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">قيد الانتظار</span>
                                    @break
                                @case('sent')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-500/20 text-sky-400">مرسل</span>
                                    @break
                                @case('delivered')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">تم التسليم</span>
                                    @break
                                @case('failed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400" title="{{ $notification->error_message }}">فشل</span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400 text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">لا توجد إشعارات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($notifications->hasPages())
        <div class="p-4 border-t border-slate-700/50">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

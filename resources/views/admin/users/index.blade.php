@extends('admin.layouts.app')

@section('title', 'المستخدمون')

@section('content')
<!-- Search -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث بالاسم أو البريد..." 
                   class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all duration-200">
        </div>
        <button type="submit" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">بحث</button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">إلغاء</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-800">
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">المستخدم</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الدولة</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الاشتراكات</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">آخر دخول</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">تاريخ التسجيل</th>
                    <th class="px-6 py-4 text-sm font-medium text-slate-400 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->country)
                                <span class="text-slate-300">{{ $user->country->flag }} {{ $user->country->name_ar }}</span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $user->subscriptions_count }}</td>
                        <td class="px-6 py-4">
                            @if($user->last_login_at)
                                <span class="text-slate-400 text-sm">{{ $user->last_login_at->diffForHumans() }}</span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-400 text-sm">{{ $user->created_at->format('Y-m-d') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-slate-400 hover:text-sky-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors">
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
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400">لا يوجد مستخدمون</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="p-4 border-t border-slate-700/50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'المستخدمون')

@section('content')
<!-- Search -->
<div class="card p-4 mb-6">
    <form action="" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث بالاسم أو البريد..." class="input">
        </div>
        <button type="submit" class="btn-secondary">بحث</button>
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">إلغاء</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>المستخدم</th>
                    <th>الدولة</th>
                    <th>الاشتراكات</th>
                    <th>آخر دخول</th>
                    <th>تاريخ التسجيل</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-sm text-dark-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->country)
                                <span class="text-dark-300">{{ $user->country->flag }} {{ $user->country->name_ar }}</span>
                            @else
                                <span class="text-dark-500">-</span>
                            @endif
                        </td>
                        <td>{{ $user->subscriptions_count }}</td>
                        <td>
                            @if($user->last_login_at)
                                <span class="text-dark-400 text-sm">{{ $user->last_login_at->diffForHumans() }}</span>
                            @else
                                <span class="text-dark-500">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark-400 text-sm">{{ $user->created_at->format('Y-m-d') }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-dark-400 hover:text-primary-400 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-dark-400 hover:text-red-400 transition-colors">
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
                        <td colspan="6" class="text-center py-8 text-dark-400">لا يوجد مستخدمون</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="p-4 border-t border-dark-700/50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection



@extends('admin.layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="bg-slate-700 text-white px-6 py-2.5 rounded-xl font-medium transition-all duration-200 hover:bg-slate-600">
        رجوع
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- User Header -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-8">
        <div class="flex items-start gap-6">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover">
            @else
                <div class="w-24 h-24 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center text-white font-bold text-3xl">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>
                <p class="text-slate-400 text-lg mb-4">{{ $user->email }}</p>
                
                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    @if($user->country)
                        <span>{{ $user->country->flag }} {{ $user->country->name_ar }}</span>
                    @endif
                    @if($user->email_verified_at)
                        <span>•</span>
                        <span class="text-emerald-400">✓ البريد موثق</span>
                    @else
                        <span>•</span>
                        <span class="text-amber-400">⚠ البريد غير موثق</span>
                    @endif
                    <span>•</span>
                    <span>{{ $user->language ?? 'ar' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">الاشتراكات</div>
            <div class="text-3xl font-bold text-white">{{ $user->subscriptions_count }}</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">المقالات المقروءة</div>
            <div class="text-3xl font-bold text-white">{{ $user->article_reads_count }}</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">الإشارات المرجعية</div>
            <div class="text-3xl font-bold text-white">{{ $user->bookmarks_count }}</div>
        </div>
        
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
            <div class="text-sm text-slate-400 mb-2">الأجهزة</div>
            <div class="text-3xl font-bold text-white">{{ $user->devices_count }}</div>
        </div>
    </div>
    
    <!-- User Info -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">معلومات المستخدم</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                <span class="text-slate-400">المعرف</span>
                <span class="text-white font-mono">#{{ $user->id }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                <span class="text-slate-400">البريد الإلكتروني</span>
                <span class="text-white">{{ $user->email }}</span>
            </div>
            
            @if($user->country)
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">الدولة</span>
                    <span class="text-white">{{ $user->country->flag }} {{ $user->country->name_ar }}</span>
                </div>
            @endif
            
            <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                <span class="text-slate-400">اللغة</span>
                <span class="text-white">{{ $user->language ?? 'ar' }}</span>
            </div>
            
            @if($user->timezone)
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">المنطقة الزمنية</span>
                    <span class="text-white">{{ $user->timezone }}</span>
                </div>
            @endif
            
            <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                <span class="text-slate-400">آخر دخول</span>
                <span class="text-white">
                    @if($user->last_login_at)
                        {{ $user->last_login_at->format('Y-m-d H:i') }}
                    @else
                        <span class="text-slate-500">-</span>
                    @endif
                </span>
            </div>
            
            @if($user->last_login_ip)
                <div class="flex justify-between items-center py-2 border-b border-slate-700/50">
                    <span class="text-slate-400">آخر IP</span>
                    <span class="text-white font-mono text-sm">{{ $user->last_login_ip }}</span>
                </div>
            @endif
            
            <div class="flex justify-between items-center py-2">
                <span class="text-slate-400">تاريخ التسجيل</span>
                <span class="text-white">{{ $user->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Subscriptions -->
    @if($user->subscriptions->isNotEmpty())
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">اشتراكات المصادر ({{ $user->subscriptions_count }})</h3>
            </div>
            
            <div class="divide-y divide-slate-700/50">
                @foreach($user->subscriptions as $subscription)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-center gap-3">
                            @if($subscription->subscribable_type === 'App\Models\Source')
                                @php
                                    $source = $subscription->subscribable;
                                @endphp
                                @if($source->logo)
                                    <img src="{{ $source->logo }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ substr($source->name_en, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-white">{{ $source->name_ar }}</p>
                                    <p class="text-sm text-slate-400">مصدر</p>
                                </div>
                            @elseif($subscription->subscribable_type === 'App\Models\Category')
                                @php
                                    $category = $subscription->subscribable;
                                @endphp
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $category->color }}20">
                                    <svg class="w-5 h-5" style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $category->name_ar }}</p>
                                    <p class="text-sm text-slate-400">فئة</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="text-sm text-slate-400">
                            {{ $subscription->created_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Devices -->
    @if($user->devices->isNotEmpty())
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-700/50">
                <h3 class="text-lg font-bold text-white">الأجهزة المسجلة ({{ $user->devices_count }})</h3>
            </div>
            
            <div class="divide-y divide-slate-700/50">
                @foreach($user->devices as $device)
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center">
                                    @if(str_contains($device->device_name, 'iPhone') || str_contains($device->device_name, 'iPad'))
                                        <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                        </svg>
                                    @elseif(str_contains($device->device_name, 'Android'))
                                        <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.523 15.341c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5c.826 0 1.5.673 1.5 1.5s-.674 1.5-1.5 1.5zm-11.046 0c-.827 0-1.5-.673-1.5-1.5s.673-1.5 1.5-1.5 1.5.673 1.5 1.5-.673 1.5-1.5 1.5zm11.405-6.02l1.997-3.46a.416.416 0 00-.152-.567.415.415 0 00-.566.152l-2.03 3.515c-1.617-.714-3.418-1.118-5.308-1.118-1.89 0-3.69.404-5.307 1.118L4.487 5.446a.415.415 0 00-.567-.152.416.416 0 00-.152.567l1.997 3.46C2.777 11.248.926 14.325.926 17.8h22.148c0-3.475-1.851-6.552-4.74-8.479h-.452z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ $device->device_name ?? 'Unknown Device' }}</p>
                                    <p class="text-sm text-slate-400">{{ $device->device_type ?? 'mobile' }}</p>
                                    @if($device->fcm_token)
                                        <p class="text-xs text-slate-500 font-mono mt-1">FCM: {{ Str::limit($device->fcm_token, 30) }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-sm text-slate-400">{{ $device->created_at->diffForHumans() }}</p>
                                @if($device->last_active_at)
                                    <p class="text-xs text-slate-500">نشط: {{ $device->last_active_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Actions -->
    <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">إجراءات</h3>
        <div class="flex gap-3">
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-xl transition-all duration-200 border border-red-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    حذف المستخدم
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


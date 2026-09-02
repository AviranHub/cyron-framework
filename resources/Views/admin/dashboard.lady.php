@extends('admin.layout')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">داشبورد مدیریت</h1>
    
    <!-- کارت‌های آماری -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @foreach($stats as $key => $stat)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stat['count']) }}</p>
                        </div>
                        <div class="rounded-full p-3 bg-{{ $stat['color'] }}-100">
                            <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <a href="{{ $stat['route'] }}" class="text-sm text-{{ $stat['color'] }}-600 hover:text-{{ $stat['color'] }}-800 flex items-center justify-between">
                        <span>مشاهده جزئیات</span>
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- بخش فعالیت‌های اخیر (اختیاری) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- آخرین کاربران ثبت‌نام شده -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">آخرین کاربران</h2>
            </div>
            <div class="p-4">
                @php
                    $recentUsers = \App\Models\User::orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                @if($recentUsers->count())
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentUsers as $user)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                                {{-- <span class="text-xs text-gray-400">{{ jdate('Y/m/d',$user->created_at) }}</span> --}}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-center py-4">کاربری یافت نشد</p>
                @endif
            </div>
        </div>

        <!-- آخرین سفارشات یا فعالیت‌های اخیر -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">آخرین فعالیت‌ها</h2>
            </div>
            <div class="p-4">
                @php
                    $recentActivities = \App\Models\UserActivity::orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                @if($recentActivities->count())
                    <ul class="divide-y divide-gray-200">
                        @foreach($recentActivities as $activity)
                            <li class="py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $activity->action }}</p>
                                        <p class="text-sm text-gray-500">{{ $activity->user->name ?? 'کاربر ناشناس' }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ jdate($activity->created_at)->format('H:i') }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-center py-4">فعالیتی یافت نشد</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
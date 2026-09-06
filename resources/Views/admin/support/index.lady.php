@extends('admin.layout')

@section('content')
<div class="max-w-6xl mx-auto p-6" dir="rtl">
    <div class="flex items-center justify-between mb-6"><div><p class="text-sm text-slate-500">مرکز پشتیبانی</p><h1 class="text-2xl font-bold">گفتگوهای کاربران</h1></div><a class="text-blue-700" href="{{ route('admin.dashboard') }}">بازگشت به داشبورد</a></div>
    <div class="bg-white rounded-xl shadow divide-y">
        @if(count($conversationRows))
            @foreach($conversationRows as $row)
                <a class="block p-4 hover:bg-slate-50" href="{{ route('admin.support.show', ['id' => $row['conversation']->id]) }}"><div class="flex items-center justify-between gap-4"><div><strong>{{ $row['user_name'] }}</strong><p class="text-sm text-slate-500">{{ $row['user_email'] }} · {{ $row['conversation']->subject ?? 'پشتیبانی' }}</p></div><div class="text-left"><span class="text-xs {{ $row['conversation']->status === 'open' ? 'text-emerald-600' : 'text-slate-400' }}">{{ $row['conversation']->status === 'open' ? 'باز' : 'بسته' }}</span><small class="block text-xs text-slate-400">{{ $row['conversation']->last_message_at }}</small></div></div></a>
            @endforeach
        @else
            <p class="p-8 text-center text-slate-500">هنوز گفتگویی ثبت نشده است.</p>
        @endif
    </div>
</div>
@endsection
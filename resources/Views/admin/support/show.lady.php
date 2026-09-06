@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto p-6" dir="rtl">
    <div class="flex items-center justify-between mb-6"><div><p class="text-sm text-slate-500">گفتگوی پشتیبانی</p><h1 class="text-2xl font-bold">{{ $user->name ?? 'کاربر' }}</h1><p class="text-sm text-slate-500">{{ $user->email ?? '' }}</p></div><a class="text-blue-700" href="{{ route('admin.support.index') }}">همه گفتگوها</a></div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-emerald-700"><i class="fas fa-circle-check ml-1"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-700"><i class="fas fa-circle-exclamation ml-1"></i>{{ session('error') }}</div>@endif
    <div class="bg-white rounded-xl shadow p-4 mb-4 space-y-3 min-h-64">@foreach($messages as $message)<div class="max-w-xl rounded-xl p-3 {{ $message->sender_type === 'admin' ? 'mr-auto bg-blue-50' : 'ml-auto bg-slate-100' }}"><div class="text-sm whitespace-pre-wrap">{{ $message->body }}</div>@if($message->attachment_path)<a class="mt-2 block text-sm text-blue-700 underline" href="{{ route('support.attachments.show', ['filename' => basename($message->attachment_path)]) }}" target="_blank" rel="noopener"><i class="fas fa-paperclip ml-1"></i>{{ $message->attachment_name }}</a>@endif<small class="block mt-1 text-xs text-slate-400">{{ $message->created_at }} · {{ $message->sender_type === 'admin' ? 'پشتیبانی' : 'کاربر' }}</small></div>@endforeach</div>
    @if($conversation->status === 'open')<form id="admin-support-reply-form" method="POST" action="{{ route('admin.support.reply', ['id' => $conversation->id]) }}" enctype="multipart/form-data" class="admin-support-reply rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><label class="mb-2 block font-semibold text-slate-700">پاسخ شما<div class="mt-2 overflow-hidden rounded-lg border border-slate-300 bg-white"><textarea id="admin-support-body" name="body" rows="4" maxlength="2000" class="block w-full resize-none border-0 p-3 text-slate-700 outline-none" placeholder="پاسخ خود را بنویسید..."></textarea><div class="flex items-center gap-3 border-t border-slate-100 px-3 py-2"><label class="cursor-pointer text-slate-500 transition hover:text-blue-700" title="افزودن فایل"><i class="fas fa-paperclip text-lg"></i><input id="admin-support-file" name="attachment" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" hidden></label><button id="admin-support-emoji-toggle" class="text-slate-500 transition hover:text-blue-700" type="button" title="افزودن ایموجی" aria-label="افزودن ایموجی"><i class="far fa-face-smile text-lg"></i></button><span id="admin-support-file-name" class="truncate text-xs text-slate-400"></span></div></div></label><div id="admin-support-emoji-picker" class="hidden flex-wrap gap-1 rounded-lg bg-slate-50 p-2"><button type="button">🙂</button><button type="button">🙏</button><button type="button">📚</button><button type="button">✅</button><button type="button">❓</button><button type="button">❤️</button><button type="button">😕</button><button type="button">🎉</button></div>@csrf<button class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-700 px-5 py-2 text-white transition hover:bg-blue-800" type="submit"><i class="fas fa-paper-plane"></i><span>ارسال پاسخ</span></button></form><form method="POST" action="{{ route('admin.support.close', ['id' => $conversation->id]) }}" class="mt-3 text-left">@csrf<button class="text-sm text-slate-500 transition hover:text-red-700" type="submit"><i class="fas fa-lock ml-1"></i>بستن گفتگو</button></form>@else<div class="rounded bg-slate-100 p-4 text-slate-600"><i class="fas fa-lock ml-1"></i>این گفتگو بسته شده است. ارسال پیام جدید توسط کاربر آن را دوباره باز می‌کند.</div>@endif
</div>
<script>
const adminSupportEmojiToggle = document.getElementById('admin-support-emoji-toggle');
const adminSupportEmojiPicker = document.getElementById('admin-support-emoji-picker');
const adminSupportBody = document.getElementById('admin-support-body');
const adminSupportFile = document.getElementById('admin-support-file');
const adminSupportFileName = document.getElementById('admin-support-file-name');
adminSupportEmojiToggle?.addEventListener('click', () => {
    adminSupportEmojiPicker.classList.toggle('hidden');
    adminSupportEmojiPicker.classList.toggle('flex');
});
adminSupportEmojiPicker?.querySelectorAll('button').forEach((button) => button.addEventListener('click', () => {
    adminSupportBody.value += button.textContent;
    adminSupportBody.focus();
}));
adminSupportFile?.addEventListener('change', () => {
    adminSupportFileName.textContent = adminSupportFile.files[0]?.name || '';
});
</script>
@endsection
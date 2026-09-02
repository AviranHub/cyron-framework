@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">🧪 تست کامپوننت‌ها</h1>

    {{-- 1. روش @component --}}
    @component('alert', ['type' => 'success', 'title' => 'عملیات موفق'])
        اطلاعات شما با موفقیت ذخیره شد.
    @endcomponent

    {{-- 2. روش x-tag --}}
    <x-alert type="warning" title="توجه">
        این یک هشدار مهم است.
    </x-alert>

    {{-- 3. با دکمه بستن (dismissible) --}}
    <x-alert type="danger" :dismissible="true">
        خطایی رخ داده است. لطفاً مجدد تلاش کنید.
    </x-alert>
</div>
@endsection
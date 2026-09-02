@extends('auth.layouts.guest')
@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">تایید شماره تلفن</h2>
    <p>کد تایید به شماره {{ $phone }} ارسال شد.</p>
    <form method="POST" action="{{ route('phone.verify') }}">
        @csrf
        <div class="mb-4">
            <label>کد ۶ رقمی</label>
            <input type="text" name="code" class="w-full border p-2 rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">تایید</button>
    </form>
    <div class="mt-4 text-center">
        <form method="POST" action="{{ route('phone.send') }}">
            @csrf
            <button type="submit" class="text-sm text-blue-500">ارسال مجدد کد</button>
        </form>
    </div>
</div>
@endsection
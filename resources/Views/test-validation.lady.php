@extends('layouts.master')

@section('content')
<div class="container mt-2">
    <h2>تست سیستم Validation</h2>
    <form method="POST" action="{{ route('test-validation.validate') }}">
        @csrf
        <div>
            <label>نام (required, string, min:3, max:50):</label>
            <input type="text" name="name" value="علی">
        </div>
        <div>
            <label>ایمیل (required, email):</label>
            <input type="email" name="email" value="test@example.com">
        </div>
        <div>
            <label>سن (integer, min:18, max:99):</label>
            <input type="number" name="age" value="25">
        </div>
        <div>
            <label>وبسایت (url):</label>
            <input type="text" name="website" value="https://example.com">
        </div>
        <div>
            <label>تاریخ تولد (date):</label>
            <input type="date" name="birthdate" value="1990-01-01">
        </div>
        <div>
            <label>فعال (boolean):</label>
            <select name="is_active">
                <option value="1">فعال</option>
                <option value="0">غیرفعال</option>
            </select>
        </div>
        <div>
            <label>وضعیت (in:active,inactive):</label>
            <select name="status">
                <option value="active">فعال</option>
                <option value="inactive">غیرفعال</option>
                <option value="blocked">مسدود (نامعتبر)</option>
            </select>
        </div>
        <div>
            <label>نقش (not_in:admin,superadmin):</label>
            <input type="text" name="role" value="editor">
        </div>
        <button type="submit">ارسال</button>
    </form>
</div>
@endsection
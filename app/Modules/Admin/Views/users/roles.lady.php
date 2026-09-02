@extends('admin.layout')
@section('page_title','نقش‌های کاربر')
@section('content')
<div class="rbac-header"><div><h1>{{ $user->name }}</h1><p>{{ $user->email }}</p></div><a class="btn btn-secondary" href="{{ route('admin.users.index') }}">بازگشت</a></div>
<form method="POST" action="{{ route('admin.users.roles.update',$user->id) }}">@csrf @method('PUT')
<section class="card"><div class="card-header"><strong>نقش‌های کاربر</strong><span>نقش‌های فعال را انتخاب کنید</span></div><div class="role-grid">
@foreach($roles as $role)
<label class="role-row"><input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id,$assigned) ? 'checked' : '' }}><span><b>{{ $role->name }}</b><small>{{ $role->slug }}</small></span><i>Priority: {{ $role->priority ?? 0 }}</i><input type="datetime-local" name="expires_at[{{ $role->id }}]" class="role-expiry" title="انقضای نقش (اختیاری)"></label>
@endforeach
</div></section>
<section class="card"><div class="card-header"><strong>نقش اصلی</strong></div><div class="card-body"><select name="primary_role_id" id="primaryRole"><option value="">بدون نقش اصلی</option>@foreach($roles as $role)<option value="{{ $role->id }}" {{ (int)$user->primary_role_id === (int)$role->id ? 'selected' : '' }}>{{ $role->name }}</option>@endforeach</select><p class="muted">نقش اصلی باید جزو نقش‌های انتخاب‌شده باشد.</p></div></section>
<div class="rbac-actions"><button class="btn" type="submit">ذخیره نقش‌ها</button></div></form>
<script>const checks=[...document.querySelectorAll('input[name="roles[]"]')],select=document.querySelector('#primaryRole');function sync(){[...select.options].forEach(o=>{if(o.value)o.disabled=!checks.some(c=>c.checked&&c.value===o.value)});if(select.selectedOptions[0]?.disabled)select.value='';}checks.forEach(c=>c.onchange=sync);sync();</script>
@endsection
@extends('admin.layout')
@section('page_title','دسترسی‌های نقش')
@section('content')
<div class="rbac-header"><div><h1>{{ $role->name }}</h1><p>مدیریت دسترسی‌های این نقش به‌صورت گروه‌بندی‌شده</p></div><a class="btn btn-secondary" href="{{ route('admin.roles.index') }}">بازگشت</a></div>
<form method="POST" action="{{ route('admin.roles.permissions.update',$role->id) }}">@csrf @method('PUT')
@foreach($groups as $group => $permissions)
<section class="card permission-group"><div class="card-header"><strong>{{ $group }}</strong><button type="button" class="select-group">انتخاب همه</button></div><div class="permission-grid">
@foreach($permissions as $permission)
<label class="permission-row"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id,$assigned) ? 'checked' : '' }}><span><b>{{ $permission->name }}</b><small>{{ $permission->slug }}</small></span>@if($permission->is_critical)<em>Critical</em>@endif</label>
@endforeach</div></section>
@endforeach
<div class="rbac-actions"><button class="btn" type="submit">ذخیره دسترسی‌ها</button></div></form>
<script>document.querySelectorAll('.select-group').forEach(b=>b.onclick=()=>{const c=[...b.closest('.permission-group').querySelectorAll('input')];const all=c.every(x=>x.checked);c.forEach(x=>x.checked=!all);b.textContent=all?'انتخاب همه':'لغو انتخاب همه';});</script>
@endsection
@extends('admin.layout')
@section('page_title','امنیت ورود')
@section('content')
<div class="analytics-hero"><div><h1>🚦 تلاش‌های ناموفق ورود</h1><p>برای شناسایی رفتارهای غیرعادی و بررسی محدودسازی ورود</p></div></div>
<section class="card"><div class="card-body"><table class="table"><thead><tr><th>شناسه محدودسازی</th><th>زمان</th></tr></thead><tbody>@foreach($logs as $log)<tr><td>{{ $log->key }}</td><td>{{ $log->occurred_at }}</td></tr>@endforeach</tbody></table></div></section>
@endsection
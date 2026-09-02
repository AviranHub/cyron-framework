@extends('account.layout')
@section('page_title','Recovery Codes')
@section('content')
<div class="analytics-hero"><div><h1>🗝️ کدهای بازیابی</h1><p>این کدها را در جای امن نگه دارید. هر کد فقط یک بار قابل استفاده است.</p></div></div>
<section class="card"><div class="card-body"><div class="recovery-code-grid">@foreach($codes as $code)<code>{{ $code }}</code>@endforeach</div></div></section>
@endsection
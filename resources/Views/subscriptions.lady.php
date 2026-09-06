@extends('layouts.master')

@section('content')
<div class="reading-home">
    <section class="reading-hero" style="min-height:auto; padding-bottom:2rem">
        <div class="reading-hero__copy">
            <span class="eyebrow">کتابخانه‌ای که بیشتر می‌خوانی</span>
            <h1>برای هر سلیقه،<br><em>یک مسیر مطالعه.</em></h1>
            <p>با اشتراک کلبه کتاب، به جای انتخاب‌های پراکنده یک مسیر منظم برای خواندن و شنیدن داشته باش.</p>
        </div>
        <div class="community-callout" style="margin:0; display:block">
            <span class="section-kicker">مزیت اشتراک</span>
            <h2 style="color:white">کمتر حساب کن، بیشتر کشف کن.</h2>
            <p>کتاب‌های منتخب، دانلود مجاز و تجربه‌ای راحت‌تر برای خواندن مداوم.</p>
        </div>
    </section>

    @if(session('notice'))<div class="user-alert">{{ session('notice') }}</div>@endif
    @if(session('error'))<div class="user-alert user-alert--error">{{ session('error') }}</div>@endif

    <section class="book-section" aria-labelledby="plans-title" style="padding-top:1rem">
        <div class="section-heading"><div><span class="section-kicker">انتخاب ساده</span><h2 id="plans-title">پلن اشتراک</h2></div><span class="section-kicker">لغو در هر زمان</span></div>
        <div class="subscription-grid">
            @foreach($plans as $plan)
                <article class="subscription-card {{ $activePlan && $activePlan->subscription_id == $plan->id ? 'subscription-card--active' : '' }}">
                    @if($activePlan && $activePlan->subscription_id == $plan->id)<span class="subscription-badge">اشتراک فعال</span>@endif
                    <span class="section-kicker">{{ $plan->duration }} روز</span>
                    <h3>{{ $plan->name }}</h3>
                    <p>{{ $plan->description ?: 'دسترسی راحت‌تر به مجموعه کتاب‌های کلبه کتاب.' }}</p>
                    <strong>{{ number_format($plan->price) }} <small>تومان</small></strong>
                    <ul><li><i class="fas fa-check"></i> مطالعه کتاب‌های منتخب</li><li><i class="fas fa-check"></i> {{ $plan->allows_download ? 'دانلود مجاز' : 'مطالعه آنلاین' }}</li></ul>
                    @if(!($activePlan && $activePlan->subscription_id == $plan->id))
                        @auth
                            <form method="POST" action="{{ route('subscriptions.purchase', ['id' => $plan->id]) }}">@csrf<input type="text" name="coupon_code" placeholder="کد تخفیف (اختیاری)" class="w-full mb-2 px-3 py-2 border rounded"><button class="button button--primary" type="submit">انتخاب پلن <i class="fas fa-arrow-left"></i></button></form>
                        @else
                            <a class="button button--primary" href="{{ route('login') }}">ورود برای ادامه <i class="fas fa-arrow-left"></i></a>
                        @endauth
                    @else
                        <span class="subscription-current">پلن فعلی شما</span>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection

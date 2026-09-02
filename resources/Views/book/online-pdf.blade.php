@extends('layouts.master')


@section('keywords')

@endsection

@section('description')
{{ $book->title }}
{{ $book->introduction }}
@endsection



@section('content')
<header>
	
	<div class="display-flex flex-right dir-rtl">
		<div class="linkding bg-white padding-4-6-4-10 border-r25-25 mr-4">
			<div class="border-r50 fa fa-home color-primary-dark bg-primary-light icon-style"></div>
			<a href="" class="linkding-link index">کلبه کتاب</a>
			<i class="fa fa-chevron-left" id="linkding-ico"></i>
			<a href="" class="linkding-link">کتاب ها</a>
			<i class="fa fa-chevron-left" id="linkding-ico"></i>
			<a href="{{ route('book',['slug' => $book->slug]) }}" class="linkding-link">{{ $book->title }}</a>
		</div>
	</div>
	<div class="container">
		<div class="display-flex flex-dir-respo padding-0-5 dir-rtl bg-white shadow-lime">
			
			<div class="display-flex padding-0-5 dir-rtl">
				<div class="book-cover-box">
					<img src="{{ asset('storage/book/'.$book->cover) }}" alt="" class="book-cover">
				</div>
				<div class="display-flex flex-dir-col padding-0-5 font-vazir fs-18">
					
					<p class="font-yekan fs-28">{{ $book->title }}</p>
					<p class="">نویسنده :
						@if($book->author_id == "author")
						<a class="" href="{{ route('author',['id' => $book->publisher_id]) }}">{{ $book->author }}</a>
						@else
						<span class="color-green">{{ $book->author }}</span>
						@endif
					</p>
					
					@if($book->genre)
					<p class="">ژانر : <span class="color-green">{{ $book->genre }}</span></p>
					@endif
					
					
					
					
					
					@if($book->copen > 0)
					<p>تخفیف :
						<span class="bg-red color-white border-r5 padding-01-05 fs-14">{{ $book->copen }}%</span>
					</p>
					@endif
					@if($book->copen == 100 or $book->price == 0)
					<p class="">قیمت : <span class="color-green">رایگان</span></p>
					@else
					<p class="">قیمت : <span class="color-green">{{ $book->price }}</span> تومان</p>
					@endif
					
				</div>
			</div>
			
			<div class="display-inline-flex flex-dir-col align-items-center justify-content-center padding-1 font-vazir fs-18 gap-0-5">
				
				<a href="" class="btn-buy text-decoration-none text-align-center">مطالعه آنلاین</a>
				<a href="" class="btn-black-outline text-decoration-none text-align-center">نسخه نمونه</a>
				
			</div>
			
		</div>
	</div>
</header>


<main class="">
	
	
	
	
	
	
	<div class="bookbox">
		<div class="display-grid grid-temp-col-2 padding-0-5 dir-rtl">
			<div class="flex-right">
				<div class="btn-title">کتاب صوتی</div>
			</div>
			<div class="flex-left">
				<a href="" class="link-to-page">مشاهده همه</a>
			</div>
		</div>
		<div class="x-slider">
			<div class="x-slide">
				<div class="x-carusel">
					<img src="/assets/img/1648142816452626.jpg" alt="" class="book-cover">
				</div>
				<div class="x-carusel">
					<img src="/assets/img/5270457146298788.jpg" alt="" class="book-cover">
					
					<div class="display-flex flex-dir-col padding-0-4">
						<p class="fs-14 font-vazir m-0">رمان طغیانگر</p>
						<p class="fs-12 font-vazir m-0">نویسنده مهیا الله یاری</p>
					</div>
				</div>
				<div class="x-carusel">
					<a href="">
						<img src="/assets/img/6602568509988474.jpg" alt="" class="book-cover">
					</a>
					
					<div class="display-flex flex-dir-col padding-0-4">
						<p class="fs-14 font-vazir m-0">رمان طغیانگر</p>
						<p class="fs-12 font-vazir m-0">نویسنده مهیا الله یاری</p>
					</div>
				</div>
				<div class="x-carusel">
					<img src="/assets/img/8042188421342223.jpg" alt="" class="book-cover">
					
					<div class="display-flex flex-dir-col padding-0-4">
						<p class="fs-14 font-vazir m-0">رمان طغیانگر</p>
						<p class="fs-12 font-vazir m-0">نویسنده مهیا الله یاری</p>
					</div>
				</div>
				<div class="x-carusel">
					<a href="">
						<img src="/assets/img/9239585507682642.jpg" alt="" class="book-cover">
					</a>
					<div class="display-flex flex-dir-col padding-0-4">
						<p class="fs-14 font-vazir m-0">رمان طغیانگر</p>
						<p class="fs-12 font-vazir m-0">نویسنده مهیا الله یاری</p>
					</div>
				</div>
				<div class="x-carusel">
					<img src="" alt="" class="book-cover">
				</div>
				<div class="x-carusel">
					<img src="" alt="" class="book-cover">
				</div>
				<div class="x-carusel">
					<img src="" alt="" class="book-cover">
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="container">
		<div class="flex-dir-col">
			<div class="display-flex m-0-5 gap-0-5">
				<img class="w-3" src="/assets/img/icon.png">
				<div class="bg-white br10 shadow-lime padding-0-5">
					<p class="font-vazir fs-16">کامنت من</p>
				</div>
			</div>
		</div>
	</div>
	
	
</main>



@endsection

		</body>
		
		</html>		
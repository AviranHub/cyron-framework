@extends('layouts.master')

@section('content')
    <header>
        
		<div class="containerate">
                <form class="display-inline-block dir-rtl bg-white padding-4-6-4-10 border-r25-25" method="get" action="">
                    <button class="border-r50 fs-18 fa fa-search color-green bg-primary-light icon-style border-none" type="submit" > </button>
                    <input class="field-empty w-a14-d20" placeholder="دنبال چه کتابی میگردی؟" type="text" name="" id="">
                </form>
            </div>
    </header>


    <main class="">






            <div class="display-flex flex-right dir-rtl">
                <div class="linkding bg-white padding-4-6-4-10 border-r25-25 mr-4">
                    <div class="border-r50 fa fa-home color-primary-dark bg-primary-light icon-style"></div>
                    <a href="" class="linkding-link index">کلبه کتاب</a>
                    <i class="fa fa-chevron-left" id="linkding-ico"></i>
                    <a href="" class="linkding-link">صفحه اصلی</a>
                </div>
            </div>
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



    </main>



@endsection

</body>

</html>
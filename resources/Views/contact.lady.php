@extends('layouts.master')

@section('content')
    {{-- ===== هدر با padding-top مناسب برای جبران هدر fixed ===== --}}
    <div class="container mx-auto px-6 pt-24 md:pt-28">

        {{-- ===== مسیر راهنما (Breadcrumb) ===== --}}
        <nav class="flex items-center text-sm text-gray-600 dark:text-gray-400 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm px-5 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-700/50 mb-8">
            <a href="{{ route('home') }}" class="text-brand-green-600 dark:text-brand-green-400 hover:underline transition-colors flex items-center gap-1">
                <i class="fa fa-home"></i>
                <span class="hidden xs:inline">کلبه کتاب</span>
            </a>
            <i class="fa fa-chevron-left mx-2 text-gray-400 text-xs"></i>
            <span class="text-gray-700 dark:text-gray-300 font-medium">تماس با ما</span>
        </nav>

        {{-- ===== بخش هدر محتوا (بدون گرادیان سنگین) ===== --}}
        <div class="text-center mb-12">
            <span class="inline-block bg-brand-green-100 dark:bg-brand-green-900/40 text-brand-green-700 dark:text-brand-green-300 text-sm font-bold px-5 py-2 rounded-full border border-brand-green-200 dark:border-brand-green-700 mb-4">
                <i class="fa fa-phone ml-2"></i> ارتباط با ما
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold dark:text-white">
                در تماس باشیم
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto mt-2 text-base">
                سوال، پیشنهاد یا ایده‌ای دارید؟ خوشحال می‌شیم از شما بشنویم
            </p>
        </div>

        {{-- ===== بخش ارتباط سریع ===== --}}
        <section class="mb-14">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- واتساپ --}}
                <a href="#" class="group bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-1 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-green-50 dark:bg-brand-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-brands fa-whatsapp text-3xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <h3 class="font-bold mt-3 dark:text-white text-sm">واتساپ</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">پاسخگویی فوری</p>
                </a>

                {{-- تلگرام --}}
                <a href="#" class="group bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-1 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-sky-50 dark:bg-brand-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-brands fa-telegram text-3xl text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <h3 class="font-bold mt-3 dark:text-white text-sm">تلگرام</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">پاسخگویی فوری</p>
                </a>

                {{-- ایمیل --}}
                <a href="mailto:info@kolbeketab.com" class="group bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-1 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-red-50 dark:bg-brand-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fa fa-envelope text-3xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="font-bold mt-3 dark:text-white text-sm">ایمیل</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">پاسخ در ۲۴ ساعت</p>
                </a>

                {{-- تلفن --}}
                <a href="tel:+982112345678" class="group bg-white dark:bg-zinc-800 p-5 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-1 text-center">
                    <div class="w-14 h-14 mx-auto rounded-full bg-brand-green-50 dark:bg-brand-green-900/30 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <i class="fa fa-phone text-3xl text-brand-green-600 dark:text-brand-green-400"></i>
                    </div>
                    <h3 class="font-bold mt-3 dark:text-white text-sm">تلفن</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">ساعات اداری</p>
                </a>
            </div>
        </section>

        {{-- ===== فرم تماس + اطلاعات جانبی ===== --}}
        <section class="grid md:grid-cols-5 gap-8 mb-16">

            {{-- فرم تماس --}}
            <div class="md:col-span-3">
                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-3xl shadow-xl p-6 md:p-8 border border-gray-100 dark:border-zinc-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-10 h-1 bg-brand-green-500 rounded-full"></span>
                        <span class="text-brand-green-600 dark:text-brand-green-400 font-bold text-sm tracking-widest">ارسال پیام</span>
                    </div>
                    <h3 class="text-2xl font-bold dark:text-white mb-1">فرم تماس</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">اطلاعات خود را دقیق وارد کنید تا بهتر پاسخگوی شما باشیم.</p>

                    <form action="#" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fa fa-tag ml-1 text-brand-green-500"></i> نوع درخواست
                            </label>
                            <select name="subject" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-300">
                                <option value="">— انتخاب کنید —</option>
                                <option value="support">پشتیبانی و راهنمایی</option>
                                <option value="suggestion">پیشنهاد و انتقاد</option>
                                <option value="investment">درخواست سرمایه‌گذاری</option>
                                <option value="cooperation">همکاری با ناشران / نویسندگان</option>
                                <option value="report">گزارش خطا یا مشکل</option>
                                <option value="other">سایر موارد</option>
                            </select>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <i class="fa fa-user ml-1 text-brand-green-500"></i> نام کامل
                                </label>
                                <input type="text" name="name" placeholder="مثال: میلاد امینی" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    <i class="fa fa-envelope ml-1 text-brand-green-500"></i> ایمیل
                                </label>
                                <input type="email" name="email" placeholder="example@mail.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fa fa-heading ml-1 text-brand-green-500"></i> موضوع
                            </label>
                            <input type="text" name="title" placeholder="موضوع پیام خود را وارد کنید" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fa fa-message ml-1 text-brand-green-500"></i> پیام شما
                            </label>
                            <textarea name="message" rows="5" placeholder="پیام خود را به‌طور کامل بنویسید..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-green-500 focus:border-transparent transition-all duration-300 resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                <i class="fa fa-paperclip ml-1 text-brand-green-500"></i> پیوست (اختیاری)
                            </label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-xl cursor-pointer bg-gray-50 dark:bg-zinc-800/50 hover:bg-gray-100 dark:hover:bg-zinc-700/50 transition-all duration-300">
                                    <div class="flex flex-col items-center justify-center pt-3 pb-2">
                                        <i class="fa fa-cloud-upload text-2xl text-gray-400 dark:text-gray-500 mb-1"></i>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center px-2">فایل خود را بکشید یا کلیک کنید</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">حداکثر ۵ مگابایت</p>
                                    </div>
                                    <input type="file" class="hidden" />
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-brand-green-600 to-light-green-500 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                            <i class="fa fa-paper-plane"></i>
                            ارسال پیام
                        </button>
                    </form>
                </div>
            </div>

            {{-- اطلاعات جانبی --}}
            <div class="md:col-span-2 space-y-6">

                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-3xl shadow-xl p-6 border border-gray-100 dark:border-zinc-700/50">
                    <h4 class="font-bold text-lg dark:text-white mb-4 flex items-center gap-2">
                        <i class="fa fa-circle-info text-brand-green-500"></i>
                        اطلاعات تماس
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-green-50 dark:bg-brand-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa fa-location-dot text-brand-green-600 dark:text-brand-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium dark:text-white text-sm">آدرس</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">تهران، خیابان ولیعصر، پلاک ۱۲۳</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-green-50 dark:bg-brand-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa fa-phone text-brand-green-600 dark:text-brand-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium dark:text-white text-sm">تلفن</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">۰۲۱-۱۲۳۴۵۶۷۸</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-green-50 dark:bg-brand-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa fa-envelope text-brand-green-600 dark:text-brand-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium dark:text-white text-sm">ایمیل</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">info@kolbeketab.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-green-50 dark:bg-brand-green-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa fa-clock text-brand-green-600 dark:text-brand-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium dark:text-white text-sm">ساعات کاری</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">شنبه تا پنجشنبه: ۹ صبح تا ۶ عصر</p>
                                <p class="text-gray-500 dark:text-gray-500 text-xs">جمعه‌ها تعطیل</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-brand-green-50 to-light-green-50 dark:from-brand-green-900/30 dark:to-brand-green-900/20 rounded-3xl shadow-lg p-6 border border-brand-green-100 dark:border-brand-green-800/50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                        <span class="font-bold text-brand-green-700 dark:text-brand-green-300 text-sm">آنلاین هستیم</span>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                        تیم پشتیبانی کلبه کتاب آماده پاسخگویی به سوالات شماست.
                        <span class="block mt-1 text-brand-green-600 dark:text-brand-green-400 font-medium">میانگین زمان پاسخ: کمتر از ۲ ساعت</span>
                    </p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="bg-white/60 dark:bg-zinc-800/60 px-3 py-1 rounded-full text-xs text-gray-600 dark:text-gray-400 border border-white/50 dark:border-zinc-700/50">
                            <i class="fa-regular fa-clock ml-1"></i> پاسخگویی سریع
                        </span>
                        <span class="bg-white/60 dark:bg-zinc-800/60 px-3 py-1 rounded-full text-xs text-gray-600 dark:text-gray-400 border border-white/50 dark:border-zinc-700/50">
                            <i class="fa-regular fa-heart ml-1"></i> ۹۸٪ رضایت
                        </span>
                    </div>
                </div>

                <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden border border-gray-100 dark:border-zinc-700/50 h-44 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-green-100/30 to-light-green-100/30 dark:from-brand-green-900/20 dark:to-brand-green-800/20 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fa fa-map-location-dot text-4xl text-brand-green-600 dark:text-brand-green-400 opacity-50"></i>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">تهران، خیابان ولیعصر</p>
                            <span class="inline-block mt-1 text-xs text-brand-green-600 dark:text-brand-green-400 bg-white/50 dark:bg-zinc-800/50 px-3 py-0.5 rounded-full">نمایش در نقشه</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection
@extends('layouts.master')

@section('content')
    {{-- ===== هدر بزرگ با گرادیان و افکت شیشه‌ای ===== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-green-900 via-brand-green-700 to-light-green-500 pt-20 pb-28">
        <!-- دایره‌های محو برای عمق -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-light-green-300 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-brand-green-300 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="inline-block bg-white/20 backdrop-blur-sm text-white text-sm font-bold px-5 py-2 rounded-full mb-6 border border-white/30 shadow-lg animate-pulse">
                <i class="fa fa-leaf ml-2"></i> کلبه کتاب
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight drop-shadow-2xl">
                داستان ما
            </h1>
            <p class="text-white/90 text-lg md:text-2xl mt-4 max-w-2xl mx-auto font-light leading-relaxed">
                از یک ایده ساده تا یک پلتفرم جامع برای عاشقان کتاب
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <div class="bg-white/20 backdrop-blur-sm px-5 py-2 rounded-full text-white border border-white/30 text-sm md:text-base hover:bg-white/30 transition-all duration-300 hover:scale-105 cursor-default">
                    <i class="fa fa-book ml-2"></i> ۱۰۰۰+ کتاب
                </div>
                <div class="bg-white/20 backdrop-blur-sm px-5 py-2 rounded-full text-white border border-white/30 text-sm md:text-base hover:bg-white/30 transition-all duration-300 hover:scale-105 cursor-default">
                    <i class="fa fa-users ml-2"></i> ۵۰+ نویسنده
                </div>
                <div class="bg-white/20 backdrop-blur-sm px-5 py-2 rounded-full text-white border border-white/30 text-sm md:text-base hover:bg-white/30 transition-all duration-300 hover:scale-105 cursor-default">
                    <i class="fa fa-star ml-2"></i> ۴.۹ امتیاز
                </div>
            </div>
        </div>

        <!-- منحنی جداکننده SVG -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="w-full h-16 md:h-24">
                <path d="M0,0 C300,80 700,80 1200,0 L1200,120 L0,120 Z" fill="#fafafa" class="dark:fill-zinc-800"></path>
            </svg>
        </div>
    </section>

    {{-- ===== محتوای اصلی – با z-index کمتر از منو ===== --}}
    <div class="container mx-auto px-6 -mt-8 relative z-10">

        {{-- بخش درباره ما – کارت شیشه‌ای با سایه عمیق --}}
        <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl shadow-2xl p-6 md:p-10 border border-white/30 dark:border-zinc-700/50 transition-all duration-500 hover:shadow-[0_20px_70px_-15px_rgba(52,176,111,0.3)] dark:hover:shadow-[0_20px_70px_-15px_rgba(52,176,111,0.15)]">
            <div class="grid md:grid-cols-5 gap-8 items-start">
                <div class="md:col-span-3 space-y-5">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-1 bg-brand-green-500 rounded-full"></span>
                        <span class="text-brand-green-600 dark:text-brand-green-400 font-bold text-sm tracking-widest">درباره ما</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold dark:text-white">
                        بیش از یک فروشگاه کتاب
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300 text-base md:text-lg leading-relaxed">
                        فروشگاه کلبه کتاب با هدف ارائه بهترین و جدیدترین کتاب‌های الکترونیکی به زبان فارسی تأسیس شده است.
                        ما به دنبال ایجاد یک پلتفرم جامع برای علاقه‌مندان به کتاب و مطالعه هستیم.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        تیم ما متشکل از افراد متخصص و علاقه‌مند به کتاب و فناوری است که با تلاش و پشتکار فراوان سعی در ارائه بهترین
                        خدمات به شما عزیزان دارند.
                    </p>
                    <div class="grid grid-cols-2 gap-4 pt-3">
                        <div class="bg-light-green-50 dark:bg-brand-green-900/30 p-4 rounded-2xl border border-light-green-200 dark:border-brand-green-700 transition-all duration-300 hover:scale-105 hover:shadow-lg">
                            <div class="text-2xl font-bold text-brand-green-600 dark:text-light-green-400">۱۳۹۸</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">سال تأسیس</div>
                        </div>
                        <div class="bg-light-green-50 dark:bg-brand-green-900/30 p-4 rounded-2xl border border-light-green-200 dark:border-brand-green-700 transition-all duration-300 hover:scale-105 hover:shadow-lg">
                            <div class="text-2xl font-bold text-brand-green-600 dark:text-light-green-400">۱۰۰٪</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">رضایت مشتری</div>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2 flex justify-center">
                    <div class="relative w-full max-w-xs aspect-square rounded-3xl overflow-hidden shadow-2xl border-4 border-white/30 dark:border-zinc-700/50 group">
                        <img src="/assets/img/about-us.jpg" alt="کلبه کتاب" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-tr from-brand-green-900/40 to-transparent"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== بخش تیم ما – کارت‌های مدرن با انیمیشن ===== --}}
        <section class="mt-20">
            <div class="text-center mb-14">
                <span class="inline-block bg-brand-green-100 dark:bg-brand-green-900/40 text-brand-green-700 dark:text-brand-green-300 text-sm font-bold px-5 py-2 rounded-full border border-brand-green-200 dark:border-brand-green-700">
                    <i class="fa fa-users ml-2"></i> اعضای تیم
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mt-4 dark:text-white">
                    انسان‌های پشت صحنه
                </h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-xl mx-auto mt-2">
                    متخصصانی که با عشق و تعهد، بهترین تجربه را برای شما می‌سازند
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                {{-- کارت ۱ – میلاد --}}
                <div class="group relative bg-white dark:bg-zinc-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden border border-brand-green-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-b from-brand-green-50/0 via-brand-green-50/0 to-brand-green-50/60 dark:from-brand-green-900/0 dark:via-brand-green-900/0 dark:to-brand-green-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6 text-center">
                        <div class="relative inline-block">
                            <div class="w-36 h-36 md:w-40 md:h-40 rounded-full border-4 border-brand-green-200 dark:border-brand-green-600 overflow-hidden shadow-lg group-hover:scale-105 transition-transform duration-500">
                                <img src="/assets/img/milad-amini.jpg" alt="میلاد امینی" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-brand-green-500 text-white py-1 px-2 md:py-1 md:px-2 rounded-full shadow-lg border-2 border-white dark:border-zinc-800 animate-bounce">
                                <i class="fa fa-check text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mt-4 dark:text-white group-hover:text-brand-green-600 dark:group-hover:text-brand-green-400 transition-colors">میلاد امینی</h3>
                        <div class="flex items-center justify-center gap-2 text-brand-green-600 dark:text-brand-green-400 font-medium text-sm md:text-base">
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                            بنیان‌گذار
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs md:text-sm mt-3 leading-relaxed">
                            عاشق کتاب، تکنولوژی و خلق تجربه‌های جدید
                        </p>
                        <div class="flex justify-center gap-3 mt-4">
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-linkedin text-lg md:text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-twitter text-lg md:text-xl"></i></a>
                        </div>
                    </div>
                </div>

                {{-- کارت ۲ – آویران --}}
                <div class="group relative bg-white dark:bg-zinc-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden border border-brand-green-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-b from-brand-green-50/0 via-brand-green-50/0 to-brand-green-50/60 dark:from-brand-green-900/0 dark:via-brand-green-900/0 dark:to-brand-green-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6 text-center">
                        <div class="relative inline-block">
                            <div class="w-36 h-36 md:w-40 md:h-40 rounded-full border-4 border-brand-green-200 dark:border-brand-green-600 overflow-hidden shadow-lg group-hover:scale-105 transition-transform duration-500">
                                <img src="/assets/img/aviran.jpg" alt="آویران راد" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-brand-green-500 text-white py-1 px-2 md:py-1 md:px-2 rounded-full shadow-lg border-2 border-white dark:border-zinc-800 animate-bounce">
                                <i class="fa fa-check text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mt-4 dark:text-white group-hover:text-brand-green-600 dark:group-hover:text-brand-green-400 transition-colors">آویران راد</h3>
                        <div class="flex items-center justify-center gap-2 text-brand-green-600 dark:text-brand-green-400 font-medium text-sm md:text-base">
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                            مدیر بازاریابی
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs md:text-sm mt-3 leading-relaxed">
                            استراتژیست، رشددهنده و عاشق داستان‌های خوب
                        </p>
                        <div class="flex justify-center gap-3 mt-4">
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-linkedin text-lg md:text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-twitter text-lg md:text-xl"></i></a>
                        </div>
                    </div>
                </div>

                {{-- کارت ۳ – ناشناس --}}
                <div class="group relative bg-white dark:bg-zinc-800 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 overflow-hidden border border-brand-green-100 dark:border-zinc-700 hover:border-brand-green-300 dark:hover:border-brand-green-600 hover:-translate-y-2">
                    <div class="absolute inset-0 bg-gradient-to-b from-brand-green-50/0 via-brand-green-50/0 to-brand-green-50/60 dark:from-brand-green-900/0 dark:via-brand-green-900/0 dark:to-brand-green-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6 text-center">
                        <div class="relative inline-block">
                            <div class="w-36 h-36 md:w-40 md:h-40 rounded-full border-4 border-brand-green-200 dark:border-brand-green-600 overflow-hidden shadow-lg group-hover:scale-105 transition-transform duration-500">
                                <img src="/assets/img/marketing-managment.jpg" alt="مدیر فناوری" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute -bottom-2 -right-2 bg-brand-green-500 text-white py-1 px-2 md:py-1 md:px-2 rounded-full shadow-lg border-2 border-white dark:border-zinc-800 animate-bounce">
                                <i class="fa fa-check text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold mt-4 dark:text-white group-hover:text-brand-green-600 dark:group-hover:text-brand-green-400 transition-colors">ناشناس</h3>
                        <div class="flex items-center justify-center gap-2 text-brand-green-600 dark:text-brand-green-400 font-medium text-sm md:text-base">
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                            مدیر برنامه‌نویسی
                            <span class="w-5 h-0.5 bg-brand-green-300 dark:bg-brand-green-600"></span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-xs md:text-sm mt-3 leading-relaxed">
                            معمار کد، حل‌کننده چالش‌ها و عاشق بهینه‌سازی
                        </p>
                        <div class="flex justify-center gap-3 mt-4">
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-linkedin text-lg md:text-xl"></i></a>
                            <a href="#" class="text-gray-400 hover:text-brand-green-600 dark:hover:text-brand-green-400 transition-all duration-300 hover:scale-125"><i class="fa fa-twitter text-lg md:text-xl"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- ===== بخش آمار و ارتباط ===== --}}
        <section class="mt-20 mb-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 bg-gradient-to-r from-brand-green-50 to-white dark:from-zinc-800 dark:to-zinc-900 p-6 md:p-8 rounded-3xl shadow-lg border border-brand-green-100 dark:border-zinc-700">
                <div class="text-center group transition-all duration-300 hover:scale-105">
                    <div class="text-2xl md:text-3xl font-bold text-brand-green-600 dark:text-brand-green-400 group-hover:text-brand-green-500">۱۲۰۰+</div>
                    <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">کتاب الکترونیک</div>
                </div>
                <div class="text-center group transition-all duration-300 hover:scale-105">
                    <div class="text-2xl md:text-3xl font-bold text-brand-green-600 dark:text-brand-green-400 group-hover:text-brand-green-500">۱۵۰۰+</div>
                    <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">مشتری وفادار</div>
                </div>
                <div class="text-center group transition-all duration-300 hover:scale-105">
                    <div class="text-2xl md:text-3xl font-bold text-brand-green-600 dark:text-brand-green-400 group-hover:text-brand-green-500">۴.۹</div>
                    <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">میانگین امتیاز</div>
                </div>
                <div class="text-center group transition-all duration-300 hover:scale-105">
                    <div class="text-2xl md:text-3xl font-bold text-brand-green-600 dark:text-brand-green-400 group-hover:text-brand-green-500">۹۸٪</div>
                    <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">رضایت کاربران</div>
                </div>
            </div>
        </section>

    </div>
@endsection
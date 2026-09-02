@extends('Layouts.app')

@section('style')
<style>
    body {
        font-family: 'Vazirmatn', sans-serif;
        direction: rtl;
    }
    
    /* اسلایدر اختصاصی Cyron */
    .cyron-slider {
        position: relative;
        width: 100%;
        height: 210px;
        overflow: hidden;
        padding: var(--cyron-space-2);
        border-radius: var(--cyron-radius-xl);
        background: var(--cyron-gray-100);
    }
    
    .cyron-slides {
        position: relative;
        height: 200px;
    }
    
    .cyron-slides img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: var(--cyron-radius-lg);
        position: absolute;
        opacity: 0;
        transition: opacity 0.5s var(--cyron-transition-normal);
    }
    
    .cyron-slides img.cyron-active {
        opacity: 1;
    }
    
    .cyron-slider-dots {
        position: absolute;
        bottom: var(--cyron-space-2);
        left: 4%;
        display: flex;
        gap: var(--cyron-space-2);
    }
    
    .cyron-dot {
        width: 10px;
        height: 10px;
        background: var(--cyron-gray-400);
        border-radius: var(--cyron-radius-full);
        cursor: pointer;
        transition: all var(--cyron-transition-fast);
    }
    
    .cyron-dot.cyron-active {
        background: var(--cyron-primary-600);
        width: 20px;
    }
    
    /* اسکرول افقی */
    .cyron-scroll-container {
        display: flex;
        gap: var(--cyron-space-4);
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        padding: var(--cyron-space-2);
    }
    
    .cyron-scroll-container::-webkit-scrollbar {
        height: 4px;
    }
    
    .cyron-scroll-container::-webkit-scrollbar-track {
        background: var(--cyron-gray-200);
        border-radius: var(--cyron-radius-full);
    }
    
    .cyron-scroll-container::-webkit-scrollbar-thumb {
        background: var(--cyron-primary-500);
        border-radius: var(--cyron-radius-full);
    }
    
    /* کارت پیشنهادات */
    .cyron-suggestion-card {
        min-width: 150px;
        transition: transform var(--cyron-transition-fast);
    }
    
    .cyron-suggestion-card:hover {
        transform: translateY(-4px);
    }
    
    .cyron-suggestion-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: var(--cyron-radius-lg);
    }
</style>
@endsection

@section('content')
<div class="cyron-bg-gray-100">
    <div class="cyron-bg-white cyron-shadow-md">
        
        <!-- اسلایدر اختصاصی Cyron -->
        <div class="cyron-slider" x-data="sliderComponent()" x-init="init()">
            <div class="cyron-slides">
                <template x-for="(slide, index) in slides" :key="index">
                    <img :src="slide.image" :alt="slide.alt" 
                         :class="{ 'cyron-active': currentSlide === index }"
                         :style="{ opacity: currentSlide === index ? 1 : 0 }">
                </template>
            </div>
            <div class="cyron-slider-dots">
                <template x-for="(slide, index) in slides" :key="index">
                    <div class="cyron-dot" 
                         :class="{ 'cyron-active': currentSlide === index }"
                         @click="goToSlide(index)"></div>
                </template>
            </div>
        </div>
        <!-- دسته‌بندی‌ها -->
        <div class="cyron-grid cyron-grid-cols-4 sm:cyron-grid-cols-6 md:cyron-grid-cols-8 cyron-gap-4 cyron-p-4 cyron-text-center">
            @foreach($categories as $category)
            <div>
                <a href="@route('guilds-category',['slug' => $category->slug])" 
                   class="cyron-block cyron-transition cyron-hover-scale">
                    <img src="@storage($category->image)" 
                         alt="{{ $category->name }}" 
                         class="cyron-w-16 cyron-h-16 cyron-mx-auto cyron-rounded-full cyron-shadow-md">
                    <p class="cyron-mt-2 cyron-text-sm cyron-text-gray-700">{{ $category->name }}</p>
                </a>
            </div>
            @endforeach
        </div>
        
        <!-- پیشنهادات -->
        <div class="cyron-text-right">
            <div class="cyron-flex cyron-justify-between cyron-items-center cyron-mb-4">
                <h2 class="cyron-text-lg cyron-font-bold cyron-py-2 cyron-pt-4 cyron-px-4 cyron-text-gray-800">
                    پیشنهادها
                </h2>
                <a href="@route('suggestions')" 
                   class="cyron-text-primary-600 cyron-text-lg cyron-px-2 cyron-transition cyron-hover-text-primary-800">
                    مشاهده بیشتر ←
                </a>
            </div>
            
            <div class="cyron-relative" x-data="scrollComponent()">
                <button @click="scrollLeft" 
                        class="cyron-btn cyron-btn-secondary cyron-hidden md:cyron-block cyron-absolute cyron-left-0 cyron-top-1/2 cyron-transform -cyron-translate-y-1/2 cyron-z-10">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                <div class="cyron-scroll-container cyron-px-2" x-ref="scrollContainer">
                    @foreach($suggestions as $suggestion)
                    <a href="@route('guild',['slug' => $suggestion->slug])" 
                       class="cyron-suggestion-card cyron-block">
                        <img src="@storage($suggestion->image)" alt="{{ $suggestion->name }}">
                        <p class="cyron-mt-2 cyron-text-sm cyron-text-center cyron-font-medium cyron-text-gray-700">
                            {{ $suggestion->name }}
                        </p>
                    </a>
                    @endforeach
                </div>
                
                <button @click="scrollRight" 
                        class="cyron-btn cyron-btn-secondary cyron-hidden md:cyron-block cyron-absolute cyron-right-0 cyron-top-1/2 cyron-transform -cyron-translate-y-1/2 cyron-z-10">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <hr class="cyron-my-4 cyron-border-gray-200">
        
        <!-- فوتر -->
        <div class="cyron-bg-gray-100 cyron-text-gray-600 cyron-flex cyron-flex-col cyron-items-center cyron-p-4">
            <div class="cyron-text-center">
                <p>© {{ date('Y') }} <span class="cyron-text-primary-600 cyron-font-bold">Cyron</span> Framework. All rights reserved.</p>
                <p class="cyron-text-xs cyron-text-gray-400 cyron-mt-2">Powered by Cyron PHP Framework</p>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('script')
<script>
    // کامپوننت اسلایدر
    function sliderComponent() {
        return {
            slides: @json($sliders),
            currentSlide: 0,
            interval: null,
            
            init() {
                this.startAutoPlay();
                // اضافه کردن داده‌های اسلایدها از سرور
                this.slides = this.slides.map(slide => ({
                    image: slide.image_url ?? '/storage/' + slide.image,
                    alt: slide.description
                }));
            },
            
            goToSlide(index) {
                this.currentSlide = index;
                this.resetAutoPlay();
            },
            
            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            },
            
            startAutoPlay() {
                this.interval = setInterval(() => {
                    this.nextSlide();
                }, 3000);
            },
            
            resetAutoPlay() {
                clearInterval(this.interval);
                this.startAutoPlay();
            }
        }
    }
    
    // کامپوننت اسکرول افقی
    function scrollComponent() {
        return {
            scrollLeft() {
                const container = this.$refs.scrollContainer;
                container.scrollBy({ left: -200, behavior: 'smooth' });
            },
            
            scrollRight() {
                const container = this.$refs.scrollContainer;
                container.scrollBy({ left: 200, behavior: 'smooth' });
            }
        }
    }
    
    // ثبت کامپوننت‌ها در Cyron
    if (typeof Cyron !== 'undefined') {
        Cyron.component('slider', sliderComponent);
        Cyron.component('scroll', scrollComponent);
    }
</script>
@endsection
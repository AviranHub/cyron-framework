<!-- resources/views/components/book-slider.lady.php -->
@props(['title' => '', 'icon' => 'fa fa-book', 'iconColor'=> 'text-lime-600', 'targetId' => '', 'seeAllUrl' => '#'])

<div class="bookbox container mx-auto px-2 md:px-0 mb-8">
    <div class="flex justify-between gap-4 items-center mb-8 border-b border-lime-400 rounded-r-full p-2">
        <div class="text-right"><p class="text-2xl py-1 px-4 cursor-pointer font-bold text-black dark:text-white"><i class="{{ $icon }} {{ $iconColor }} ml-3"></i>{{ $title }}</p></div>
        <div class="text-left"><a class="py-1 px-3 text-sm rounded-full text-black hover:text-white dark:text-white bg-gray-300 dark:bg-gray-600 hover:bg-lime-600 dark:hover:bg-lime-600" href="{{ $seeAllUrl }}">مشاهده همه <i class="fa fa-chevron-left"></i></a></div>
    </div>
    <div class="flex justify-center">
        {{-- <div class="scroll-right hidden lg:flex items-center p-4 text-stone-500 text-3xl cursor-pointer" data-target="{{ $targetId }}">
            <i class="fa fa-chevron-right" aria-hidden="true"></i>
        </div> --}}
        <div class="flex space-x-2 overflow-x-auto overflow-y-hidden" :id="{{ $targetId }}">
            <div class="flex flex-nowrap x-slide gap-2 md:gap-4 lg:gap-12 whitespace-nowrap justify-center">
                {!! $slot !!}
            </div>
        </div>
        {{-- <div class="scroll-left hidden lg:flex items-center p-4 text-stone-500 text-3xl cursor-pointer" data-target="{{ $targetId }}">
            <i class="fa fa-chevron-left" aria-hidden="true"></i>
        </div> --}}
    </div>
</div>


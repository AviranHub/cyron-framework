@props(['active','href'])

@php
$classes = ($active ?? false)
            ? 'py-2 px-5 border-b-2 border-green-400 dark:border-green-600 font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-green-700 transition duration-150 ease-in-out'
            : 'py-2 px-5 border-b-2 border-transparent font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a href="{{ $href }}" {!! $attributes->merge(['class' => $classes]) !!}>
    {{ $slot }}
</a>

{{-- @props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'py-2 px-5 border-b-2 border-green-400 dark:border-green-600 font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-green-700 transition duration-150 ease-in-out'
    : 'py-2 px-5 border-b-2 border-transparent font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a href="{{ $href ?? '#' }}" class="{{ $classes }}">
    {{ $slot }}
</a> --}}
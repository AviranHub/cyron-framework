@extends('Layouts.app')

@section('content')
<div class="bg-gray-50 text-gray-800">
    <div class="max-w-7xl mx-auto p-4">
        <!-- Breadcrumbs -->
        <nav class="flex mb-4 bg-white p-3 rounded-lg shadow" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home ml-2"></i>
                        خانه
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                        <a href="@route('guilds')" class="text-sm font-medium text-gray-700 hover:text-blue-600">اصناف</a>
                    </div>
                </li>
                @isset($category)
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-left text-gray-400 mx-2"></i>
                        <a href="" class="text-sm font-medium text-gray-700 hover:text-blue-600">{{$category->name}}</a>
                    </div>
                </li>
                @endisset
            </ol>
        </nav>

        <!-- Listings -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Listing 1 -->
            @foreach($guilds as $guild)
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="relative">
                    <img alt="{{ $guild->name }}" class="w-full h-48 object-cover rounded-lg" height="200" src="@storage('{{$guild->image}}')" width="300" />
                </div>
                <div class="py-4 pt-0">
                    <h2 class="text-lg font-bold mt-2">{{ $guild->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $guild->description }}</p>
                </div>
                <a href="@route('guild',['slug' => '$guild->slug'])" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-full">مشاهده مغازه</a>
            </div>
            @endforeach





        </div>
    </div>
</div>

@endsection
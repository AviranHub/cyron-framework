<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@title('Authentication')</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="/assets/css/all.css">
    

    <link rel="stylesheet" href="/build/assets/style.css">
    <script type="module" src="/build/assets/script.js"></script>

    <link rel="stylesheet" href="http://localhost:5173/resources/Styles/style.css">
    <script type="module" src="http://localhost:5173/resources/Scripts/script.js"></script>

</head>
<body class="bg-gray-100 dark:bg-zinc-800">
    <div class="container mx-auto px-4 py-8">
        @if(session()->get('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session()->get('success') }}</div>
        @endif
        @if(session()->get('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session()->get('error') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>
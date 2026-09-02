<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@title('Authentication')</title>
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('modules/auth/css/auth.css') }}" rel="stylesheet">
</head>
<body>
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-brand">✦ <span>Cyron</span></div>
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-error">{{ session('error') }}</div> @endif
            @yield('content')
        </section>
    </main>
</body>
</html>
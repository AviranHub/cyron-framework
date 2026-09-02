<!DOCTYPE html>
<html lang="fa" dir="rtl" c-app="app" c-data='{"theme": "light", "sidebarOpen": true}'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('APP_NAME', 'Cyron') }}</title>
    
    <!-- Cyron CSS Framework -->

    
    
    <link rel="preload" as="style" href="/build/assets/app.css" />
    <link rel="modulepreload" href="/build/assets/app.js" />
    <link rel="stylesheet" href="/build/assets/app.css" data-navigate-track="reload" />
    <script type="module" src="/build/assets/app.js" data-navigate-track="reload"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <nav class="bg-white shadow-md p-4">
            <div class="container mx-auto flex justify-between items-center">
                <a href="#" class="text-xl font-bold text-primary">
                    {{ config('APP_NAME', 'Cyron') }}
                </a>
                
                <button c-click="toggleDarkMode" class="btn btn-secondary">
                    <span c-text="theme == 'dark' ? '☀️' : '🌙'"></span>
                </button>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="container mx-auto px-4 py-8">
            @yield('content')
        </main>
    </div>
    
    <!-- Cyron JS Framework -->
    <script src="/venus/js/cyron.js"></script>
    
    
    <script>
        // Component example
        const app = Cyron.createApp({
            data: {
                message: 'Welcome to Cyron Framework!',
                count: 0,
                users: []
            },
            
            methods: {
                increment() {
                    this.count++;
                },
                
                decrement() {
                    this.count--;
                },
                
                toggleDarkMode() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', this.theme);
                },
                
                async fetchUsers() {
                    const data = await Cyron.http.get('/api/users');
                    this.users = data;
                }
            },
            
            mounted() {
                console.log('App mounted!');
                this.fetchUsers();
            }
        }).mount('[c-app]');
    </script>
</body>
</html>
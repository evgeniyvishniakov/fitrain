<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ панель') - FitTrain</title>
    
    <style>
        .status-active {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800;
        }
        .status-inactive {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800;
        }
    </style>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js для графиков -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Кастомные стили */
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .status-active {
            @apply bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium;
        }
        
        .status-inactive {
            @apply bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium;
        }
        
        .status-pending {
            @apply bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-gray-800 text-white w-64 sidebar-transition">
            <div class="p-6">
                <div class="flex items-center">
                    <i class="fas fa-dumbbell text-2xl text-blue-400 mr-3"></i>
                    <h1 class="text-xl font-bold">FitTrain Admin</h1>
                </div>
            </div>
            
            <nav class="mt-6">
                <div class="px-6 mb-4">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Основное</p>
                </div>
                
                <a href="{{ route('admin.dashboard.main') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.dashboard*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Дашборд
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.users*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    Пользователи
                </a>
                
                <a href="{{ route('admin.languages.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.languages*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-language mr-3"></i>
                    Языки
                </a>
                
                <a href="{{ route('admin.currencies.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.currencies*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-dollar-sign mr-3"></i>
                    Валюты
                </a>
                
                <div class="px-6 mb-4 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Аналитика</p>
                </div>
                
                <a href="{{ route('admin.statistics.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.statistics*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    Статистика
                </a>
                
                <div class="px-6 mb-4 mt-6">
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Система</p>
                </div>
                
                <a href="{{ route('admin.system.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.system*') ? 'bg-gray-700 text-white' : '' }}">
                    <i class="fas fa-cogs mr-3"></i>
                    Система
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-2xl font-semibold text-gray-800 ml-4">@yield('page-title', 'Админ панель')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="text-gray-500 hover:text-gray-700 relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">Администратор</p>
                                </div>
                                <img class="h-8 w-8 rounded-full bg-gray-300" src="https://via.placeholder.com/32" alt="Avatar">
                            </div>
                        </div>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-sign-out-alt text-xl"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-6 mt-4 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-6 mt-4 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
        
        // Auto-hide flash messages
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('.bg-green-100, .bg-red-100');
            flashMessages.forEach(function(msg) {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(function() {
                    msg.remove();
                }, 500);
            });
        }, 5000);
        
        // Confirm dialogs
        function confirmDelete(message = 'Вы уверены?') {
            return confirm(message);
        }
    </script>
    
    @yield('scripts')
</body>
</html>

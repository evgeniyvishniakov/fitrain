<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ панель - Fitrain Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-red-50 to-pink-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Логотип и заголовок -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Fitrain Admin</h1>
                <p class="text-gray-600 mt-2">Войдите в админ панель</p>
            </div>

            <!-- Форма входа -->
            <form method="POST" action="{{ route('admin.login') }}" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                            placeholder="Введите ваш email"
                            required
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                            placeholder="Введите ваш пароль"
                            required
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ошибки -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Ошибка входа
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Кнопка входа -->
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Войти в админ панель
                </button>
            </form>

            <!-- Дополнительная информация -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-600">
                    <a href="{{ route('landing.home') }}" class="font-medium text-red-600 hover:text-red-500">
                        Вернуться на главный сайт
                    </a>
                </p>
            </div>
        </div>

        <!-- Футер -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                © 2024 Fitrain Admin. Все права защищены.
            </p>
        </div>
    </div>
</body>
</html>

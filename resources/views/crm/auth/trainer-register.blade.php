<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.registration_title') }} - Fitrain CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center py-12">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 relative">
            <!-- Выбор языка в верхнем правом углу блока -->
            <div class="absolute top-4 right-4">
                <select id="language_code_select" 
                        onchange="window.location.href='?lang='+this.value"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors cursor-pointer">
                    @foreach($languages as $language)
                        <option value="{{ $language->code }}" {{ app()->getLocale() === $language->code ? 'selected' : '' }}>
                            {{ $language->flag }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Логотип и заголовок -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-tie text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('auth.registration_title') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('auth.registration_subtitle') }}</p>
            </div>

            <!-- Информация о пробном периоде -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-900">
                            🎁 {{ __('auth.trial_period') }}
                        </h3>
                        <p class="mt-1 text-sm text-blue-700">
                            {{ __('auth.trial_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Форма регистрации -->
            <form method="POST" action="{{ route('crm.trainer.register') }}" class="space-y-6">
                @csrf
                
                <!-- Скрытое поле языка -->
                <input type="hidden" name="language_code" value="{{ app()->getLocale() }}">
                
                <!-- Имя -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>{{ __('common.name') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                            placeholder="{{ __('auth.enter_name') }}"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>{{ __('auth.email') }}
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
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                            placeholder="{{ __('auth.enter_email') }}"
                            required
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('auth.gmail_future') }}
                    </p>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Пароль -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>{{ __('auth.password') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                            placeholder="{{ __('auth.enter_password') }}"
                            required
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Подтверждение пароля -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>{{ __('auth.password_confirmation') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="{{ __('auth.repeat_password') }}"
                            required
                        >
                    </div>
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
                                    {{ __('common.error') }}
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

                <!-- Кнопка регистрации -->
                <button 
                    type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('auth.register_btn') }}
                </button>
            </form>

            <!-- Дополнительная информация -->
            <div class="mt-8 text-center space-y-2">
                <p class="text-sm text-gray-600">
                    {{ __('auth.already_have_account') }} 
                    <a href="{{ route('crm.login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        {{ __('auth.login') }}
                    </a>
                </p>
                <p class="text-xs text-gray-500">
                    {{ __('auth.terms_notice') }} <a href="#" class="text-blue-600 hover:underline">{{ __('auth.terms_use') }}</a>
                </p>
            </div>
        </div>

        <!-- Футер -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                © 2024 Fitrain. {{ __('common.all_rights_reserved') }}
            </p>
        </div>
    </div>
</body>
</html>

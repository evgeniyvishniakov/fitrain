@extends("crm.layouts.app")

@section("title", __('common.settings'))
@section("page-title", __('common.settings'))

<script>
// SPA функциональность для настроек
function settingsApp() {
    return {
        currentTab: 'profile', // profile, security, preferences, notifications
        
        // Навигация по вкладкам
        switchTab(tabName) {
            this.currentTab = tabName;
            
            // Обновляем активную вкладку
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            const activeBtn = document.getElementById(`tab-${tabName}`);
            if (activeBtn) {
                activeBtn.classList.remove('border-transparent', 'text-gray-500');
                activeBtn.classList.add('border-blue-500', 'text-blue-600');
            }
            
            // Показываем нужный контент
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            const activeContent = document.getElementById(`content-${tabName}`);
            if (activeContent) {
                activeContent.style.display = 'block';
            }
        }
    }
}
</script>

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
{{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
{{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
{{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
{{ __('common.clients') }}
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
{{ __('common.subscription') }}
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
{{ __('common.settings') }}
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
{{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
{{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
{{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
{{ __('common.clients') }}
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
{{ __('common.subscription') }}
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
{{ __('common.settings') }}
    </a>
@endsection

@section("header-actions")
    <!-- Кнопки действий в заголовке -->
@endsection

@section("content")
<div x-data="settingsApp()" x-cloak class="space-y-6">
    

    <!-- Основной контент -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <!-- Навигация по вкладкам -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-button py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ __('common.profile') }}
                </button>
                <button onclick="switchTab('security')" id="tab-security" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ __('common.security') }}
                </button>
                <button onclick="switchTab('preferences')" id="tab-preferences" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    {{ __('common.language') }} и {{ __('common.currency') }}
                </button>
                <button onclick="switchTab('notifications')" id="tab-notifications" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ __('common.notifications') }}
                </button>
            </nav>
        </div>

        <!-- Содержимое вкладок -->
        <div class="p-6">
            <!-- Вкладка Профиль -->
            <div id="content-profile" class="tab-content">
                <form method="POST" action="{{ route('crm.trainer.settings.profile') }}" class="space-y-6">
                    @csrf
                    <div class="profile-grid" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                        <style>
                            @media (min-width: 1024px) {
                                .profile-grid {
                                    grid-template-columns: 1fr 1fr !important;
                                }
                            }
                        </style>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.name') }}</label>
                            <input type="text" id="name" name="name" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.email') }}</label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.phone') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.specialization') }}</label>
                            <input type="text" id="specialization" name="specialization" value="{{ $user->specialization ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.experience_years') }}</label>
                            <input type="number" id="experience_years" name="experience_years" value="{{ $user->experience_years ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="certification" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.certification') }}</label>
                            <input type="text" id="certification" name="certification" value="{{ $user->certification ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.bio') }}</label>
                        <textarea id="bio" name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $user->bio ?? '' }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('common.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Вкладка Безопасность -->
            <div id="content-security" class="tab-content" style="display: none;">
                <form method="POST" action="{{ route('crm.trainer.settings.password') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.new_password') }}</label>
                        <input type="password" id="new_password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.new_password_confirmation') }}</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('common.change_password') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Вкладка Язык и валюта -->
            <div id="content-preferences" class="tab-content" style="display: none;">
                <div class="space-y-6">
                    
                    <!-- Скрытые поля для значений -->
                    <input type="hidden" id="language_code_hidden" name="language_code" value="{{ $user->language_code ?? 'ru' }}">
                    <input type="hidden" id="currency_code_hidden" name="currency_code" value="{{ $user->currency_code ?? 'RUB' }}">
                    
                    <!-- Язык -->
                    <div class="space-y-4">
                        <h3 class="text-base font-medium text-gray-900">
                            <i class="fas fa-language mr-2"></i>{{ __('common.interface_language') }}
                        </h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                            @foreach(\App\Models\Language::getActive() as $language)
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ ($user->language_code ?? 'ru') === $language->code ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" onclick="updateLanguageCode('{{ $language->code }}')" style="flex: 1; min-width: 200px; max-width: 300px;">
                                    <input type="radio" name="language_radio" value="{{ $language->code }}" 
                                           {{ ($user->language_code ?? 'ru') === $language->code ? 'checked' : '' }}
                                           class="sr-only" onchange="updateLanguageCode('{{ $language->code }}')">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl">{{ $language->flag }}</span>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $language->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $language->native_name }}</div>
                                        </div>
                                    </div>
                                    @if(($user->language_code ?? 'ru') === $language->code)
                                        <div class="absolute top-2 right-2">
                                            <i class="fas fa-check-circle text-blue-500"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Валюта -->
                    <div class="space-y-4">
                        <h3 class="text-base font-medium text-gray-900">
                            <i class="fas fa-dollar-sign mr-2"></i>{{ __('common.currency') }}
                        </h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                            @foreach(\App\Models\Currency::getActive() as $currency)
                                <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ ($user->currency_code ?? 'RUB') === $currency->code ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" onclick="updateCurrencyCode('{{ $currency->code }}')" style="flex: 1; min-width: 200px; max-width: 300px;">
                                    <input type="radio" name="currency_radio" value="{{ $currency->code }}" 
                                           {{ ($user->currency_code ?? 'RUB') === $currency->code ? 'checked' : '' }}
                                           class="sr-only" onchange="updateCurrencyCode('{{ $currency->code }}')">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-medium">{{ $currency->symbol }}</span>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $currency->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $currency->code }}</div>
                                        </div>
                                    </div>
                                    @if(($user->currency_code ?? 'RUB') === $currency->code)
                                        <div class="absolute top-2 right-2">
                                            <i class="fas fa-check-circle text-blue-500"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Часовой пояс -->
                    <div class="space-y-4">
                        <h3 class="text-base font-medium text-gray-900">
                            <i class="fas fa-clock mr-2"></i>{{ __('common.timezone') }}
                        </h3>
                        <div class="max-w-md">
                            <select id="timezone_select" name="timezone" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Europe/Moscow" {{ $user->timezone === 'Europe/Moscow' ? 'selected' : '' }}>Москва (UTC+3)</option>
                                <option value="Europe/London" {{ $user->timezone === 'Europe/London' ? 'selected' : '' }}>Лондон (UTC+0)</option>
                                <option value="Europe/Berlin" {{ $user->timezone === 'Europe/Berlin' ? 'selected' : '' }}>Берлин (UTC+1)</option>
                                <option value="Europe/Paris" {{ $user->timezone === 'Europe/Paris' ? 'selected' : '' }}>Париж (UTC+1)</option>
                                <option value="America/New_York" {{ $user->timezone === 'America/New_York' ? 'selected' : '' }}>Нью-Йорк (UTC-5)</option>
                                <option value="America/Los_Angeles" {{ $user->timezone === 'America/Los_Angeles' ? 'selected' : '' }}>Лос-Анджелес (UTC-8)</option>
                                <option value="Asia/Tokyo" {{ $user->timezone === 'Asia/Tokyo' ? 'selected' : '' }}>Токио (UTC+9)</option>
                                <option value="Asia/Shanghai" {{ $user->timezone === 'Asia/Shanghai' ? 'selected' : '' }}>Шанхай (UTC+8)</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Вкладка Уведомления -->
            <div id="content-notifications" class="tab-content" style="display: none;">
                <form method="POST" action="{{ route('crm.trainer.settings.notifications') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('common.email_notifications') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('common.receive_email_notifications') }}</p>
                            </div>
                            <input type="checkbox" name="email_notifications" value="1" {{ ($user->email_notifications ?? true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('common.sms_notifications') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('common.receive_sms_notifications') }}</p>
                            </div>
                            <input type="checkbox" name="sms_notifications" value="1" {{ ($user->sms_notifications ?? false) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('common.push_notifications') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('common.receive_push_notifications') }}</p>
                            </div>
                            <input type="checkbox" name="push_notifications" value="1" {{ ($user->push_notifications ?? true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('common.save_settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Функция переключения вкладок
function switchTab(tabName) {
    // Обновляем активную вкладку
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeBtn = document.getElementById(`tab-${tabName}`);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-gray-500');
        activeBtn.classList.add('border-blue-500', 'text-blue-600');
    }
    
    // Показываем нужный контент
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    const activeContent = document.getElementById(`content-${tabName}`);
    if (activeContent) {
        activeContent.style.display = 'block';
    }
}

// Функции для переключения языка и валюты
function updateLanguageCode(code) {
    document.getElementById('language_code_hidden').value = code;
    // Обновляем визуальное состояние
    document.querySelectorAll('label[onclick*="updateLanguageCode"]').forEach(label => {
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
    });
    event.target.closest('label').classList.remove('border-gray-200');
    event.target.closest('label').classList.add('border-blue-500', 'bg-blue-50');
    
    // Автоматически сохраняем настройки
    saveSettings('language');
}

function updateCurrencyCode(code) {
    document.getElementById('currency_code_hidden').value = code;
    // Обновляем визуальное состояние
    document.querySelectorAll('label[onclick*="updateCurrencyCode"]').forEach(label => {
        label.classList.remove('border-blue-500', 'bg-blue-50');
        label.classList.add('border-gray-200');
    });
    event.target.closest('label').classList.remove('border-gray-200');
    event.target.closest('label').classList.add('border-blue-500', 'bg-blue-50');
    
    // Автоматически сохраняем настройки
    saveSettings('currency');
}

async function saveSettings(type) {
    const languageCode = document.getElementById('language_code_hidden').value;
    const currencyCode = document.getElementById('currency_code_hidden').value;
    const timezone = document.getElementById('timezone_select').value;
    
    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('language_code', languageCode);
        formData.append('currency_code', currencyCode);
        formData.append('timezone', timezone);
        
        const response = await fetch('{{ route("crm.trainer.settings.preferences") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        if (response.ok) {
            // Показываем соответствующее уведомление
            if (type === 'language') {
                showSuccess('{{ __('common.language_updated') }}', '{{ __('common.interface_language_changed') }}');
                // Сохраняем активную вкладку в localStorage
                localStorage.setItem('activeSettingsTab', 'preferences');
                // Перезагружаем страницу только при смене языка
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else if (type === 'currency') {
                showSuccess('{{ __('common.currency_updated') }}', '{{ __('common.currency_changed') }}');
                // При смене валюты не перезагружаем страницу
            } else if (type === 'timezone') {
                showSuccess('{{ __('common.timezone_updated') }}', '{{ __('common.timezone_changed') }}');
                // При смене часового пояса не перезагружаем страницу
            } else {
                showSuccess('{{ __('common.settings_saved') }}', '{{ __('common.settings_updated') }}');
            }
        } else {
            throw new Error('Ошибка сохранения');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        showError('{{ __('common.error') }}', '{{ __('common.failed_to_save_settings') }}');
    }
}

// Восстанавливаем активную вкладку после перезагрузки
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        switchTab(activeTab);
        localStorage.removeItem('activeSettingsTab'); // Очищаем после использования
    }
    
    // Добавляем обработчик изменения часового пояса
    const timezoneSelect = document.getElementById('timezone_select');
    if (timezoneSelect) {
        timezoneSelect.addEventListener('change', function() {
            saveSettings('timezone');
        });
    }
});
</script>
@endsection
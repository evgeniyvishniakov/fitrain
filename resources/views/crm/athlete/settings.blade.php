@extends("crm.layouts.app")

@section("title", "Настройки спортсмена")
@section("page-title", "Настройки")

@section("content")
<div class="space-y-6">
    <!-- Заголовок -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Настройки</h1>
                <p class="text-gray-600 mt-1">Управление вашим профилем и настройками</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Основная информация -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Основная информация</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->name }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->email }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->phone ?? 'Не указан' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Возраст</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->age ?? 'Не указан' }} лет
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    @if($athlete->gender === 'male')
                        Мужской
                    @elseif($athlete->gender === 'female')
                        Женский
                    @else
                        Не указан
                    @endif
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Уровень подготовки</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    @if($athlete->sport_level === 'beginner')
                        Начинающий
                    @elseif($athlete->sport_level === 'intermediate')
                        Средний
                    @elseif($athlete->sport_level === 'advanced')
                        Продвинутый
                    @else
                        Не указан
                    @endif
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <a href="{{ route('crm.athlete.profile') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Редактировать профиль
            </a>
        </div>
    </div>

    <!-- Физические параметры -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Физические параметры</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Рост</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->height ? $athlete->height . ' см' : 'Не указан' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Вес</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->weight ? $athlete->weight . ' кг' : 'Не указан' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    {{ $athlete->birth_date ? $athlete->birth_date->format('d.m.Y') : 'Не указана' }}
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Цели</label>
                <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900">
                    @if($athlete->goals && count($athlete->goals) > 0)
                        {{ implode(', ', array_map(function($goal) {
                            return match($goal) {
                                'weight_loss' => 'Похудение',
                                'muscle_gain' => 'Набор мышечной массы',
                                'muscle_tone' => 'Тонизация мышц',
                                'endurance' => 'Выносливость',
                                'strength' => 'Сила',
                                'flexibility' => 'Гибкость',
                                default => $goal
                            };
                        }, $athlete->goals)) }}
                    @else
                        Не указаны
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Статус аккаунта -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Статус аккаунта</h2>
        
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-700">Статус</p>
                <p class="text-sm text-gray-600">
                    @if($athlete->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Активен
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Неактивен
                        </span>
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-700">Дата регистрации</p>
                <p class="text-sm text-gray-600">{{ $athlete->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Действия -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Действия</h2>
        
        <div class="space-y-4">
            <a href="{{ route('crm.athlete.profile') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Редактировать профиль
            </a>
            
            <a href="{{ route('crm.athlete.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors ml-4">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                </svg>
                Вернуться в дашборд
            </a>
        </div>
    </div>
</div>
@endsection

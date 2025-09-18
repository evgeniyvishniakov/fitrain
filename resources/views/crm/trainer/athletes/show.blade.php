@extends("crm.layouts.app")

@section("title", $athlete->name . " - Профиль спортсмена")
@section("page-title", "Профиль спортсмена")

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.progress.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Спортсмены
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("content")
<div class="p-6">
    <!-- Заголовок и навигация -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('crm.trainer.athletes') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $athlete->name }}</h1>
                <p class="text-gray-600">Профиль спортсмена</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Редактировать
            </button>
            <button class="px-4 py-2 text-red-600 hover:text-red-800 border border-red-300 rounded-lg">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Удалить
            </button>
        </div>
    </div>

    <!-- Карточка спортсмена с вкладками -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Заголовок карточки -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center">
                <!-- Аватар -->
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mr-6">
                    @if($athlete->avatar)
                        <img src="{{ $athlete->avatar }}" alt="{{ $athlete->name }}" class="w-20 h-20 rounded-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    @endif
                </div>
                
                <!-- Основная информация -->
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $athlete->name }}</h2>
                    <p class="text-gray-600 mb-2">{{ $athlete->email }}</p>
                    <div class="flex gap-6 text-sm text-gray-500">
                        <span>Возраст: {{ $athlete->age ?? '—' }}</span>
                        <span>Вес: {{ $athlete->weight ?? '—' }} кг</span>
                        <span>Рост: {{ $athlete->height ?? '—' }} см</span>
                    </div>
                </div>
                
                <!-- Быстрая статистика -->
                <div class="text-right">
                    <div class="text-3xl font-bold text-indigo-600">
                        @if($athlete->weight && $athlete->height)
                            {{ number_format($athlete->weight / (($athlete->height/100) ** 2), 1) }}
                        @else
                            —
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">ИМТ</div>
                </div>
            </div>
        </div>

        <!-- Вкладки -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('overview')" id="tab-overview" class="tab-button active py-4 px-1 border-b-2 border-indigo-500 text-indigo-600 font-medium text-sm">
                    Обзор
                </button>
                <button onclick="showTab('general')" id="tab-general" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Общие данные
                </button>
                <button onclick="showTab('medical')" id="tab-medical" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Медицинские данные
                </button>
                <button onclick="showTab('measurements')" id="tab-measurements" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Измерения
                </button>
                <button onclick="showTab('progress')" id="tab-progress" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Прогресс
                </button>
                <button onclick="showTab('workouts')" id="tab-workouts" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Тренировки
                </button>
                <button onclick="showTab('nutrition')" id="tab-nutrition" class="tab-button py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Питание
                </button>
            </nav>
        </div>

        <!-- Содержимое вкладок -->
        <div class="p-6">
            <!-- Вкладка "Обзор" -->
            <div id="content-overview" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Последние тренировки -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Последние тренировки</h3>
                        <div class="space-y-3">
                            @forelse($athlete->athleteWorkouts->take(5) as $workout)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $workout->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $workout->date ? $workout->date->format('d.m.Y') : '—' }}</div>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($workout->status === 'completed') bg-green-100 text-green-800
                                    @elseif($workout->status === 'planned') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($workout->status === 'completed') Выполнено
                                    @elseif($workout->status === 'planned') Запланировано
                                    @else Отменено @endif
                                </span>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Нет тренировок
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Быстрая статистика -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600">{{ $athlete->athleteWorkouts->count() }}</div>
                                <div class="text-sm text-gray-600">Всего тренировок</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $athlete->athleteWorkouts->where('status', 'completed')->count() }}</div>
                                <div class="text-sm text-gray-600">Выполнено</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $athlete->athleteWorkouts->where('status', 'planned')->count() }}</div>
                                <div class="text-sm text-gray-600">Запланировано</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка "Общие данные" -->
            <div id="content-general" class="tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Личная информация -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Личная информация</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Полное имя</label>
                                <div class="text-gray-900 font-medium">{{ $athlete->name }}</div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <div class="text-gray-900">{{ $athlete->email }}</div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Телефон</label>
                                <div class="text-gray-900">{{ $athlete->phone ?? '—' }}</div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Пол</label>
                                <div class="text-gray-900">
                                    @if($athlete->gender)
                                        @switch($athlete->gender)
                                            @case('male') Мужской @break
                                            @case('female') Женский @break
                                            @case('other') Другой @break
                                            @default {{ $athlete->gender }}
                                        @endswitch
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Дата рождения</label>
                                <div class="text-gray-900">{{ $athlete->birth_date ? $athlete->birth_date->format('d.m.Y') : '—' }}</div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Возраст</label>
                                <div class="text-gray-900 font-semibold">{{ $athlete->age ?? '—' }} лет</div>
                            </div>
                        </div>
                        
                        <!-- Контактная информация -->
                        @if($athlete->contact_info)
                        <div class="mt-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Дополнительные контакты</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($athlete->contact_info as $type => $value)
                                <div>
                                    <label class="text-sm font-medium text-gray-500">{{ ucfirst($type) }}</label>
                                    <div class="text-gray-900">{{ $value }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Спортивная информация -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Спортивная информация</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Спортивный уровень</label>
                                <div class="mt-1">
                                    @if($athlete->sport_level)
                                        @php
                                            $levelLabels = [
                                                'beginner' => 'Новичок',
                                                'intermediate' => 'Любитель',
                                                'advanced' => 'Профи'
                                            ];
                                            $levelColors = [
                                                'beginner' => 'bg-blue-100 text-blue-800',
                                                'intermediate' => 'bg-yellow-100 text-yellow-800',
                                                'advanced' => 'bg-green-100 text-green-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $levelColors[$athlete->sport_level] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $levelLabels[$athlete->sport_level] ?? $athlete->sport_level }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Цели</label>
                                <div class="mt-2">
                                    @if($athlete->goals && is_array($athlete->goals))
                                        @foreach($athlete->goals as $goal)
                                            <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full mr-1 mb-1">{{ $goal }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Статус</label>
                                <div class="mt-1">
                                    @if($athlete->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Активен
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Неактивен
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка "Медицинские данные" -->
            <div id="content-medical" class="tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Физические параметры -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Физические параметры</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $athlete->current_weight ?? $athlete->weight ?? '—' }}</div>
                                <div class="text-sm text-blue-800">Вес (кг)</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $athlete->current_height ?? $athlete->height ?? '—' }}</div>
                                <div class="text-sm text-green-800">Рост (см)</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $athlete->bmi ?? '—' }}</div>
                                <div class="text-sm text-purple-800">ИМТ</div>
                            </div>
                        </div>
                        
                        <!-- История измерений -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-md font-semibold text-gray-900">История измерений</h4>
                                <button class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Добавить измерение
                                </button>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-500 text-center py-4">История измерений будет отображаться здесь</p>
                            </div>
                        </div>
                        
                        <!-- Медицинские справки -->
                        @if($athlete->medical_documents)
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Медицинские справки</h4>
                            <div class="space-y-2">
                                @foreach($athlete->medical_documents as $doc)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doc['name'] ?? 'Справка' }}</div>
                                        <div class="text-sm text-gray-500">{{ $doc['date'] ?? '' }}</div>
                                    </div>
                                    <button class="text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Ограничения по здоровью -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ограничения по здоровью</h3>
                        <div class="space-y-3">
                            @if($athlete->health_restrictions && is_array($athlete->health_restrictions))
                                @foreach($athlete->health_restrictions as $restriction)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                    <div class="font-medium text-red-800">{{ $restriction['type'] ?? 'Ограничение' }}</div>
                                    <div class="text-sm text-red-600 mt-1">{{ $restriction['description'] ?? '' }}</div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p>Нет ограничений</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Последний медосмотр -->
                        @if($athlete->last_medical_checkup)
                        <div class="mt-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-3">Последний медосмотр</h4>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="text-green-800 font-medium">{{ $athlete->last_medical_checkup->format('d.m.Y') }}</div>
                                <div class="text-sm text-green-600">Допуск к тренировкам</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Вкладка "Измерения" -->
            <div id="content-measurements" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Заголовок с кнопкой добавления -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">История измерений</h3>
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить измерение
                        </button>
                    </div>
                    
                    <!-- Фильтры -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex flex-wrap gap-4">
                            <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option>Все измерения</option>
                                <option>Последний месяц</option>
                                <option>Последние 3 месяца</option>
                                <option>Последний год</option>
                            </select>
                            <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option>Все параметры</option>
                                <option>Вес и рост</option>
                                <option>Объемы тела</option>
                                <option>Процент жира</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- График измерений -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">График изменений</h4>
                        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p>График будет отображаться здесь</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Таблица измерений -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h4 class="text-md font-semibold text-gray-900">Последние измерения</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Вес</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Рост</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ИМТ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% жира</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            <p>Нет данных об измерениях</p>
                                            <p class="text-sm">Добавьте первое измерение для отслеживания прогресса</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка "Прогресс" -->
            <div id="content-progress" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Заголовок с кнопкой добавления -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Прогресс и аналитика</h3>
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить запись
                        </button>
                    </div>
                    
                    <!-- Статистические карточки -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">0</div>
                            <div class="text-sm text-blue-800">Личных рекордов</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">0</div>
                            <div class="text-sm text-green-800">Тренировок выполнено</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">0</div>
                            <div class="text-sm text-purple-800">Дней тренировок</div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600">0</div>
                            <div class="text-sm text-orange-800">Часов тренировок</div>
                        </div>
                    </div>
                    
                    <!-- Графики прогресса -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- График силовых показателей -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Силовые показатели</h4>
                            <div class="h-48 bg-gray-50 rounded-lg flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="text-sm">График силовых показателей</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- График выносливости -->
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Выносливость</h4>
                            <div class="h-48 bg-gray-50 rounded-lg flex items-center justify-center">
                                <div class="text-center text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="text-sm">График выносливости</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Личные рекорды -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Личные рекорды</h4>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            <p>Нет личных рекордов</p>
                            <p class="text-sm">Начните тренировки для отслеживания рекордов</p>
                        </div>
                    </div>
                    
                    <!-- Фото до/после -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Фото прогресса</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                                    <div class="text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm">Фото "До"</p>
                                    </div>
                                </div>
                                <button class="text-sm text-indigo-600 hover:text-indigo-800">Добавить фото</button>
                            </div>
                            <div class="text-center">
                                <div class="h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                                    <div class="text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm">Фото "После"</p>
                                    </div>
                                </div>
                                <button class="text-sm text-indigo-600 hover:text-indigo-800">Добавить фото</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Вкладка "Тренировки" -->
            <div id="content-workouts" class="tab-content hidden">
                <div class="space-y-4">
                    @forelse($athlete->athleteWorkouts as $workout)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">{{ $workout->title }}</h4>
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($workout->status === 'completed') bg-green-100 text-green-800
                                @elseif($workout->status === 'planned') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                @if($workout->status === 'completed') Выполнено
                                @elseif($workout->status === 'planned') Запланировано
                                @else Отменено @endif
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 mb-2">{{ $workout->date ? $workout->date->format('d.m.Y') : '—' }}</div>
                        @if($workout->description)
                            <div class="text-sm text-gray-700">{{ $workout->description }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Нет тренировок</h3>
                        <p class="mb-4">Создайте первую тренировку для этого спортсмена</p>
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Создать тренировку
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Вкладка "Питание" -->
            <div id="content-nutrition" class="tab-content hidden">
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Питание</h3>
                    <p class="mb-4">Здесь будет информация о питании и диете</p>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        Добавить запись
                    </button>
                </div>
            </div>

            <!-- Вкладка "Питание" -->
            <div id="content-nutrition" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Заголовок с кнопкой добавления -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Питание и диета</h3>
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить запись
                        </button>
                    </div>
                    
                    <!-- Статистика питания -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">0</div>
                            <div class="text-sm text-red-800">Калорий сегодня</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">0</div>
                            <div class="text-sm text-blue-800">Белков (г)</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">0</div>
                            <div class="text-sm text-yellow-800">Углеводов (г)</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">0</div>
                            <div class="text-sm text-green-800">Жиров (г)</div>
                        </div>
                    </div>
                    
                    <!-- Планы питания -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Планы питания</h4>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p>Нет планов питания</p>
                            <p class="text-sm">Создайте план питания для спортсмена</p>
                        </div>
                    </div>
                    
                    <!-- Дневник питания -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Дневник питания</h4>
                        <div class="space-y-4">
                            <!-- Завтрак -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">Завтрак</h5>
                                    <span class="text-sm text-gray-500">0 ккал</span>
                                </div>
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <p class="text-sm">Добавить блюдо</p>
                                </div>
                            </div>
                            
                            <!-- Обед -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">Обед</h5>
                                    <span class="text-sm text-gray-500">0 ккал</span>
                                </div>
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <p class="text-sm">Добавить блюдо</p>
                                </div>
                            </div>
                            
                            <!-- Ужин -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">Ужин</h5>
                                    <span class="text-sm text-gray-500">0 ккал</span>
                                </div>
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <p class="text-sm">Добавить блюдо</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отчеты по питанию -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Отчеты по питанию</h4>
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>Нет отчетов по питанию</p>
                            <p class="text-sm">Отчеты будут отображаться здесь</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Скрываем все вкладки
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Убираем активный класс со всех кнопок
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Показываем нужную вкладку
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Активируем нужную кнопку
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-indigo-500', 'text-indigo-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}
</script>
@endsection

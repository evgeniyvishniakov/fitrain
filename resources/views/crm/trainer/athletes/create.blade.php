@extends("crm.layouts.app")

@section("title", "Добавить спортсмена")
@section("page-title", "Добавить спортсмена")

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
    <div class="flex items-center mb-6">
        <a href="{{ route('crm.trainer.athletes') }}" class="text-gray-500 hover:text-gray-700 mr-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Добавить спортсмена</h1>
            <p class="text-gray-600">Создание нового профиля спортсмена</p>
        </div>
    </div>

    <!-- Форма добавления -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('crm.trainer.store-athlete') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Левая колонка -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Основная информация</h3>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Полное имя *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Пароль *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('birth_date') border-red-500 @enderror">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                        <select id="gender" name="gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('gender') border-red-500 @enderror">
                            <option value="">Выберите пол</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Другой</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Правая колонка -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Физические параметры</h3>
                    
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                        <input type="number" id="weight" name="weight" value="{{ old('weight') }}" step="0.1" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('weight') border-red-500 @enderror">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                        <input type="number" id="height" name="height" value="{{ old('height') }}" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('height') border-red-500 @enderror">
                        @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sport_level" class="block text-sm font-medium text-gray-700 mb-2">Спортивный уровень</label>
                        <select id="sport_level" name="sport_level"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('sport_level') border-red-500 @enderror">
                            <option value="">Выберите уровень</option>
                            <option value="beginner" {{ old('sport_level') == 'beginner' ? 'selected' : '' }}>Новичок</option>
                            <option value="intermediate" {{ old('sport_level') == 'intermediate' ? 'selected' : '' }}>Любитель</option>
                            <option value="advanced" {{ old('sport_level') == 'advanced' ? 'selected' : '' }}>Профи</option>
                        </select>
                        @error('sport_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цели</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="goals[]" value="weight_loss" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Похудение</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="goals[]" value="muscle_gain" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Набор массы</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="goals[]" value="competition_prep" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Подготовка к соревнованиям</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="goals[]" value="general_fitness" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Общая физическая подготовка</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="health_restrictions" class="block text-sm font-medium text-gray-700 mb-2">Ограничения по здоровью</label>
                        <textarea id="health_restrictions" name="health_restrictions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('health_restrictions') border-red-500 @enderror"
                                  placeholder="Опишите любые травмы, хронические заболевания или ограничения...">{{ old('health_restrictions') }}</textarea>
                        @error('health_restrictions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('crm.trainer.athletes') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Создать спортсмена
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


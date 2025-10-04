@extends('admin.layouts.app')

@section('title', 'Добавить язык')
@section('page-title', 'Добавить язык')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Новый язык</h3>
                <a href="{{ route('admin.languages.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.languages.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Код языка -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-code mr-2"></i>Код языка
                </label>
                <input type="text" name="code" id="code" 
                       value="{{ old('code') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                       placeholder="ru, en, de, fr..."
                       maxlength="5"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">ISO код языка (например: ru, en, de)</p>
            </div>

            <!-- Название -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-globe mr-2"></i>Название языка
                </label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Русский, English, Deutsch..."
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Родное название -->
            <div>
                <label for="native_name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-flag mr-2"></i>Родное название
                </label>
                <input type="text" name="native_name" id="native_name" 
                       value="{{ old('native_name') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('native_name') border-red-500 @enderror"
                       placeholder="Русский, English, Deutsch..."
                       required>
                @error('native_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Как называется язык на самом языке</p>
            </div>

            <!-- Флаг -->
            <div>
                <label for="flag" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-flag mr-2"></i>Флаг (эмодзи)
                </label>
                <input type="text" name="flag" id="flag" 
                       value="{{ old('flag') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('flag') border-red-500 @enderror"
                       placeholder="🇷🇺, 🇺🇸, 🇩🇪..."
                       maxlength="10">
                @error('flag')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Эмодзи флага страны (необязательно)</p>
            </div>

            <!-- Порядок сортировки -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sort mr-2"></i>Порядок сортировки
                </label>
                <input type="number" name="sort_order" id="sort_order" 
                       value="{{ old('sort_order', 0) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror"
                       min="0"
                       required>
                @error('sort_order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Чем меньше число, тем выше в списке</p>
            </div>

            <!-- Статусы -->
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle mr-1"></i>Активный язык
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Неактивные языки не будут доступны пользователям</p>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_default" value="1" 
                               {{ old('is_default', false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-star mr-1"></i>Язык по умолчанию
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Будет установлен для новых пользователей</p>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.languages.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Создать язык
                </button>
            </div>
        </form>
    </div>

    <!-- Информация -->
    <div class="mt-6 bg-blue-50 rounded-xl p-6">
        <h4 class="text-sm font-medium text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>Информация о языках
        </h4>
        <div class="space-y-2 text-sm text-blue-800">
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Код языка должен быть уникальным (ISO стандарт)</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Только один язык может быть установлен по умолчанию</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Неактивные языки скрыты от пользователей</span>
            </div>
        </div>
    </div>
</div>
@endsection












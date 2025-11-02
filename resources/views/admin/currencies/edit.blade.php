@extends('admin.layouts.app')

@section('title', 'Редактировать валюту')
@section('page-title', 'Редактировать валюту')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Редактировать валюту: {{ $currency->name }}</h3>
                <a href="{{ route('admin.currencies.show', $currency) }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.currencies.update', $currency) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Код валюты -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-code mr-2"></i>Код валюты
                </label>
                <input type="text" name="code" id="code" 
                       value="{{ old('code', $currency->code) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                       placeholder="RUB, USD, EUR..."
                       maxlength="3"
                       style="text-transform: uppercase"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">ISO код валюты (например: RUB, USD, EUR)</p>
            </div>

            <!-- Название -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-dollar-sign mr-2"></i>Название валюты
                </label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $currency->name) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Российский рубль, US Dollar, Euro..."
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Символ -->
            <div>
                <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-symbol mr-2"></i>Символ валюты
                </label>
                <input type="text" name="symbol" id="symbol" 
                       value="{{ old('symbol', $currency->symbol) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('symbol') border-red-500 @enderror"
                       placeholder="₽, $, €, £..."
                       maxlength="10"
                       required>
                @error('symbol')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Позиция символа -->
            <div>
                <label for="symbol_position" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2"></i>Позиция символа
                </label>
                <select name="symbol_position" id="symbol_position" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('symbol_position') border-red-500 @enderror"
                        required>
                    <option value="before" {{ old('symbol_position', $currency->symbol_position) === 'before' ? 'selected' : '' }}>Перед суммой ($100)</option>
                    <option value="after" {{ old('symbol_position', $currency->symbol_position) === 'after' ? 'selected' : '' }}>После суммы (100₽)</option>
                </select>
                @error('symbol_position')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Количество знаков после запятой -->
            <div>
                <label for="decimal_places" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-calculator mr-2"></i>Знаков после запятой
                </label>
                <input type="number" name="decimal_places" id="decimal_places" 
                       value="{{ old('decimal_places', $currency->decimal_places) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('decimal_places') border-red-500 @enderror"
                       min="0" max="4"
                       required>
                @error('decimal_places')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Обычно 2 (100.00), для йен - 0 (100)</p>
            </div>

            <!-- Курс обмена -->
            <div>
                <label for="exchange_rate" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-exchange-alt mr-2"></i>Курс обмена
                </label>
                <input type="number" name="exchange_rate" id="exchange_rate" 
                       value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                       step="0.0001"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('exchange_rate') border-red-500 @enderror"
                       min="0"
                       required>
                @error('exchange_rate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Курс к базовой валюте (обычно 1.0000 для основной валюты)</p>
            </div>

            <!-- Порядок сортировки -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sort mr-2"></i>Порядок сортировки
                </label>
                <input type="number" name="sort_order" id="sort_order" 
                       value="{{ old('sort_order', $currency->sort_order) }}"
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
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $currency->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-check-circle mr-1"></i>Активная валюта
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Неактивные валюты не будут доступны пользователям</p>
                </div>

                <div>
                    <input type="hidden" name="is_default" value="0">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_default" value="1" 
                               {{ old('is_default', $currency->is_default) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-star mr-1"></i>Валюта по умолчанию
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Будет установлена для новых пользователей</p>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.currencies.show', $currency) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>

    <!-- Информация -->
    <div class="mt-6 bg-blue-50 rounded-xl p-6">
        <h4 class="text-sm font-medium text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>Информация о валютах
        </h4>
        <div class="space-y-2 text-sm text-blue-800">
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Код валюты должен быть уникальным (ISO стандарт)</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Только одна валюта может быть установлена по умолчанию</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check text-green-600 mr-2"></i>
                <span>Курс обмена используется для конвертации между валютами</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Автоматическое преобразование кода валюты в верхний регистр
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endsection



















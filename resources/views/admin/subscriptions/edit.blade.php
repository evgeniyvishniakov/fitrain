@extends('admin.layouts.app')

@section('title', 'Редактировать план подписки')
@section('page-title', 'Редактировать план подписки')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Редактировать план: {{ $subscription->name }}</h3>
                <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Название -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-tag mr-2"></i>Название плана
                </label>
                <input type="text" name="name" id="name" 
                       value="{{ old('name', $subscription->name) }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       placeholder="Базовый, Профессиональный..."
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Описание -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-2"></i>Описание
                </label>
                <textarea name="description" id="description" rows="3"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          placeholder="Описание плана подписки...">{{ old('description', $subscription->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Цена и валюта -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign mr-2"></i>Цена
                    </label>
                    <input type="number" name="price" id="price" 
                           value="{{ old('price', $subscription->price) }}"
                           step="0.01"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror"
                           min="0"
                           required>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="currency_code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-coins mr-2"></i>Валюта
                    </label>
                    <select name="currency_code" id="currency_code" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('currency_code') border-red-500 @enderror"
                            required>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->code }}" {{ old('currency_code', $subscription->currency_code) === $currency->code ? 'selected' : '' }}>
                                {{ $currency->code }} ({{ $currency->symbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('currency_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Статус -->
            <div>
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" 
                           {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">
                        <i class="fas fa-check-circle mr-1"></i>Активный план
                    </span>
                </label>
                <p class="mt-1 text-xs text-gray-500">Неактивные планы не будут доступны тренерам</p>
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-save mr-2"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

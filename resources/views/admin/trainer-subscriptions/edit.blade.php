@extends('admin.layouts.app')

@section('title', 'Редактировать подписку тренера')
@section('page-title', 'Редактировать подписку тренера')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Редактировать подписку</h3>
                <a href="{{ route('admin.trainer-subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.trainer-subscriptions.update', $subscription) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Тренер -->
            <div>
                <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tie mr-2"></i>Тренер
                </label>
                <select name="trainer_id" id="trainer_id" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('trainer_id') border-red-500 @enderror"
                        required>
                    <option value="">Выберите тренера</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}" {{ old('trainer_id', $subscription->trainer_id) == $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->name }} ({{ $trainer->email }})
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- План подписки (необязательно) -->
            <div>
                <label for="subscription_plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-credit-card mr-2"></i>План подписки (необязательно)
                </label>
                <select name="subscription_plan_id" id="subscription_plan_id" 
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subscription_plan_id') border-red-500 @enderror">
                    <option value="">Без плана</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('subscription_plan_id', $subscription->subscription_plan_id) == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} - {{ number_format($plan->price, 2) }} {{ $plan->currency->symbol ?? $plan->currency_code }}
                        </option>
                    @endforeach
                </select>
                @error('subscription_plan_id')
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

            <!-- Даты -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2"></i>Дата начала
                    </label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ old('start_date', $subscription->start_date->format('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('start_date') border-red-500 @enderror"
                           required>
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="expires_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-times mr-2"></i>Дата окончания
                    </label>
                    <input type="date" name="expires_date" id="expires_date" 
                           value="{{ old('expires_date', $subscription->expires_date->format('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expires_date') border-red-500 @enderror"
                           required>
                    @error('expires_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Пробный период -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <input type="hidden" name="is_trial" value="0">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_trial" value="1" 
                               {{ old('is_trial', $subscription->is_trial) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-clock mr-1"></i>Пробный период
                        </span>
                    </label>
                </div>

                <div>
                    <label for="trial_days" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-day mr-2"></i>Дней пробного периода
                    </label>
                    <input type="number" name="trial_days" id="trial_days" 
                           value="{{ old('trial_days', $subscription->trial_days) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('trial_days') border-red-500 @enderror"
                           min="1">
                    @error('trial_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Заметки -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2"></i>Заметки (необязательно)
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                          placeholder="Дополнительная информация...">{{ old('notes', $subscription->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.trainer-subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
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


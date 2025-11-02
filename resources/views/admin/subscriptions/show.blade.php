@extends('admin.layouts.app')

@section('title', 'Просмотр плана подписки')
@section('page-title', 'Просмотр плана подписки')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Информация о плане подписки</h3>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit mr-1"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900 font-semibold text-lg">
                        {{ $subscription->name }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Валюта</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900 font-mono text-lg">
                        {{ $subscription->currency_code }}
                    </div>
                </div>
            </div>

            @if($subscription->description)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                    {{ $subscription->description }}
                </div>
            </div>
            @endif

            <!-- Финансовая информация -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Цена</label>
                <div class="bg-blue-50 rounded-lg px-3 py-2 text-blue-900 font-mono text-xl font-bold">
                    {{ number_format($subscription->price, 2) }} {{ $subscription->currency->symbol ?? $subscription->currency_code }}
                </div>
            </div>

            <!-- Статус -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                <div class="flex items-center">
                    @if($subscription->is_active)
                        <span class="status-active">Активен</span>
                    @else
                        <span class="status-inactive">Неактивен</span>
                    @endif
                </div>
            </div>

            <!-- Даты -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Создан</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Обновлен</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->updated_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Действия -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Назад к списку
                </a>
                
                <div class="flex items-center space-x-3">
                    <button onclick="deleteSubscription({{ $subscription->id }}, '{{ $subscription->name }}')" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Подтверждение удаления</h3>
                </div>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-500">
                    Вы действительно хотите удалить план <span id="subscriptionName" class="font-medium"></span>?
                    Это действие нельзя отменить.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function deleteSubscription(subscriptionId, subscriptionName) {
        document.getElementById('subscriptionName').textContent = subscriptionName;
        document.getElementById('deleteForm').action = `/subscriptions/${subscriptionId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection

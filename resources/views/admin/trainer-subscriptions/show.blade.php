@extends('admin.layouts.app')

@section('title', 'Просмотр подписки тренера')
@section('page-title', 'Просмотр подписки')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Информация о подписке</h3>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.trainer-subscriptions.edit', $subscription) }}" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit mr-1"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.trainer-subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Тренер -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Тренер</label>
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <div class="text-lg font-semibold text-gray-900">{{ $subscription->trainer->name }}</div>
                    <div class="text-sm text-gray-600">{{ $subscription->trainer->email }}</div>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">План подписки</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->plan->name ?? 'Не указан' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Валюта</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900 font-mono text-lg">
                        {{ $subscription->currency_code }}
                    </div>
                </div>
            </div>

            <!-- Финансовая информация -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Цена</label>
                <div class="bg-blue-50 rounded-lg px-3 py-2 text-blue-900 font-mono text-xl font-bold">
                    {{ number_format($subscription->price, 2) }} {{ $subscription->currency->symbol ?? $subscription->currency_code }}
                </div>
            </div>

            <!-- Даты и период -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Дата начала</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->start_date->format('d.m.Y') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Дата окончания</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->expires_date->format('d.m.Y') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Период подписки</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->start_date->diffInDays($subscription->expires_date) }} дней
                    </div>
                </div>
            </div>

            <!-- Статус и пробный период -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                    <div class="flex items-center">
                        @if($subscription->status === 'active')
                            <span class="status-active">Активная</span>
                        @elseif($subscription->status === 'trial')
                            <span class="status-pending">Пробный период</span>
                        @else
                            <span class="status-inactive">Истекшая</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пробный период</label>
                    <div class="flex items-center">
                        @if($subscription->is_trial)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i>Да ({{ $subscription->trial_days }} дней)
                            </span>
                        @else
                            <span class="text-gray-500">Нет</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($subscription->notes)
            <!-- Заметки -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Заметки</label>
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                    {{ $subscription->notes }}
                </div>
            </div>
            @endif

            <!-- Даты создания и обновления -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Создана</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Обновлена</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $subscription->updated_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Информация об истечении -->
            @if($subscription->expires_date > now())
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-green-900">Подписка активна</div>
                            <div class="text-sm text-green-700">Осталось: {{ $subscription->expires_date->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                        <div>
                            <div class="text-sm font-medium text-red-900">Подписка истекла</div>
                            <div class="text-sm text-red-700">Истекла: {{ $subscription->expires_date->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Действия -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.trainer-subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Назад к списку
                </a>
                
                <div class="flex items-center space-x-3">
                    <button onclick="deleteSubscription({{ $subscription->id }}, '{{ $subscription->trainer->name }}')" 
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
                    Вы действительно хотите удалить подписку для тренера <span id="trainerName" class="font-medium"></span>?
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
    function deleteSubscription(subscriptionId, trainerName) {
        document.getElementById('trainerName').textContent = trainerName;
        document.getElementById('deleteForm').action = `/trainer-subscriptions/${subscriptionId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection


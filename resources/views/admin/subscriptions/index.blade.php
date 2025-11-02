@extends('admin.layouts.app')

@section('title', 'Планы подписок')
@section('page-title', 'Управление планами подписок')

@section('content')
<div class="space-y-6">
    <!-- Заголовок и кнопка создания -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Планы подписок</h2>
            <p class="text-gray-600 mt-1">Управление планами подписок для тренеров</p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Добавить план
        </a>
    </div>

    <!-- Таблица подписок -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Название
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Цена
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->name }}</div>
                                    @if($subscription->description)
                                        <div class="text-sm text-gray-500">{{ Str::limit($subscription->description, 50) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($subscription->price, 2) }} {{ $subscription->currency->symbol ?? $subscription->currency_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->is_active)
                                    <span class="status-active">Активна</span>
                                @else
                                    <span class="status-inactive">Неактивна</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="toggleStatus({{ $subscription->id }})" 
                                            class="text-{{ $subscription->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $subscription->is_active ? 'yellow' : 'green' }}-900" 
                                            title="{{ $subscription->is_active ? 'Деактивировать' : 'Активировать' }}">
                                        <i class="fas fa-{{ $subscription->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                    <button onclick="deleteSubscription({{ $subscription->id }}, '{{ $subscription->name }}')" 
                                            class="text-red-600 hover:text-red-900" title="Удалить">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <i class="fas fa-credit-card text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Планы подписок не найдены</p>
                                <a href="{{ route('admin.subscriptions.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>Создать план
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
    function toggleStatus(subscriptionId) {
        fetch(`/subscriptions/${subscriptionId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка');
        });
    }

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

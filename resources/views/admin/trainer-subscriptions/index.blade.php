@extends('admin.layouts.app')

@section('title', 'Подписки тренеров')
@section('page-title', 'Управление подписками тренеров')

@section('content')
<div class="space-y-6">
    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Всего подписок</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <i class="fas fa-credit-card text-blue-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-green-50 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Активных</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['active'] }}</p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600">Пробный период</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $stats['trial'] }}</p>
                </div>
                <i class="fas fa-clock text-yellow-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-orange-50 rounded-xl p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600">Истекают скоро</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2">{{ $stats['expiring_soon'] }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-orange-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-xl p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600">Истекших</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['expired'] }}</p>
                </div>
                <i class="fas fa-times-circle text-red-400 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.trainer-subscriptions.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Поиск -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Поиск</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" 
                           value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Имя тренера или email">
                </div>
            </div>

            <!-- Фильтр по статусу -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Пробный период</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Истекшие</option>
                    <option value="expiring_soon" {{ request('status') == 'expiring_soon' ? 'selected' : '' }}>Истекают скоро</option>
                </select>
            </div>

            <!-- Кнопки -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-search mr-2"></i>Поиск
                </button>
                <a href="{{ route('admin.trainer-subscriptions.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Заголовок и кнопка создания -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Подписки тренеров</h2>
            <p class="text-gray-600 mt-1">Всего подписок: {{ $subscriptions->total() }}</p>
        </div>
        <a href="{{ route('admin.trainer-subscriptions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Добавить подписку
        </a>
    </div>

    <!-- Таблица подписок -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Тренер
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            План
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Цена
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата окончания
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
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription->trainer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subscription->trainer->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $subscription->plan->name ?? 'Не указан' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ number_format($subscription->price, 2) }} {{ $subscription->currency->symbol ?? $subscription->currency_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subscription->status === 'active')
                                    <span class="status-active">Активная</span>
                                @elseif($subscription->status === 'trial')
                                    <span class="status-pending">Пробный период</span>
                                @else
                                    <span class="status-inactive">Истекшая</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <span class="text-sm text-gray-900">{{ $subscription->expires_date->format('d.m.Y') }}</span>
                                    <div class="text-xs text-gray-500">
                                        @if($subscription->expires_date > now())
                                            Осталось: {{ $subscription->expires_date->diffForHumans() }}
                                        @else
                                            Истекла {{ $subscription->expires_date->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.trainer-subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.trainer-subscriptions.edit', $subscription) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteSubscription({{ $subscription->id }}, '{{ $subscription->trainer->name }}')" 
                                            class="text-red-600 hover:text-red-900" title="Удалить">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-user-tie text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Подписки не найдены</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        @if($subscriptions->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $subscriptions->links() }}
            </div>
        @endif
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


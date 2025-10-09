@extends('admin.layouts.app')

@section('title', 'Валюты')
@section('page-title', 'Управление валютами')

@section('content')
<div class="space-y-6">
    <!-- Заголовок и кнопка создания -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Валюты</h2>
            <p class="text-gray-600 mt-1">Управление валютами системы</p>
        </div>
        <a href="{{ route('admin.currencies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Добавить валюту
        </a>
    </div>

    <!-- Таблица валют -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Валюта
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Код
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Курс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Пользователи
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($currencies as $currency)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="text-lg font-medium">{{ $currency->symbol }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $currency->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $currency->symbol_position === 'before' ? $currency->symbol . '100' : '100' . $currency->symbol }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $currency->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($currency->exchange_rate, 4) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($currency->is_default)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>По умолчанию
                                        </span>
                                    @endif
                                    @if($currency->is_active)
                                        <span class="status-active">Активна</span>
                                    @else
                                        <span class="status-inactive">Неактивна</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $currency->users->count() }} пользователей
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.currencies.show', $currency) }}" class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.currencies.edit', $currency) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(!$currency->is_default)
                                        <button onclick="setDefault({{ $currency->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900" 
                                                title="Установить по умолчанию">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        
                                        <button onclick="toggleStatus({{ $currency->id }}, {{ $currency->is_active ? 'false' : 'true' }})" 
                                                class="text-{{ $currency->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $currency->is_active ? 'yellow' : 'green' }}-900" 
                                                title="{{ $currency->is_active ? 'Деактивировать' : 'Активировать' }}">
                                            <i class="fas fa-{{ $currency->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button onclick="deleteCurrency({{ $currency->id }}, '{{ $currency->name }}')" 
                                                class="text-red-600 hover:text-red-900" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-dollar-sign text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Валюты не найдены</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        @if($currencies->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $currencies->links() }}
            </div>
        @endif
    </div>

    <!-- Обновление курсов -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-exchange-alt mr-2"></i>Обновление курсов валют
        </h3>
        <form method="POST" action="{{ route('admin.currencies.update-rates') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($currencies as $currency)
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium text-gray-700 w-16">{{ $currency->code }}</label>
                        <input type="number" 
                               name="rates[{{ $currency->id }}]" 
                               value="{{ $currency->exchange_rate }}"
                               step="0.0001"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                @endforeach
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i>Обновить курсы
                </button>
            </div>
        </form>
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
                    Вы действительно хотите удалить валюту <span id="currencyName" class="font-medium"></span>?
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
    function setDefault(currencyId) {
        if (confirm('Установить эту валюту по умолчанию?')) {
            fetch(`/admin/currencies/${currencyId}/set-default`, {
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
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка');
            });
        }
    }

    function toggleStatus(currencyId, newStatus) {
        fetch(`/admin/currencies/${currencyId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_active: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка');
        });
    }

    function deleteCurrency(currencyId, currencyName) {
        document.getElementById('currencyName').textContent = currencyName;
        document.getElementById('deleteForm').action = `/admin/currencies/${currencyId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection


















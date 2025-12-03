@extends('admin.layouts.app')

@section('title', 'Языки')
@section('page-title', 'Управление языками')

@section('content')
<div class="space-y-6">
    <!-- Заголовок и кнопка создания -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Языки</h2>
            <p class="text-gray-600 mt-1">Управление языками системы</p>
        </div>
        <a href="{{ route('admin.languages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Добавить язык
        </a>
    </div>

    <!-- Таблица языков -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Язык
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Код
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
                    @forelse($languages as $language)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="text-2xl">{{ $language->flag }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $language->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $language->native_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ strtoupper($language->code) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($language->is_default)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-star mr-1"></i>По умолчанию
                                        </span>
                                    @endif
                                    @if($language->is_active)
                                        <span class="status-active">Активен</span>
                                    @else
                                        <span class="status-inactive">Неактивен</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $language->users->count() }} пользователей
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.languages.show', $language) }}" class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.languages.edit', $language) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(!$language->is_default)
                                        <button onclick="setDefault({{ $language->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900" 
                                                title="Установить по умолчанию">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        
                                        <button onclick="toggleStatus({{ $language->id }}, '{{ route('admin.languages.toggle-status', $language) }}')" 
                                                class="text-{{ $language->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $language->is_active ? 'yellow' : 'green' }}-900" 
                                                title="{{ $language->is_active ? 'Деактивировать' : 'Активировать' }}">
                                            <i class="fas fa-{{ $language->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button onclick="deleteLanguage({{ $language->id }}, '{{ $language->name }}')" 
                                                class="text-red-600 hover:text-red-900" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-language text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Языки не найдены</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        @if($languages->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $languages->links() }}
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
                    Вы действительно хотите удалить язык <span id="languageName" class="font-medium"></span>?
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
    function setDefault(languageId) {
        if (confirm('Установить этот язык по умолчанию?')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF токен не найден');
                return;
            }

            fetch(`/admin/languages/${languageId}/set-default`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Язык успешно установлен по умолчанию');
                    location.reload();
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка: ' + error.message);
            });
        }
    }

    function toggleStatus(languageId, url) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('CSRF токен не найден');
            return;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!response.ok) {
                // Если ответ не JSON, читаем как текст для отладки
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Ошибка сервера');
                    });
                } else {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
            }
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                throw new Error('Ожидался JSON ответ, но получен ' + contentType);
            }
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.error || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка: ' + error.message);
        });
    }

    function deleteLanguage(languageId, languageName) {
        document.getElementById('languageName').textContent = languageName;
        document.getElementById('deleteForm').action = `/admin/languages/${languageId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection









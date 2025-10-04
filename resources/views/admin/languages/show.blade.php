@extends('admin.layouts.app')

@section('title', 'Просмотр языка')
@section('page-title', 'Просмотр языка')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Информация о языке</h3>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.languages.edit', $language) }}" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-edit mr-1"></i>Редактировать
                    </a>
                    <a href="{{ route('admin.languages.index') }}" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Флаг</label>
                    <div class="text-4xl">{{ $language->flag }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Код языка</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900 font-mono">
                        {{ strtoupper($language->code) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $language->name }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Родное название</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $language->native_name }}
                    </div>
                </div>
            </div>

            <!-- Статус -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                    <div class="flex items-center">
                        @if($language->is_active)
                            <span class="status-active">Активен</span>
                        @else
                            <span class="status-inactive">Неактивен</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">По умолчанию</label>
                    <div class="flex items-center">
                        @if($language->is_default)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-star mr-1"></i>Да
                            </span>
                        @else
                            <span class="text-gray-500">Нет</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Порядок сортировки</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $language->sort_order }}
                    </div>
                </div>
            </div>

            <!-- Пользователи -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Пользователи</label>
                <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                    {{ $language->users->count() }} пользователей используют этот язык
                </div>
            </div>

            <!-- Даты -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Создан</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $language->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Обновлен</label>
                    <div class="bg-gray-50 rounded-lg px-3 py-2 text-gray-900">
                        {{ $language->updated_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Действия -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.languages.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Назад к списку
                </a>
                
                <div class="flex items-center space-x-3">
                    @if(!$language->is_default)
                        <button onclick="setDefault({{ $language->id }})" 
                                class="px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200">
                            <i class="fas fa-star mr-2"></i>Установить по умолчанию
                        </button>
                        
                        <button onclick="deleteLanguage({{ $language->id }}, '{{ $language->name }}')" 
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Удалить
                        </button>
                    @endif
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
            fetch(`/admin/languages/${languageId}/set-default`, {
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












@extends('admin.layouts.app')

@section('title', 'Пользователи')
@section('page-title', 'Управление пользователями')

@php
    $roleLabels = [
        'admin' => __('common.role_admin'),
        'trainer' => __('common.role_trainer'),
        'athlete' => __('common.role_athlete'),
        'self-athlete' => __('common.role_self_athlete'),
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                           placeholder="Имя или email">
                </div>
            </div>

            <!-- Фильтр по роли -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Роль</label>
                <select name="role" id="role" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Все роли</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $roleLabels[$role->name] ?? ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Фильтр по статусу -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                <select name="status" id="status" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивные</option>
                </select>
            </div>

            <!-- Кнопки -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-search mr-2"></i>Поиск
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Заголовок и кнопка создания -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Пользователи</h2>
            <p class="text-gray-600 mt-1">Всего пользователей: {{ $users->total() }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <i class="fas fa-plus mr-2"></i>Создать пользователя
        </a>
    </div>

    <!-- Таблица пользователей -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Пользователь
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Роль
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата регистрации
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('common.last_activity') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($role->name === 'admin') bg-purple-100 text-purple-800
                                        @elseif($role->name === 'trainer') bg-blue-100 text-blue-800
                                        @elseif($role->name === 'athlete') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $roleLabels[$role->name] ?? ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="status-active">Активен</span>
                                @else
                                    <span class="status-inactive">Неактивен</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_activity_at ? $user->last_activity_at->format('d.m.Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Просмотр">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900" title="Редактировать">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <button onclick="toggleStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})" 
                                                class="text-{{ $user->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $user->is_active ? 'yellow' : 'green' }}-900" 
                                                title="{{ $user->is_active ? 'Деактивировать' : 'Активировать' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
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
                                <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Пользователи не найдены</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        @if($users->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $users->links() }}
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
                    Вы действительно хотите удалить пользователя <span id="userName" class="font-medium"></span>?
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
    function toggleStatus(userId, newStatus) {
        fetch(`/admin/users/${userId}/toggle-status`, {
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

    function deleteUser(userId, userName) {
        document.getElementById('userName').textContent = userName;
        document.getElementById('deleteForm').action = `/admin/users/${userId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection


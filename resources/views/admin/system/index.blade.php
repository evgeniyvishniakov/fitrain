@extends('admin.layouts.app')

@section('title', 'Система')
@section('page-title', 'Системные функции')

@section('content')
<div class="space-y-6">
    <!-- Системные действия -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Кэш -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-broom mr-2"></i>Управление кэшем
            </h3>
            <div class="space-y-3">
                <form method="POST" action="{{ route('admin.system.clear-cache') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>Очистить кэш
                    </button>
                </form>
                
                <form method="POST" action="{{ route('admin.system.optimize') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-rocket mr-2"></i>Оптимизировать систему
                    </button>
                </form>
            </div>
        </div>

        <!-- Логи и резервные копии -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-tools mr-2"></i>Обслуживание
            </h3>
            <div class="space-y-3">
                <a href="{{ route('admin.system.logs') }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-file-alt mr-2"></i>Просмотр логов
                    @if($logSize > 0)
                        <span class="ml-2 text-xs bg-blue-500 px-2 py-1 rounded-full">
                            {{ round($logSize / 1024 / 1024, 2) }} MB
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Резервное копирование -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-database mr-2"></i>Резервное копирование
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <form method="POST" action="{{ route('admin.system.backup-db') }}" class="inline">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-database mr-2"></i>Резервная копия БД
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.system.backup-files') }}" class="inline">
                @csrf
                <input type="hidden" name="include_images" value="0">
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-folder mr-2"></i>Резервная копия файлов (без картинок)
                </button>
            </form>
            
            <form method="POST" action="{{ route('admin.system.backup-files') }}" class="inline">
                @csrf
                <input type="hidden" name="include_images" value="1">
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                    <i class="fas fa-images mr-2"></i>Резервная копия с картинками
                </button>
            </form>
        </div>

        <!-- Список резервных копий -->
        @if(count($backups) > 0)
            <div class="mt-6">
                <h4 class="text-md font-semibold text-gray-900 mb-3">
                    <i class="fas fa-list mr-2"></i>Последние резервные копии
                </h4>
                <div class="space-y-2">
                    @foreach($backups as $backup)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="flex-shrink-0">
                                    @if($backup['type'] === 'db')
                                        <i class="fas fa-database text-purple-600"></i>
                                    @else
                                        <i class="fas fa-folder text-indigo-600"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $backup['name'] }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $backup['size_formatted'] }} • {{ $backup['created_at'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="ml-3 flex items-center space-x-2 flex-shrink-0">
                                <a href="{{ route('admin.system.backup.download', ['filename' => $backup['name']]) }}" 
                                   class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-download mr-1"></i>Скачать
                                </a>
                                <form method="POST" action="{{ route('admin.system.backup.delete', ['filename' => $backup['name']]) }}" 
                                      class="inline"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить резервную копию {{ $backup['name'] }}? Это действие нельзя отменить.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-6 text-center py-4 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>Резервные копии отсутствуют</p>
            </div>
        @endif
    </div>

    <!-- Предупреждения -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Важно!
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Очистка кэша может временно замедлить работу сайта</li>
                        <li>Оптимизация создает новые кэш-файлы для ускорения работы</li>
                        <li>Резервные копии сохраняются в папку storage/backups</li>
                        <li>Регулярно проверяйте логи на наличие ошибок</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






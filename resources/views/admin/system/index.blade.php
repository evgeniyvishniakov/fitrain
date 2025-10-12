@extends('admin.layouts.app')

@section('title', 'Система')
@section('page-title', 'Системные функции')

@section('content')
<div class="space-y-6">
    <!-- Информация о системе -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-info-circle mr-2"></i>Информация о системе
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">PHP версия</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Laravel версия</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Сервер</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['server'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Лимит памяти</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['memory_limit'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Максимальное время выполнения</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['max_execution_time'] }} сек</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Максимальный размер загрузки</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['upload_max_filesize'] }}</p>
            </div>
        </div>
    </div>

    <!-- Дисковое пространство -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-hdd mr-2"></i>Дисковое пространство
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Всего места</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['disk_space']['total'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Использовано</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['disk_space']['used'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Свободно</label>
                <p class="mt-1 text-sm text-gray-900">{{ $systemInfo['disk_space']['free'] }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Заполнено</label>
                <div class="mt-1">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $systemInfo['disk_space']['percentage'] }}%"></div>
                    </div>
                    <p class="text-sm text-gray-900 mt-1">{{ $systemInfo['disk_space']['percentage'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Статус кэша -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">
            <i class="fas fa-memory mr-2"></i>Статус кэша
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-700">Конфигурация</span>
                @if($cacheStatus['config'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i>Кэширована
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1"></i>Не кэширована
                    </span>
                @endif
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-700">Маршруты</span>
                @if($cacheStatus['routes'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i>Кэшированы
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1"></i>Не кэшированы
                    </span>
                @endif
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-700">Сервисы</span>
                @if($cacheStatus['services'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i>Кэшированы
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1"></i>Не кэшированы
                    </span>
                @endif
            </div>
            
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-sm text-gray-700">Пакеты</span>
                @if($cacheStatus['packages'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check mr-1"></i>Кэшированы
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i class="fas fa-times mr-1"></i>Не кэшированы
                    </span>
                @endif
            </div>
        </div>
    </div>

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
                
                <form method="POST" action="{{ route('admin.system.backup') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-database mr-2"></i>Создать резервную копию
                    </button>
                </form>
            </div>
        </div>
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



















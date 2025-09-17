@extends("crm.layouts.app")

@section("title", "Упражнение")
@section("page-title", "Упражнение")

@section("content")
<div class="space-y-6 fade-in-up">
    
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exercise->name }}</h1>
            <p class="text-gray-600 mt-1">{{ $exercise->description }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('crm.exercises.edit', $exercise->id) }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Редактировать
            </a>
            <a href="{{ route('crm.exercises.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Назад к списку
            </a>
        </div>
    </div>

    <!-- Информация об упражнении -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Основная информация -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Основная информация</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Категория</label>
                    <p class="mt-1 text-gray-900">{{ $exercise->category_label }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Оборудование</label>
                    <p class="mt-1 text-gray-900">{{ $exercise->equipment_label }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Сложность</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($exercise->difficulty === 'beginner') bg-green-100 text-green-800
                        @elseif($exercise->difficulty === 'intermediate') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $exercise->difficulty_label }}
                    </span>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Группы мышц</label>
                    <div class="mt-1 flex flex-wrap gap-2">
                        @if($exercise->muscle_groups)
                            @foreach($exercise->muscle_groups as $muscle)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $muscle }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-gray-500">Не указано</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Инструкции -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Инструкции по выполнению</h3>
            
            @if($exercise->instructions)
                <div class="prose max-w-none">
                    {!! nl2br(e($exercise->instructions)) !!}
                </div>
            @else
                <p class="text-gray-500">Инструкции не добавлены</p>
            @endif
        </div>
    </div>

    <!-- Конфигурация полей -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Настраиваемые поля</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($exercise->getAvailableFieldsWithLabels() as $field => $label)
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                </div>
            @endforeach
        </div>
        
        @if(empty($exercise->getAvailableFieldsWithLabels()))
            <p class="text-gray-500">Используются поля по умолчанию: подходы, повторения, вес, отдых</p>
        @endif
    </div>

    <!-- Действия -->
    <div class="flex items-center justify-end gap-4">
        <a href="{{ route('crm.exercises.index') }}" class="btn-secondary">
            Назад к списку
        </a>
        <a href="{{ route('crm.exercises.edit', $exercise->id) }}" class="btn">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Редактировать
        </a>
    </div>
</div>
@endsection

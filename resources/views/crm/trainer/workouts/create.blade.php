@extends("crm.layouts.app")

@section("title", "Создать тренировку")
@section("page-title", "Создать тренировку")

@section("content")
<div class="space-y-6 fade-in-up">
    
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Создать тренировку</h1>
            <p class="text-gray-600 mt-1">Добавьте новую тренировку для вашего спортсмена</p>
        </div>
        <a href="{{ route('crm.workouts.index') }}" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Назад к списку
        </a>
    </div>

    <!-- Форма создания -->
    <div class="card p-6">
        <form action="{{ route('crm.workouts.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Название тренировки *
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="input @error('title') border-red-500 @enderror" 
                           placeholder="Например: Силовая тренировка груди"
                           required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="athlete_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Спортсмен *
                    </label>
                    <select id="athlete_id" 
                            name="athlete_id" 
                            class="input @error('athlete_id') border-red-500 @enderror" 
                            required>
                        <option value="">Выберите спортсмена</option>
                        @foreach($athletes as $athlete)
                            <option value="{{ $athlete->id }}" {{ old('athlete_id') == $athlete->id ? 'selected' : '' }}>
                                {{ $athlete->name }} ({{ $athlete->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('athlete_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Дата и время -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Дата тренировки *
                    </label>
                    <input type="date" 
                           id="date" 
                           name="date" 
                           value="{{ old('date', date('Y-m-d')) }}"
                           class="input @error('date') border-red-500 @enderror" 
                           required>
                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Продолжительность (минуты)
                    </label>
                    <input type="number" 
                           id="duration" 
                           name="duration" 
                           value="{{ old('duration') }}"
                           class="input @error('duration') border-red-500 @enderror" 
                           placeholder="60"
                           min="1" max="300">
                    @error('duration')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Описание -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Описание тренировки
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="input @error('description') border-red-500 @enderror" 
                          placeholder="Опишите план тренировки, упражнения, количество подходов...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Статус -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Статус тренировки
                </label>
                <select id="status" 
                        name="status" 
                        class="input @error('status') border-red-500 @enderror">
                    <option value="planned" {{ old('status', 'planned') == 'planned' ? 'selected' : '' }}>Запланирована</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Завершена</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Отменена</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Кнопки -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t">
                <a href="{{ route('crm.workouts.index') }}" class="btn-secondary">
                    Отмена
                </a>
                <button type="submit" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Создать тренировку
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



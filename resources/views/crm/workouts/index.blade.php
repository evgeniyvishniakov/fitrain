@extends("crm.layouts.app")

@section("title", "Тренировки")
@section("page-title", "Тренировки")

<script>
// SPA функциональность для тренировок
function workoutApp() {
    return {
        currentView: 'list', // list, create, edit, view
        workouts: @json($workouts->items()),
        currentWorkout: null,
        search: '',
        status: '',
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentWorkout = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentWorkout = {
                title: '',
                description: '',
                athlete_id: '',
                date: new Date().toISOString().split('T')[0],
                duration: 60,
                status: 'planned'
            };
        },
        
        showEdit(workout) {
            this.currentView = 'edit';
            this.currentWorkout = { ...workout };
        },
        
        showView(workout) {
            this.currentView = 'view';
            this.currentWorkout = workout;
        },
        
        // Фильтрация
        get filteredWorkouts() {
            let filtered = this.workouts;
            
            if (this.search) {
                filtered = filtered.filter(w => 
                    w.title.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            if (this.status) {
                filtered = filtered.filter(w => w.status === this.status);
            }
            
            return filtered;
        },
        
        // Сохранение
        async saveWorkout() {
            try {
                const url = this.currentWorkout.id ? 
                    `/workouts/${this.currentWorkout.id}` : '/workouts';
                const method = this.currentWorkout.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.currentWorkout)
                });
                
                if (response.ok) {
                    location.reload(); // Просто перезагружаем страницу
                }
            } catch (error) {
                console.error('Ошибка:', error);
            }
        },
        
        // Удаление
        async deleteWorkout(id) {
            if (confirm('Удалить тренировку?')) {
                try {
                    const response = await fetch(`/workouts/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        location.reload();
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                }
            }
        }
    }
}
</script>

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.progress.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.progress.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("header-actions")
    @if(auth()->user()->hasRole('trainer'))
        <button @click="showCreate()" class="btn">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Создать тренировку
        </button>
    @endif
@endsection

@section("content")
<div x-data="workoutApp()" class="space-y-6 fade-in-up">
    
    <!-- Фильтры и поиск -->
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       x-model="search" 
                       placeholder="Поиск тренировок..." 
                       class="input">
            </div>
            <div class="flex gap-2">
                <select x-model="status" class="input">
                    <option value="">Все статусы</option>
                    <option value="planned">Запланирована</option>
                    <option value="completed">Завершена</option>
                    <option value="cancelled">Отменена</option>
                </select>
            </div>
        </div>
    </div>

    <!-- СПИСОК ТРЕНИРОВОК -->
    <div x-show="currentView === 'list'" class="space-y-4">
        <template x-for="workout in filteredWorkouts" :key="workout.id">
            <div class="card p-6 hover:shadow-lg transition-shadow">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1" x-text="workout.title"></h3>
                                <p class="text-gray-600 mb-2" x-text="workout.description || ''"></p>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span x-text="new Date(workout.date).toLocaleDateString('ru-RU')"></span>
                                    </div>
                                    <div class="flex items-center gap-1" x-show="workout.duration">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span x-text="workout.duration + ' мин'"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span x-text="workout.athlete?.name || workout.trainer?.name || 'Неизвестно'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <!-- Статус -->
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                              :class="{
                                  'bg-green-100 text-green-800': workout.status === 'completed',
                                  'bg-red-100 text-red-800': workout.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': workout.status === 'planned'
                              }"
                              x-text="{
                                  'completed': 'Завершена',
                                  'cancelled': 'Отменена',
                                  'planned': 'Запланирована'
                              }[workout.status]">
                        </span>
                        
                        <!-- Действия -->
                        <div class="flex gap-2">
                            <button @click="showView(workout)" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            
                            @if(auth()->user()->hasRole('trainer'))
                                <button @click="showEdit(workout)" class="btn-secondary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                
                                <button @click="deleteWorkout(workout.id)" class="btn-danger">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Пустое состояние -->
        <div x-show="filteredWorkouts.length === 0" class="card p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Нет тренировок</h3>
            <p class="text-gray-600 mb-6">У вас пока нет запланированных тренировок</p>
            @if(auth()->user()->hasRole('trainer'))
                <button @click="showCreate()" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Создать первую тренировку
                </button>
            @endif
        </div>
    </div>

    <!-- СОЗДАНИЕ/РЕДАКТИРОВАНИЕ ТРЕНИРОВКИ -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
                <span x-text="currentWorkout?.id ? 'Редактировать тренировку' : 'Создать тренировку'"></span>
            </h3>
            <button @click="showList()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Назад
            </button>
        </div>
        
        <form @submit.prevent="saveWorkout()" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Основная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Название тренировки *
                        </label>
                        <input type="text" 
                               x-model="currentWorkout.title"
                               class="input"
                               placeholder="Например: Силовая тренировка"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Спортсмен *
                        </label>
                        <select x-model="currentWorkout.athlete_id" class="input" required>
                            <option value="">Выберите спортсмена</option>
                            @foreach($athletes ?? [] as $athlete)
                                <option value="{{ $athlete->id }}" x-text="{{ $athlete->name }}"></option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Дата тренировки *
                        </label>
                        <input type="date" 
                               x-model="currentWorkout.date"
                               class="input"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Продолжительность (минуты)
                        </label>
                        <input type="number" 
                               x-model="currentWorkout.duration"
                               class="input"
                               placeholder="60"
                               min="1">
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Описание тренировки
                        </label>
                        <textarea x-model="currentWorkout.description"
                                  rows="6"
                                  class="input"
                                  placeholder="Опишите план тренировки, упражнения, цели..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Статус
                        </label>
                        <select x-model="currentWorkout.status" class="input">
                            <option value="planned">Запланирована</option>
                            <option value="completed">Завершена</option>
                            <option value="cancelled">Отменена</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <button type="button" @click="showList()" class="btn-secondary">
                    Отмена
                </button>
                <button type="submit" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="currentWorkout?.id ? 'Обновить' : 'Создать'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- ПРОСМОТР ТРЕНИРОВКИ -->
    <div x-show="currentView === 'view'" x-transition class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Просмотр тренировки</h3>
            <button @click="showList()" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Назад
            </button>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4" x-text="currentWorkout?.title"></h4>
                    <p class="text-gray-600" x-text="currentWorkout?.description || 'Описание отсутствует'"></p>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Дата:</span>
                        <span class="font-medium" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></span>
                    </div>
                    <div class="flex justify-between" x-show="currentWorkout?.duration">
                        <span class="text-gray-600">Продолжительность:</span>
                        <span class="font-medium" x-text="currentWorkout?.duration + ' мин'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Статус:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-medium"
                              :class="{
                                  'bg-green-100 text-green-800': currentWorkout?.status === 'completed',
                                  'bg-red-100 text-red-800': currentWorkout?.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': currentWorkout?.status === 'planned'
                              }"
                              x-text="{
                                  'completed': 'Завершена',
                                  'cancelled': 'Отменена',
                                  'planned': 'Запланирована'
                              }[currentWorkout?.status]">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

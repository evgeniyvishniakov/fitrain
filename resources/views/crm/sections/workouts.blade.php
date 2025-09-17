<!-- СЕКЦИЯ ТРЕНИРОВОК -->
<div class="space-y-6">
    
    <!-- Заголовок и действия -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Тренировки</h2>
            <p class="text-gray-600 mt-1">Управление тренировочными программами</p>
        </div>
        
        @if(auth()->user()->hasRole('trainer'))
            <button @click="createWorkout()" class="btn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Создать тренировку
            </button>
        @endif
    </div>

    <!-- Фильтры и поиск -->
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       x-model="workoutSearch" 
                       placeholder="Поиск тренировок..." 
                       class="input">
            </div>
            <div class="flex gap-2">
                <select x-model="workoutStatus" class="input">
                    <option value="">Все статусы</option>
                    <option value="planned">Запланирована</option>
                    <option value="completed">Завершена</option>
                    <option value="cancelled">Отменена</option>
                </select>
                <select x-model="workoutDate" class="input">
                    <option value="">Все даты</option>
                    <option value="today">Сегодня</option>
                    <option value="week">Эта неделя</option>
                    <option value="month">Этот месяц</option>
                </select>
            </div>
        </div>
    </div>

    <!-- СПИСОК ТРЕНИРОВОК -->
    <div x-show="currentPage === 'workouts'" class="space-y-4">
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
                            <button @click="viewWorkout(workout)" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            
                            @if(auth()->user()->hasRole('trainer'))
                                <button @click="editWorkout(workout)" class="btn-secondary">
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
                <button @click="createWorkout()" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Создать первую тренировку
                </button>
            @endif
        </div>
    </div>

    <!-- СОЗДАНИЕ/РЕДАКТИРОВАНИЕ ТРЕНИРОВКИ -->
    <div x-show="currentPage === 'workout-create' || currentPage === 'workout-edit'" x-transition>
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    <span x-text="currentWorkout?.id ? 'Редактировать тренировку' : 'Создать тренировку'"></span>
                </h3>
                <button @click="currentPage = 'workouts'; currentWorkout = null" class="btn-secondary">
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
                                <template x-for="athlete in athletes" :key="athlete.id">
                                    <option :value="athlete.id" x-text="athlete.name"></option>
                                </template>
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
                    <button type="button" @click="currentPage = 'workouts'; currentWorkout = null" class="btn-secondary">
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
    </div>
</div>

<script>
// Добавляем в основной crmApp()
Object.assign(crmApp(), {
    workoutSearch: '',
    workoutStatus: '',
    workoutDate: '',
    
    get filteredWorkouts() {
        let filtered = this.workouts;
        
        // Поиск по названию
        if (this.workoutSearch) {
            filtered = filtered.filter(workout => 
                workout.title.toLowerCase().includes(this.workoutSearch.toLowerCase())
            );
        }
        
        // Фильтр по статусу
        if (this.workoutStatus) {
            filtered = filtered.filter(workout => workout.status === this.workoutStatus);
        }
        
        // Фильтр по дате
        if (this.workoutDate) {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            
            filtered = filtered.filter(workout => {
                const workoutDate = new Date(workout.date);
                
                switch (this.workoutDate) {
                    case 'today':
                        return workoutDate.getTime() === today.getTime();
                    case 'week':
                        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        return workoutDate >= weekAgo && workoutDate <= today;
                    case 'month':
                        const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                        return workoutDate >= monthAgo && workoutDate <= today;
                    default:
                        return true;
                }
            });
        }
        
        return filtered;
    },
    
    viewWorkout(workout) {
        this.currentWorkout = workout;
        this.currentPage = 'workout-view';
    }
});
</script>


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
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="workout.title"></h3>
                        <p class="text-gray-600 mt-1" x-text="workout.description"></p>
                        
                        <div class="flex items-center gap-4 mt-3 text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="workout.date"></span>
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="workout.duration + ' {{ __('common.min') }}'"></span>
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span x-text="workout.athlete?.name || 'Не указан'"></span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                              :class="{
                                  'bg-blue-100 text-blue-800': workout.status === 'planned',
                                  'bg-green-100 text-green-800': workout.status === 'completed',
                                  'bg-red-100 text-red-800': workout.status === 'cancelled'
                              }"
                              x-text="{
                                  'planned': 'Запланирована',
                                  'completed': 'Завершена', 
                                  'cancelled': 'Отменена'
                              }[workout.status]"></span>
                        
                        <div class="flex gap-1">
                            <button @click="editWorkout(workout)" class="btn-secondary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="deleteWorkout(workout.id)" class="btn-danger btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Пустое состояние -->
        <div x-show="filteredWorkouts.length === 0" class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Нет тренировок</h3>
            <p class="text-gray-500 mb-4">Создайте первую тренировку для ваших спортсменов</p>
            @if(auth()->user()->hasRole('trainer'))
                <button @click="createWorkout()" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Создать тренировку
                </button>
            @endif
        </div>
    </div>
</div>

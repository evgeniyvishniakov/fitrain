@extends("crm.layouts.app")

@section("title", "Тренировки спортсмена")
@section("page-title", "Тренировки")

<script>
        // Функциональность для просмотра тренировок
        function athleteWorkoutApp() {
            return {
                currentView: 'list', // list, view
                workouts: @json($workouts->items()),
                currentWorkout: null,
                exerciseStatuses: {}, // Хранение статусов упражнений
                exerciseComments: {}, // Хранение комментариев к упражнениям
                exerciseSetsData: {}, // Хранение данных по подходам
                exerciseSetsExpanded: {}, // Хранение состояния развернутости полей подходов
                saveTimeout: null, // Таймер для автосохранения
                lastSaved: null, // Время последнего сохранения
                workoutProgress: {}, // Прогресс для каждой тренировки
                isLoading: true, // Флаг загрузки
                lastChangedExercise: null, // Последнее измененное упражнение

                // Инициализация
                init() {
                    this.loadAllWorkoutProgress();
                },

                // Навигация
                showList() {
                    this.currentView = 'list';
                    this.currentWorkout = null;
                },

                showView(workoutId) {
                    this.currentView = 'view';
                    this.currentWorkout = this.workouts.find(w => w.id === workoutId);
                    
                // Загружаем сохраненный прогресс при открытии тренировки
                this.loadExerciseProgress(workoutId);
            },

            // Загрузка прогресса для всех тренировок
            async loadAllWorkoutProgress() {
                try {
                    for (let workout of this.workouts) {
                        const response = await fetch(`/athlete/exercise-progress?workout_id=${workout.id}`, {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            const responseData = await response.json();
                            
                            // Проверяем формат ответа
                            const progressData = responseData.success ? responseData.progress : responseData;
                            
                            this.workoutProgress[workout.id] = {};
                            
                            if (Array.isArray(progressData)) {
                                progressData.forEach(progress => {
                                    this.workoutProgress[workout.id][progress.exercise_id] = {
                                        status: progress.status,
                                        athlete_comment: progress.athlete_comment,
                                        sets_data: progress.sets_data,
                                        completed_at: progress.completed_at
                                    };
                                });
                            } else if (progressData && typeof progressData === 'object') {
                                // Если данные в формате объекта, преобразуем в массив
                                Object.values(progressData).forEach(progress => {
                                    this.workoutProgress[workout.id][progress.exercise_id] = {
                                        status: progress.status,
                                        athlete_comment: progress.athlete_comment,
                                        sets_data: progress.sets_data,
                                        completed_at: progress.completed_at
                                    };
                                });
                            }
                        }
                    }
                } catch (error) {
                    console.error('Ошибка загрузки прогресса для всех тренировок:', error);
                } finally {
                    // Завершаем загрузку
                    this.isLoading = false;
                }
            },

            // Получение статуса упражнения для списка тренировок
            getExerciseStatusForList(workoutId, exerciseId) {
                return this.workoutProgress[workoutId]?.[exerciseId]?.status || null;
            },

                // Управление статусом упражнений
                setExerciseStatus(exerciseId, status) {
                    this.exerciseStatuses[exerciseId] = status;
                    this.lastChangedExercise = { id: exerciseId, status: status };
                    
                    if (status === 'partial') {
                        // Инициализируем данные по подходам для частичного выполнения
                        const exercise = this.currentWorkout?.exercises?.find(ex => (ex.exercise_id == exerciseId) || (ex.id == exerciseId));
                        if (exercise) {
                            const totalSets = exercise.sets || exercise.pivot?.sets || 3;
                            this.initSetsData(exerciseId, totalSets);
                            // Автоматически разворачиваем поля при выборе "Частично"
                            this.exerciseSetsExpanded[exerciseId] = true;
                        }
                    } else {
                        // Если статус не "частично", очищаем комментарий и данные по подходам
                        delete this.exerciseComments[exerciseId];
                        delete this.exerciseSetsData[exerciseId];
                        delete this.exerciseSetsExpanded[exerciseId];
                    }
                    
                    // Автосохранение через 2 секунды после изменения
                    this.autoSave();
                },

                getExerciseStatus(exerciseId) {
                    return this.exerciseStatuses[exerciseId] || null;
                },

                // Инициализация данных по подходам для упражнения
                initSetsData(exerciseId, totalSets) {
                    if (!this.exerciseSetsData[exerciseId]) {
                        this.exerciseSetsData[exerciseId] = [];
                        const exercise = this.currentWorkout?.exercises?.find(ex => (ex.exercise_id == exerciseId) || (ex.id == exerciseId));
                        const defaultRest = exercise?.rest || exercise?.pivot?.rest || 1.0; // По умолчанию 1 минута
                        
                        for (let i = 0; i < totalSets; i++) {
                            this.exerciseSetsData[exerciseId].push({
                                set_number: i + 1,
                                reps: '',
                                weight: '',
                                rest: defaultRest // Автоматически заполняем отдых в минутах
                            });
                        }
                    }
                },

                // Получение данных по подходам для упражнения
                getSetsData(exerciseId) {
                    return this.exerciseSetsData[exerciseId] || [];
                },

                // Обновление данных по подходу
                updateSetData(exerciseId, setIndex, field, value) {
                    if (!this.exerciseSetsData[exerciseId]) {
                        this.exerciseSetsData[exerciseId] = [];
                    }
                    if (!this.exerciseSetsData[exerciseId][setIndex]) {
                        this.exerciseSetsData[exerciseId][setIndex] = {};
                    }
                    this.exerciseSetsData[exerciseId][setIndex][field] = value;
                    
                    // Обновляем последнее измененное упражнение для правильного уведомления
                    this.lastChangedExercise = { id: exerciseId, status: 'partial' };
                    
                    this.autoSave();
                },

                // Управление сворачиванием/разворачиванием полей подходов
                toggleSetsExpanded(exerciseId) {
                    this.exerciseSetsExpanded[exerciseId] = !this.exerciseSetsExpanded[exerciseId];
                },

                // Проверка, развернуты ли поля подходов
                isSetsExpanded(exerciseId) {
                    return this.exerciseSetsExpanded[exerciseId] || false;
                },

                // Автосохранение прогресса
                autoSave() {
                    // Очищаем предыдущий таймер
                    if (this.saveTimeout) {
                        clearTimeout(this.saveTimeout);
                    }
                    
                    // Устанавливаем новый таймер на 2 секунды
                    this.saveTimeout = setTimeout(() => {
                        this.saveExerciseProgress();
                    }, 2000);
                },

                    // Обновление прогресса на сервере
                    async saveExerciseProgress() {
                    if (!this.currentWorkout) return;
                    
                    try {
                        // Собираем все упражнения с изменениями
                        const exercises = Object.keys(this.exerciseStatuses).map(exerciseId => ({
                            exercise_id: parseInt(exerciseId),
                            status: this.exerciseStatuses[exerciseId],
                            athlete_comment: this.exerciseComments[exerciseId] || null,
                            sets_data: this.exerciseSetsData[exerciseId] || null
                        }));

                        // Показываем индикатор загрузки
                        showInfo('Сохранение...', 'Сохраняем ваш прогресс...', 2000);

                        const response = await fetch('/athlete/exercise-progress', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                workout_id: this.currentWorkout.id,
                                exercises: exercises
                            })
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            // Записываем время последнего сохранения
                            this.lastSaved = new Date();
                            
                            // Показываем уведомление только для последнего измененного упражнения
                            let title = '';
                            let message = '';
                            
                            if (this.lastChangedExercise) {
                                const { status, id } = this.lastChangedExercise;
                                
                                if (status === 'completed') {
                                    title = 'Прогресс сохранен!';
                                    message = 'Упражнение отмечено как выполнено';
                                } else if (status === 'partial') {
                                    // Проверяем, есть ли данные по подходам
                                    const hasSetsData = this.exerciseSetsData[id] && 
                                                       this.exerciseSetsData[id].some(set => 
                                                           set.reps || set.weight || set.rest
                                                       );
                                    
                                    if (hasSetsData) {
                                        title = 'Прогресс сохранен!';
                                        message = 'Упражнение сохранено с детализацией';
                                    } else {
                                        title = 'Статус обновлен!';
                                        message = 'Упражнение отмечено как частично выполненное';
                                    }
                                } else if (status === 'not_done') {
                                    title = 'Статус обновлен!';
                                    message = 'Упражнение отмечено как не выполнено';
                                }
                                
                                // Сбрасываем последнее измененное упражнение
                                this.lastChangedExercise = null;
                            } else {
                                // Fallback для случая, если lastChangedExercise не установлен
                                title = 'Статус обновлен!';
                                message = `Обновлено ${exercises.length} упражнений`;
                            }
                            
                            showSuccess(title, message);
                        } else {
                            showError('Ошибка сохранения', result.message || 'Не удалось сохранить прогресс. Попробуйте еще раз.');
                        }
                    } catch (error) {
                        console.error('Ошибка обновления:', error);
                        showError('Ошибка соединения', 'Проверьте подключение к интернету и попробуйте еще раз.');
                    }
                },

                // Загрузка сохраненного прогресса
                async loadExerciseProgress(workoutId) {
                    try {
                        const response = await fetch(`/athlete/exercise-progress?workout_id=${workoutId}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();
                        
                        if (result.success && result.progress) {
                            // Загружаем сохраненные статусы, комментарии и данные по подходам
                            Object.keys(result.progress).forEach(exerciseId => {
                                const progress = result.progress[exerciseId];
                                this.exerciseStatuses[exerciseId] = progress.status;
                                if (progress.athlete_comment) {
                                    this.exerciseComments[exerciseId] = progress.athlete_comment;
                                }
                                if (progress.sets_data) {
                                    this.exerciseSetsData[exerciseId] = progress.sets_data;
                                }
                            });
                        }
                    } catch (error) {
                        console.error('Ошибка загрузки прогресса:', error);
                    }
                },


                // Вспомогательные методы
                getStatusLabel(status) {
                    const labels = {
                        'completed': 'Завершена',
                        'cancelled': 'Отменена',
                        'planned': 'Запланирована',
                        'in_progress': 'В процессе'
                    };
                    return labels[status] || 'Неизвестно';
                }
            }
        }
</script>

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("content")
<div x-data="athleteWorkoutApp()" x-init="init()" class="space-y-6 fade-in-up">

    <!-- Индикатор загрузки -->
    <div x-show="isLoading" x-cloak class="flex justify-center items-center py-12">
        <div class="flex items-center space-x-3">
            <div class="loading-spinner"></div>
            <span class="text-gray-600">Загрузка тренировок...</span>
        </div>
    </div>

    <!-- Статистика -->
    <div x-show="currentView === 'list' && !isLoading" x-cloak class="stats-container">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Всего тренировок</div>
                <div class="stat-value">{{ $workoutsCount ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Завершено</div>
                <div class="stat-value">{{ $completedCount ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">В процессе</div>
                <div class="stat-value">{{ $inProgressCount ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Запланировано</div>
                <div class="stat-value">{{ $plannedCount ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Список тренировок -->
    <div x-show="currentView === 'list' && !isLoading" x-cloak class="space-y-4">
        @if($workouts->count() > 0)
            @foreach($workouts as $workout)
                <div class="workout-card group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200 overflow-hidden">
                    <!-- Статус индикатор -->
                    <div class="absolute top-0 left-0 w-full h-1"
                         @if($workout->status === 'completed') style="background-color: #10b981;"
                         @elseif($workout->status === 'cancelled') style="background-color: #ef4444;"
                         @else style="background-color: #3b82f6;" @endif>
                    </div>

                    <div class="workout-content p-6">
                        <!-- Заголовок, мета-информация и статус -->
                        <div class="workout-header flex items-center justify-between mb-4">
                            <div class="workout-title-section flex items-center gap-4">
                                <h3 class="workout-title text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $workout->title }}</h3>
                                <div class="workout-meta flex items-center gap-3 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($workout->date)->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $workout->time ? \Carbon\Carbon::parse($workout->time)->format('H:i') : '' }}</span>
                                    </div>
                                    <div class="flex items-center duration-field">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $workout->duration }} мин</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $workout->trainer->name ?? 'Неизвестно' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Статус -->
                            <span class="workout-status px-3 py-1 rounded-full text-xs font-semibold
                                @if($workout->status === 'completed') bg-green-100 text-green-800
                                @elseif($workout->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                @if($workout->status === 'completed') Завершена
                                @elseif($workout->status === 'cancelled') Отменена
                                @else Запланирована @endif
                            </span>
                        </div>

                        <!-- Описание -->
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm line-clamp-2">{{ $workout->description ?? '' }}</p>

                            <!-- Упражнения -->
                            @if(($workout->exercises ?? [])->count() > 0)
                                <div class="mt-3">
                                    <div class="text-xs font-medium text-gray-500 mb-2">Упражнения:</div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(($workout->exercises ?? [])->take(3) as $exercise)
                                            <span class="inline-block px-2 py-1 text-xs rounded-full font-medium"
                                                  :class="{
                                                      'bg-green-100 text-green-700': getExerciseStatusForList({{ $workout->id }}, {{ $exercise->exercise_id ?? $exercise->id }}) === 'completed',
                                                      'bg-yellow-100 text-yellow-700': getExerciseStatusForList({{ $workout->id }}, {{ $exercise->exercise_id ?? $exercise->id }}) === 'partial',
                                                      'bg-red-100 text-red-700': getExerciseStatusForList({{ $workout->id }}, {{ $exercise->exercise_id ?? $exercise->id }}) === 'not_done',
                                                      'bg-gray-100 text-gray-600': getExerciseStatusForList({{ $workout->id }}, {{ $exercise->exercise_id ?? $exercise->id }}) === null
                                                  }"
                                                  :title="getExerciseStatusForList({{ $workout->id }}, {{ $exercise->exercise_id ?? $exercise->id }}) === 'partial' && workoutProgress[{{ $workout->id }}]?.[{{ $exercise->exercise_id ?? $exercise->id }}]?.athlete_comment ? 'Комментарий: ' + workoutProgress[{{ $workout->id }}][{{ $exercise->exercise_id ?? $exercise->id }}].athlete_comment : ''">{{ $exercise->name ?? 'Без названия' }}</span>
                                        @endforeach
                                        @if(($workout->exercises ?? [])->count() > 3)
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">+ {{ ($workout->exercises ?? [])->count() - 3 }} еще</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Кнопки действий -->
                        <div class="flex justify-end">
                            <button @click="showView({{ $workout->id }})" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-auto">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M12 15h.01"/>
                                </svg>
                                Подробнее
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Пагинация -->
            @if($workouts->hasPages())
                <div class="mt-6">
                    {{ $workouts->links() }}
                </div>
            @endif
        @else
            <!-- Пустое состояние -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Нет тренировок</h3>
                <p class="mt-1 text-sm text-gray-500">Ваш тренер пока не назначил тренировки.</p>
            </div>
        @endif
    </div>

    <!-- Просмотр тренировки -->
    <div x-show="currentView === 'view'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="workout-view-title text-xl font-semibold text-gray-900">Просмотр<span class="hidden-mobile"> тренировки</span></h3>
                <div x-show="lastSaved" class="text-sm text-green-600 mt-1">
                    💾 Последнее сохранение: <span x-text="lastSaved ? lastSaved.toLocaleTimeString('ru-RU') : ''"></span>
                </div>
            </div>
            <button @click="showList()" 
                    class="back-button px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                ← Назад<span class="hidden-mobile"> к списку</span>
            </button>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <!-- Заголовок и статус -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h4 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0;" x-text="currentWorkout?.title"></h4>
                <span style="padding: 6px 12px; border-radius: 9999px; font-size: 14px; font-weight: 600;"
                      :style="{
                          'background-color': currentWorkout?.status === 'completed' ? '#dcfce7' : 
                                            currentWorkout?.status === 'cancelled' ? '#fef2f2' : 
                                            currentWorkout?.status === 'planned' ? '#dbeafe' : '#fef3c7',
                          'color': currentWorkout?.status === 'completed' ? '#166534' : 
                                 currentWorkout?.status === 'cancelled' ? '#991b1b' : 
                                 currentWorkout?.status === 'planned' ? '#1e40af' : '#92400e'
                      }"
                      x-text="getStatusLabel(currentWorkout?.status)">
                </span>
            </div>
            
            <!-- Описание -->
            <div class="prose max-w-none" x-show="currentWorkout?.description">
                <h5 class="text-lg font-semibold text-gray-900 mb-3">Описание</h5>
                <p class="text-gray-600 whitespace-pre-line" x-text="currentWorkout?.description"></p>
            </div>
            
            <!-- Детали -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Дата</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;" x-show="currentWorkout?.time">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Время</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.time ? currentWorkout.time.substring(0, 5) : ''"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;" x-show="currentWorkout?.duration">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Продолжительность</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.duration + ' мин'"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Тренер</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.trainer?.name || 'Неизвестно'"></p>
                </div>
            </div>
            
            <!-- Упражнения -->
            <div x-show="(currentWorkout?.exercises || []).length > 0" class="pt-6 border-t border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900 mb-4">Упражнения</h5>
                <div class="space-y-4">
                    <template x-for="(exercise, index) in (currentWorkout?.exercises || [])" :key="`view-exercise-${index}`">
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-indigo-600 font-medium" x-text="(index + 1) + '.'"></span>
                                    <span class="text-sm font-medium text-gray-900" x-text="exercise.name || 'Без названия'"></span>
                                    <span class="text-xs text-gray-500" x-text="exercise.category || ''"></span>
                                    
                                </div>
                            </div>
                            
                            <!-- Параметры упражнения -->
                            <div class="exercise-params-grid grid grid-cols-4 gap-4">
                                <!-- Подходы -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('sets')" 
                                     class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-indigo-800">Подходы</span>
                                        </div>
                                        <div class="text-2xl font-bold text-indigo-900" x-text="exercise.sets || exercise.pivot?.sets || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Повторения -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('reps')" 
                                     class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">Повторения</span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900" x-text="exercise.reps || exercise.pivot?.reps || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Вес -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('weight')" 
                                     class="bg-gradient-to-r from-purple-50 to-violet-50 border-2 border-purple-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-purple-800">Вес (кг)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-purple-900" x-text="exercise.weight || exercise.pivot?.weight || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Отдых -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('rest')" 
                                     class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-orange-800">Отдых (мин)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-orange-900" x-text="exercise.rest || exercise.pivot?.rest || 1.0"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Статус выполнения упражнения -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="exercise-status-section flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">Статус выполнения:</span>
                                    <div class="exercise-status-buttons flex space-x-2">
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'completed')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'completed' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ✅ Выполнено
                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'partial')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'partial' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ⚠️ Частично
                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'not_done')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'not_done' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ❌ Не выполнено
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Поля для каждого подхода (появляется при выборе "Частично") -->
                                <div x-show="getExerciseStatus(exercise.exercise_id || exercise.id) === 'partial'" class="mt-4">
                                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3 cursor-pointer rounded-lg p-2 -m-2"
                                             @click="toggleSetsExpanded(exercise.exercise_id || exercise.id)">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <h6 class="text-sm font-semibold text-yellow-800">Детализация по подходам</h6>
                                            </div>
                                            <div class="flex items-center text-xs text-yellow-700 hover:text-yellow-800 transition-colors">
                                                <span x-text="isSetsExpanded(exercise.exercise_id || exercise.id) ? 'Свернуть' : 'Развернуть'"></span>
                                                <svg class="w-4 h-4 ml-1 transition-transform" 
                                                     :class="isSetsExpanded(exercise.exercise_id || exercise.id) ? 'rotate-180' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Индикатор свернутого состояния -->
                                        <div x-show="!isSetsExpanded(exercise.exercise_id || exercise.id)" 
                                             class="text-xs text-yellow-600 mb-2 cursor-pointer rounded-lg p-2 -m-2"
                                             @click="toggleSetsExpanded(exercise.exercise_id || exercise.id)">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Поля подходов свернуты. Нажмите для разворачивания.</span>
                                            </div>
                                        </div>
                                        
                                        <div x-show="isSetsExpanded(exercise.exercise_id || exercise.id)" x-transition>
                                            <p class="text-xs text-yellow-700 mb-4">Укажите, что именно вы выполнили в каждом подходе:</p>
                                        
                                        <div class="space-y-3">
                                            <template x-for="(set, setIndex) in getSetsData(exercise.exercise_id || exercise.id)" :key="`set-${exercise.exercise_id || exercise.id}-${setIndex}`">
                                                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                            <span class="text-sm font-semibold text-yellow-800">Подход <span x-text="setIndex + 1"></span></span>
                                                        </div>
                                                        <span class="text-xs text-yellow-600">из <span x-text="exercise.sets || exercise.pivot?.sets || 0"></span></span>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-3 gap-4">
                                                        <!-- Повторения -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('reps')" 
                                                             class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-3">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-green-800">Повторения</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    x-model="set.reps"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'reps', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-green-900 bg-transparent border-none outline-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Вес -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('weight')" 
                                                             class="bg-gradient-to-r from-purple-50 to-violet-50 border-2 border-purple-200 rounded-lg p-3">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-purple-800">Вес (кг)</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.5"
                                                                    x-model="set.weight"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'weight', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-purple-900 bg-transparent border-none outline-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Отдых -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('rest')" 
                                                             class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-lg p-3">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-orange-800">Отдых (мин)</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.1"
                                                                    x-model="set.rest"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'rest', $event.target.value)"
                                                                    placeholder="1.0"
                                                                    class="w-full text-center text-lg font-bold text-orange-900 bg-transparent border-none outline-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                            <div class="text-xs text-yellow-600 mt-3">
                                                💡 Изменения сохраняются автоматически при вводе
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Комментарий тренера к упражнению -->
                            <div x-show="exercise.notes || exercise.pivot?.notes" class="mt-4">
                                <div class="bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800 mb-1">Комментарий тренера:</div>
                                            <div class="text-sm text-gray-700" x-text="exercise.notes || exercise.pivot?.notes || ''"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* Мобильная адаптация для карточек тренировок */
@media (max-width: 640px) {
    .workout-card {
        margin: 0;
    }
    
    .workout-content {
        padding: 1rem;
    }
    
    .workout-header {
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 12px !important;
    }
    
    .workout-title-section {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
        flex: 1 !important;
        min-width: 0 !important;
    }
    
    .workout-title {
        font-size: 1.125rem !important;
        margin-bottom: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        max-width: 200px !important;
    }
    
    .workout-meta {
        flex-direction: row !important;
        align-items: center !important;
        gap: 12px !important;
        width: 100% !important;
        flex-wrap: nowrap !important;
    }
    
    .workout-meta span {
        white-space: nowrap !important;
    }
    
    .workout-status {
        align-self: flex-start !important;
        margin-top: 0 !important;
        flex-shrink: 0 !important;
        font-size: 10px !important;
        padding: 4px 8px !important;
    }
    
    
    .workout-content button {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .p-6 {
        padding: 1rem;
    }
    
    /* Скрытие лишнего текста на мобильной версии */
    .workout-view-title .hidden-mobile {
        display: none !important;
    }
    
    .back-button .hidden-mobile {
        display: none !important;
    }
    
    .exercise-params-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
    }
    
    /* Адаптация для полей подходов на мобильных */
    .grid.grid-cols-3 {
        grid-template-columns: 1fr !important;
        gap: 8px !important;
    }
    
    .exercise-status-buttons {
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        margin-top: 8px !important;
        margin-bottom: 8px !important;
    }
    
    .exercise-status-buttons button {
        font-size: 11px !important;
        padding: 6px 8px !important;
        flex: 1 !important;
        min-width: 0 !important;
    }
    
    .exercise-status-section {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}

/* Десктопная версия остается как есть */
@media (min-width: 641px) {
    .workout-header {
        flex-direction: row !important;
        align-items: center !important;
    }
    
    .workout-title-section {
        flex-direction: row !important;
        align-items: center !important;
    }
    
    .workout-meta {
        flex-direction: row !important;
        align-items: center !important;
    }
    
    .duration-field {
        display: flex !important;
    }
}
</style>
@endsection
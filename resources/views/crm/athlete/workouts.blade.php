@extends("crm.layouts.app")

@section("title", __('common.athlete_workouts'))
@section("page-title", __('common.workouts'))

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
        
        // Модальное окно для видео
        videoModal: {
            isOpen: false,
            url: '',
            title: ''
        },
                workoutProgress: {}, // Прогресс для каждой тренировки
                isLoading: true, // Флаг загрузки
                lastChangedExercise: null, // Последнее измененное упражнение
                exercisesExpanded: {}, // Хранение состояния развернутости упражнений в карточках

                // Инициализация
                init() {
                    this.loadAllWorkoutProgress();
                },

                // Навигация
                showList() {
                    // Обновляем данные в списке перед возвратом
                    if (this.currentWorkout && Object.keys(this.exerciseStatuses).length > 0) {
                        this.updateWorkoutProgressInList();
                    }
                    
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
                    // Если нажимаем на уже выбранный статус - снимаем его
                    if (this.exerciseStatuses[exerciseId] === status) {
                        // Помечаем как удаленный статус
                        this.exerciseStatuses[exerciseId] = null;
                        delete this.exerciseComments[exerciseId];
                        delete this.exerciseSetsData[exerciseId];
                        delete this.exerciseSetsExpanded[exerciseId];
                        this.lastChangedExercise = { id: exerciseId, status: null };
                    } else {
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
                    }
                    
                    // Немедленно обновляем данные в списке
                    this.updateWorkoutProgressInList();
                    
                    // Автосохранение через 2 секунды после изменения
                    this.autoSave();
                },

                getExerciseStatus(exerciseId) {
                    return this.exerciseStatuses[exerciseId] !== undefined ? this.exerciseStatuses[exerciseId] : null;
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

                // Получить класс рамки для поля в развернутых подходах
                getSetFieldBorderClass(exercise, set, fieldName) {
                    const plannedValue = parseFloat(exercise[fieldName] || exercise.pivot?.[fieldName]) || 0;
                    const actualValue = parseFloat(set[fieldName]) || 0;
                    
                    // Если поле не заполнено
                    if (actualValue === 0) {
                        return 'border-red-500 border-2';
                    }
                    
                    // Если запланированное значение равно 0 или не задано, не показываем красную рамку
                    if (plannedValue === 0) {
                        return '';
                    }
                    
                    // Если заполнено, но меньше запланированного
                    if (actualValue < plannedValue) {
                        return 'border-red-500 border-2';
                    }
                    
                    // Если заполнено полностью или больше
                    return '';
                },

                // Управление сворачиванием/разворачиванием упражнений в карточках
                toggleExercisesExpanded(workoutId) {
                    this.exercisesExpanded[workoutId] = !this.exercisesExpanded[workoutId];
                },

                // Проверка, развернуты ли упражнения в карточке
                isExercisesExpanded(workoutId) {
                    return this.exercisesExpanded[workoutId] || false;
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
                        const exercises = Object.keys(this.exerciseStatuses)
                            .filter(exerciseId => this.exerciseStatuses[exerciseId] !== undefined)
                            .map(exerciseId => ({
                                exercise_id: parseInt(exerciseId),
                                status: this.exerciseStatuses[exerciseId],
                                athlete_comment: this.exerciseComments[exerciseId] || null,
                                sets_data: this.exerciseSetsData[exerciseId] || null
                            }));

                        // Показываем индикатор загрузки
                        showInfo('{{ __('common.saving') }}...', '{{ __('common.saving') }} {{ __('common.workout_progress') }}...', 2000);

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
                                } else if (status === null) {
                                    title = 'Статус снят!';
                                    message = 'Статус упражнения сброшен';
                                }
                                
                                // Сбрасываем последнее измененное упражнение
                                this.lastChangedExercise = null;
                            } else {
                                // Fallback для случая, если lastChangedExercise не установлен
                                title = 'Статус обновлен!';
                                message = `Обновлено ${exercises.length} упражнений`;
                            }
                            
                            showSuccess(title, message);
                            
                            // Обновляем данные в списке тренировок
                            this.updateWorkoutProgressInList();
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

                // Обновление прогресса упражнений в списке тренировок
                updateWorkoutProgressInList() {
                    if (!this.currentWorkout) return;
                    
                    // Находим тренировку в списке
                    const workoutInList = this.workouts.find(w => w.id === this.currentWorkout.id);
                    if (!workoutInList) return;
                    
                    // Обновляем прогресс упражнений в списке
                    if (workoutInList.exercises) {
                        workoutInList.exercises.forEach(exercise => {
                            const exerciseId = exercise.exercise_id || exercise.id;
                            
                            // Обновляем статус упражнения в workoutProgress
                            if (this.exerciseStatuses[exerciseId]) {
                                if (!this.workoutProgress[this.currentWorkout.id]) {
                                    this.workoutProgress[this.currentWorkout.id] = {};
                                }
                                
                                this.workoutProgress[this.currentWorkout.id][exerciseId] = {
                                    status: this.exerciseStatuses[exerciseId],
                                    athlete_comment: this.exerciseComments[exerciseId] || null,
                                    sets_data: this.exerciseSetsData[exerciseId] || null
                                };
                            }
                        });
                        
                        // Принудительно обновляем реактивность Alpine.js
                        this.$nextTick(() => {
                            // Триггерим обновление через изменение массива
                            this.workouts = [...this.workouts];
                        });
                    }
                },

                // Вспомогательные методы
                getStatusLabel(status) {
                    const labels = {
                        'completed': '{{ __('common.completed_status') }}',
                        'cancelled': '{{ __('common.cancelled_status') }}',
                        'planned': '{{ __('common.planned_status') }}',
                        'in_progress': '{{ __('common.in_progress_status') }}'
                    };
                    return labels[status] || '{{ __('common.unknown') }}';
                },
                
                // Методы для работы с видео модальным окном
                openVideoModal(url, title) {
                    this.videoModal.isOpen = true;
                    this.videoModal.url = url;
                    this.videoModal.title = title;
                },
                
                closeVideoModal() {
                    this.videoModal.isOpen = false;
                    this.videoModal.url = '';
                    this.videoModal.title = '';
                },
                
                isYouTubeUrl(url) {
                    if (!url) return false;
                    return url.includes('youtube.com') || url.includes('youtu.be');
                },
                
                getYouTubeEmbedUrl(url) {
                    if (!url) return '';
                    
                    let videoId = '';
                    
                    // youtube.com/watch?v=VIDEO_ID
                    if (url.includes('youtube.com/watch?v=')) {
                        videoId = url.split('v=')[1].split('&')[0];
                    }
                    // youtu.be/VIDEO_ID
                    else if (url.includes('youtu.be/')) {
                        videoId = url.split('youtu.be/')[1].split('?')[0];
                    }
                    // youtube.com/embed/VIDEO_ID
                    else if (url.includes('youtube.com/embed/')) {
                        videoId = url.split('embed/')[1].split('?')[0];
                    }
                    
                    return videoId ? `https://www.youtube.com/embed/${videoId}` : '';
                },
                
                // Простой метод для открытия модального окна
                openSimpleModal(url, title) {
                    
                    // Создаем модальное окно
                    const modal = document.createElement('div');
                    modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0,0,0,0.8);
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    `;
                    
                    // Создаем контент
                    const content = document.createElement('div');
                    content.style.cssText = `
                        position: relative;
                        background: white;
                        border-radius: 12px;
                        padding: 20px;
                        width: 80%;
                        max-width: 800px;
                        max-height: 80%;
                        overflow: hidden;
                    `;
                    
                    // Создаем заголовок
                    const header = document.createElement('div');
                    header.style.cssText = `
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 20px;
                        border-bottom: 1px solid #eee;
                        padding-bottom: 10px;
                    `;
                    header.innerHTML = `
                        <h3 style="margin: 0; font-size: 18px; font-weight: bold;">${title}</h3>
                        <button style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    `;
                    
                    // Добавляем обработчик клика на кнопку закрытия
                    const closeButton = header.querySelector('button');
                    closeButton.addEventListener('click', function() {
                        modal.remove();
                    });
                    
                    // Создаем видео
                    const videoContainer = document.createElement('div');
                    if (url.includes('youtube.com') || url.includes('youtu.be')) {
                        const embedUrl = this.getYouTubeEmbedUrl(url);
                        videoContainer.style.cssText = `
                            position: relative;
                            padding-bottom: 56.25%;
                            height: 0;
                            overflow: hidden;
                        `;
                        videoContainer.innerHTML = `
                            <iframe src="${embedUrl}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                        `;
                    } else {
                        videoContainer.innerHTML = `
                            <div style="text-align: center;">
                                <a href="${url}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none;">
                                    {{ __('common.open_video') }}
                                </a>
                            </div>
                        `;
                    }
                    
                    // Собираем все вместе
                    content.appendChild(header);
                    content.appendChild(videoContainer);
                    modal.appendChild(content);
                    
                    // Добавляем в DOM
                    document.body.appendChild(modal);
                    
                    // Закрытие по клику на фон
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) {
                            modal.remove();
                        }
                    });
                },

                // Обновление статуса тренировки
                async updateWorkoutStatus(workoutId, newStatus) {
                    try {
                        const response = await fetch(`/athlete/workouts/${workoutId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        });

                        const result = await response.json();
                        
                        if (result.success) {
                            // Обновляем статус в текущей тренировке
                            if (this.currentWorkout && this.currentWorkout.id === workoutId) {
                                this.currentWorkout.status = newStatus;
                            }
                            
                            // Обновляем статус в списке тренировок
                            const workoutInList = this.workouts.find(w => w.id === workoutId);
                            if (workoutInList) {
                                workoutInList.status = newStatus;
                            }
                            
                            // Показываем уведомление
                            const statusLabels = {
                                'planned': 'Запланирована',
                                'completed': 'Завершена',
                                'cancelled': 'Отменена'
                            };
                            
                            showSuccess('Статус обновлен!', `Тренировка теперь: ${statusLabels[newStatus]}`);
                        } else {
                            showError('Ошибка', result.message || 'Не удалось обновить статус тренировки');
                        }
                    } catch (error) {
                        console.error('Ошибка обновления статуса:', error);
                        showError('Ошибка соединения', 'Проверьте подключение к интернету и попробуйте еще раз.');
                    }
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
    <a href="{{ route("crm.athlete.exercises") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
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
    <a href="{{ route("crm.athlete.exercises") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
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
            <span class="text-gray-600">{{ __('common.loading') }} {{ __('common.workouts') }}...</span>
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
                <div class="stat-label">{{ __('common.total_workouts') }}</div>
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
                <div class="stat-label">{{ __('common.completed') }}</div>
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
                <div class="stat-label">{{ __('common.remaining_workouts') }}</div>
                <div class="stat-value">{{ $remainingCount ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('common.planned') }}</div>
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
                                <div class="workout-meta flex flex-wrap items-center gap-3 text-sm text-gray-600">
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
                                @if($workout->status === 'completed') {{ __('common.workout_status_completed') }}
                                @elseif($workout->status === 'cancelled') {{ __('common.workout_status_cancelled') }}
                                @else {{ __('common.workout_status_planned') }} @endif
                            </span>
                        </div>

                        <!-- Описание -->
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm line-clamp-2">{{ $workout->description ?? '' }}</p>

                            <!-- Упражнения -->
                            @if(count($workout->exercises ?? []) > 0)
                                <div class="mt-3">
                                    <div class="text-xs font-medium text-gray-500 mb-2">{{ __('common.exercises') }}:</div>
                                    <div class="flex flex-wrap gap-1">
                                        <!-- Отображаем все упражнения через Alpine.js -->
                                        <template x-for="(exercise, index) in ({{ json_encode($workout->exercises ?? []) }} || [])" :key="`exercise-{{ $workout->id }}-${index}`">
                                            <span x-show="index < 3 || isExercisesExpanded({{ $workout->id }})"
                                                  class="inline-block px-2 py-1 text-xs rounded-full font-medium"
                                                  :class="{
                                                      'bg-green-100 text-green-700': getExerciseStatusForList({{ $workout->id }}, exercise.exercise_id || exercise.id) === 'completed',
                                                      'bg-yellow-100 text-yellow-700': getExerciseStatusForList({{ $workout->id }}, exercise.exercise_id || exercise.id) === 'partial',
                                                      'bg-red-100 text-red-700': getExerciseStatusForList({{ $workout->id }}, exercise.exercise_id || exercise.id) === 'not_done',
                                                      'bg-gray-100 text-gray-600': getExerciseStatusForList({{ $workout->id }}, exercise.exercise_id || exercise.id) === null
                                                  }"
                                                  :title="getExerciseStatusForList({{ $workout->id }}, exercise.exercise_id || exercise.id) === 'partial' && workoutProgress[{{ $workout->id }}]?.[exercise.exercise_id || exercise.id]?.athlete_comment ? '{{ __('common.comments') }}: ' + workoutProgress[{{ $workout->id }}][exercise.exercise_id || exercise.id].athlete_comment : ''"
                                                  x-text="exercise.name || '{{ __('common.no_title') }}'">
                                            </span>
                                        </template>
                                        
                                        <!-- Кнопка разворачивания/сворачивания -->
                                        @if(count($workout->exercises ?? []) > 3)
                                            <button @click="toggleExercisesExpanded({{ $workout->id }})" 
                                                    class="inline-block px-2 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 text-xs rounded-full transition-colors cursor-pointer">
                                                <span x-text="isExercisesExpanded({{ $workout->id }}) ? '{{ __('common.collapse') }}' : '+{{ count($workout->exercises ?? []) - 3 }} {{ __('common.more') }}'"></span>
                                            </button>
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
                                {{ __('common.view_details') }}
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('common.no_workouts') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('common.no_workouts_assigned') }}</p>
            </div>
        @endif
    </div>

    <!-- Просмотр тренировки -->
    <div x-show="currentView === 'view'" x-cloak x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <div x-show="lastSaved" class="text-sm text-green-600 mb-4">
                💾 {{ __('common.last_saved') }}: <span x-text="lastSaved ? lastSaved.toLocaleTimeString('ru-RU') : ''"></span>
            </div>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <!-- Заголовок и статус -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
                <h4 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0;" x-text="currentWorkout?.title"></h4>
                
                <!-- Выпадающий список статуса -->
                <div class="relative" x-data="{ statusDropdownOpen: false }">
                    <button @click="statusDropdownOpen = !statusDropdownOpen" 
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border hover:bg-gray-50 transition-colors"
                            :style="{
                                'background-color': currentWorkout?.status === 'completed' ? '#dcfce7' : 
                                                  currentWorkout?.status === 'cancelled' ? '#fef2f2' : '#dbeafe',
                                'color': currentWorkout?.status === 'completed' ? '#166534' : 
                                       currentWorkout?.status === 'cancelled' ? '#991b1b' : '#1e40af',
                                'border-color': currentWorkout?.status === 'completed' ? '#bbf7d0' : 
                                              currentWorkout?.status === 'cancelled' ? '#fecaca' : '#bfdbfe'
                            }">
                        <span x-text="getStatusLabel(currentWorkout?.status)"></span>
                        <svg class="w-4 h-4 transition-transform" :class="statusDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Выпадающий список -->
                    <div x-show="statusDropdownOpen" 
                         @click.away="statusDropdownOpen = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        <div class="py-1">
                            <button @click="updateWorkoutStatus(currentWorkout.id, 'planned'); statusDropdownOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-blue-50 transition-colors"
                                    :class="currentWorkout?.status === 'planned' ? 'bg-blue-100 text-blue-800' : 'text-gray-700'">
                                📅 {{ __('common.workout_status_planned') }}
                            </button>
                            <button @click="updateWorkoutStatus(currentWorkout.id, 'completed'); statusDropdownOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-green-50 transition-colors"
                                    :class="currentWorkout?.status === 'completed' ? 'bg-green-100 text-green-800' : 'text-gray-700'">
                                ✅ {{ __('common.workout_status_completed') }}
                            </button>
                            <button @click="updateWorkoutStatus(currentWorkout.id, 'cancelled'); statusDropdownOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-red-50 transition-colors"
                                    :class="currentWorkout?.status === 'cancelled' ? 'bg-red-100 text-red-800' : 'text-gray-700'">
                                ❌ {{ __('common.workout_status_cancelled') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Описание -->
            <div class="prose max-w-none" x-show="currentWorkout?.description">
                <h5 class="text-lg font-semibold text-gray-900 mb-3">{{ __('common.description') }}</h5>
                <p class="text-gray-600 whitespace-pre-line" x-text="currentWorkout?.description"></p>
            </div>
            
            <!-- Детали -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">{{ __('common.date') }}</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;" x-show="currentWorkout?.time">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">{{ __('common.time') }}</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.time ? currentWorkout.time.substring(0, 5) : ''"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;" x-show="currentWorkout?.duration">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">{{ __('common.duration') }}</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.duration + ' {{ __('common.min') }}'"></p>
                </div>
                
                <div style="background-color: #f9fafb; border-radius: 12px; padding: 16px;">
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 500; color: #6b7280;">{{ __('common.trainer') }}</span>
                    </div>
                    <p style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;" x-text="currentWorkout?.trainer?.name || '{{ __('common.unknown') }}'"></p>
                </div>
            </div>
            
            <!-- Упражнения -->
            <div x-show="(currentWorkout?.exercises || []).length > 0" class="pt-6 border-t border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.exercises') }}</h5>
                <div class="space-y-4">
                    <template x-for="(exercise, index) in (currentWorkout?.exercises || [])" :key="`view-exercise-${index}`">
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="mb-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm text-indigo-600 font-medium" x-text="(index + 1) + '.'"></span>
                                        <span class="text-sm font-medium text-gray-900" x-text="exercise.name || '{{ __('common.no_title') }}'"></span>
                                        <span class="text-xs text-gray-500" x-text="(exercise.category || '') + (exercise.category && exercise.equipment ? ' • ' : '') + (exercise.equipment || '')"></span>
                                    </div>
                                    <!-- Ссылка на видео упражнения - только на десктопе -->
                                    <div x-show="exercise.video_url" class="exercise-video-link">
                                        <button @click="openSimpleModal(exercise.video_url, exercise.name)"
                                                class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full transition-colors cursor-pointer">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                            </svg>
                                            {{ __('common.video') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Параметры упражнения -->
                            <div class="exercise-params-grid grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 w-full">
                                <!-- Подходы -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('sets')" 
                                     class="bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-indigo-800">{{ __('common.sets') }}</span>
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
                                            <span class="text-sm font-semibold text-green-800">{{ __('common.reps') }}</span>
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
                                            <span class="text-sm font-semibold text-purple-800">{{ __('common.weight') }} ({{ __('common.kg') }})</span>
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
                                            <span class="text-sm font-semibold text-orange-800">{{ __('common.rest') }} ({{ __('common.min') }})</span>
                                        </div>
                                        <div class="text-2xl font-bold text-orange-900" x-text="exercise.rest || exercise.pivot?.rest || 1.0"></div>
                                    </div>
                                </div>
                                
                                <!-- Время -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('time')" 
                                     class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-blue-800">{{ __('common.time_seconds') }}</span>
                                        </div>
                                        <div class="text-2xl font-bold text-blue-900" x-text="exercise.time || exercise.pivot?.time || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Темп -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('tempo')" 
                                     class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-purple-800">{{ __('common.tempo_speed') }}</span>
                                        </div>
                                        <div class="text-2xl font-bold text-purple-900" x-text="exercise.tempo || exercise.pivot?.tempo || ''"></div>
                                    </div>
                                </div>
                                
                                <!-- Дистанция -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('distance')" 
                                     class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">{{ __('common.distance_meters') }}</span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900" x-text="exercise.distance || exercise.pivot?.distance || 0"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Статус выполнения упражнения -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="exercise-status-section flex items-center justify-between mb-3">
                                    <span class="text-sm font-medium text-gray-700">{{ __('common.exercise_status_label') }}:</span>
                                    <div class="exercise-status-buttons flex space-x-2">
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'completed')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'completed' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ✅ {{ __('common.exercise_status_completed') }}
                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'partial')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'partial' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ⚠️ {{ __('common.exercise_status_partial') }}
                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'not_done')" 
                                                :class="getExerciseStatus(exercise.exercise_id || exercise.id) === 'not_done' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ❌ {{ __('common.exercise_status_not_done') }}
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
                                                    <div class="flex items-center mb-3">
                                                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                        </svg>
                                                        <span class="text-sm font-semibold text-yellow-800">Подход <span x-text="setIndex + 1"></span> из <span x-text="exercise.sets || exercise.pivot?.sets || 0"></span></span>
                                                    </div>
                                                    
                                                    <div class="flex gap-6 w-full">
                                                        <!-- Повторения -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('reps')" 
                                                             class="flex-1 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'reps')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-green-800">{{ __('common.reps') }}</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    x-model="set.reps"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'reps', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-green-900 bg-transparent border-none outline-none no-spinner"
                                                                    min="0"
                                                                    style="-moz-appearance: textfield;"
                                                                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                                                                    onblur="this.style.outline='none'; this.style.boxShadow='none';">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Вес -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('weight')" 
                                                             class="flex-1 bg-gradient-to-r from-purple-50 to-violet-50 border-2 border-purple-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'weight')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-purple-800">{{ __('common.weight') }} ({{ __('common.kg') }})</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.5"
                                                                    x-model="set.weight"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'weight', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-purple-900 bg-transparent border-none outline-none no-spinner"
                                                                    min="0"
                                                                    style="-moz-appearance: textfield;"
                                                                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                                                                    onblur="this.style.outline='none'; this.style.boxShadow='none';">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Отдых -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('rest')" 
                                                             class="flex-1 bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'rest')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-orange-800">{{ __('common.rest') }} ({{ __('common.min') }})</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.1"
                                                                    x-model="set.rest"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'rest', $event.target.value)"
                                                                    placeholder="1.0"
                                                                    class="w-full text-center text-lg font-bold text-orange-900 bg-transparent border-none outline-none no-spinner"
                                                                    min="0"
                                                                    style="-moz-appearance: textfield;"
                                                                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                                                                    onblur="this.style.outline='none'; this.style.boxShadow='none';">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Время -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('time')" 
                                                             class="flex-1 bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'time')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-blue-800">Время (сек)</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    x-model="set.time"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'time', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-blue-900 bg-transparent border-none outline-none no-spinner"
                                                                    min="0"
                                                                    style="-moz-appearance: textfield;"
                                                                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                                                                    onblur="this.style.outline='none'; this.style.boxShadow='none';">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Темп -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('tempo')" 
                                                             class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-3">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-purple-800">Темп/Скорость</span>
                                                                </div>
                                                                <input 
                                                                    type="text" 
                                                                    x-model="set.tempo"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'tempo', $event.target.value)"
                                                                    placeholder=""
                                                                    class="w-full text-center text-lg font-bold text-purple-900 bg-transparent border-none outline-none">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Дистанция -->
                                                        <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('distance')" 
                                                             class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-3">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-green-800">Дистанция (м)</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    x-model="set.distance"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'distance', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-green-900 bg-transparent border-none outline-none no-spinner"
                                                                    min="0"
                                                                    style="-moz-appearance: textfield;"
                                                                    onfocus="this.style.outline='none'; this.style.boxShadow='none';"
                                                                    onblur="this.style.outline='none'; this.style.boxShadow='none';">
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
                                            <div class="text-sm font-semibold text-gray-800 mb-1">{{ __('common.trainer_comment') }}:</div>
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
/* Стили для скрытия стрелок в полях ввода */
.no-spinner::-webkit-outer-spin-button,
.no-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.no-spinner {
    -moz-appearance: textfield;
}

.no-spinner:focus {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    background-color: transparent !important;
}

.no-spinner:active {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    background-color: transparent !important;
}

.no-spinner:hover {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
}

/* Агрессивное скрытие всех возможных рамок */
input[type="number"].no-spinner {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

input[type="number"].no-spinner:focus,
input[type="number"].no-spinner:active,
input[type="number"].no-spinner:hover {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
}

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
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 12px !important;
        width: 100% !important;
        flex-wrap: nowrap !important;
    }
    
    .workout-meta > span {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        display: inline-block !important;
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
        gap: 8px !important;
        width: 100% !important;
    }
    
    /* Адаптация для полей подходов на мобильных */
    .grid.grid-cols-2 {
        grid-template-columns: 1fr 1fr !important;
        gap: 8px !important;
        width: 100% !important;
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

/* Десктопная версия */
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
    
    .exercise-params-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important;
        gap: 16px !important;
        width: 100% !important;
    }
    
    /* Поля подходов на десктопе */
    .grid.grid-cols-1 {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)) !important;
        gap: 12px !important;
        width: 100% !important;
    }
}

/* Стили для статистических карточек */
.stats-container {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 1rem !important;
}

.stat-card {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    text-align: center !important;
    padding: 1.5rem !important;
    background: white !important;
    border-radius: 1rem !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid #e5e7eb !important;
}

.stat-icon {
    margin-bottom: 0.75rem !important;
    margin-right: 0 !important;
    margin-left: 0 !important;
}

.stat-content {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

@media (min-width: 768px) {
    .stat-card {
        flex-direction: row !important;
        align-items: center !important;
        text-align: left !important;
    }
    
    .stat-icon {
        margin-bottom: 0 !important;
        margin-right: 1rem !important;
    }
    
    .stat-content {
        align-items: flex-start !important;
    }
}

.stat-label {
    font-size: 0.875rem !important;
    color: #6b7280 !important;
    margin-bottom: 0.25rem !important;
}

.stat-value {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #111827 !important;
}

@media (min-width: 768px) {
    .stats-container {
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 1.5rem !important;
    }
}
</style>


@endsection
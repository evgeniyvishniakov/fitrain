@extends("crm.layouts.app")

@section("title", "Тренировки")
@section("page-title", "Тренировки")

<script>
// SPA функциональность для тренировок
function workoutApp() {
    return {
        currentView: 'list', // list, create, edit, view
        workouts: @json($workouts->items()),
        totalWorkouts: {{ $workouts->total() }},
        
        currentPage: {{ $workouts->currentPage() }},
        lastPage: {{ $workouts->lastPage() }},
        currentWorkout: null,
        search: '',
        status: '',
        itemsPerPage: 10,
        
        // Поля формы
        formTitle: '',
        formAthleteId: '',
        formDate: '',
        formTime: '',
        formDuration: 60,
        formDescription: '',
        formStatus: 'planned',
        
        // Функциональность заполнения упражнений для тренера
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
        lastChangedExercise: null, // Последнее измененное упражнение
        exercisesExpanded: {}, // Хранение состояния развернутости упражнений в карточках
        
        // Навигация
        showList() {
            // Обновляем данные в списке перед возвратом
            if (this.currentWorkout && Object.keys(this.exerciseStatuses).length > 0) {
                this.updateWorkoutProgressInList();
            }
            
            this.currentView = 'list';
            this.currentWorkout = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentWorkout = null;
            this.formTitle = '';
            this.formDescription = '';
            this.formAthleteId = '';
            this.formDate = new Date().toISOString().split('T')[0];
            this.formTime = '';
            this.formDuration = 60;
            this.formStatus = 'planned';
            
            // Очищаем форму упражнений
            document.getElementById('selectedExercisesContainer').style.display = 'none';
            document.getElementById('emptyExercisesState').style.display = 'block';
        },
        
        showEdit(workoutId) {
            this.currentView = 'edit';
            this.currentWorkout = this.workouts.find(w => w.id === workoutId);
            this.formTitle = this.currentWorkout.title;
            this.formDescription = this.currentWorkout.description || '';
            this.formAthleteId = this.currentWorkout.athlete_id;
            // Форматируем дату для input[type="date"]
            if (this.currentWorkout.date) {
                // Если дата уже в формате YYYY-MM-DD, используем её как есть
                if (typeof this.currentWorkout.date === 'string' && this.currentWorkout.date.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    this.formDate = this.currentWorkout.date;
                } else {
                    // Иначе извлекаем дату из ISO строки
                    const dateStr = this.currentWorkout.date.toString();
                    if (dateStr.includes('T')) {
                        this.formDate = dateStr.split('T')[0];
                    } else {
                        const date = new Date(this.currentWorkout.date);
                        this.formDate = date.toISOString().split('T')[0];
                    }
                }
            } else {
                this.formDate = new Date().toISOString().split('T')[0];
            }
            // Обрезаем секунды из времени (если есть)
            if (this.currentWorkout.time) {
                this.formTime = this.currentWorkout.time.substring(0, 5); // Берем только HH:MM
            } else {
                this.formTime = '';
            }
            this.formDuration = this.currentWorkout.duration || 60;
            this.formStatus = this.currentWorkout.status;
            
            
            // Загружаем упражнения в форму
            const exercises = this.currentWorkout.exercises || [];
            
            if (exercises.length > 0) {
                // Преобразуем данные из формата Laravel Eloquent в нужный формат
                const formattedExercises = exercises.map(exercise => ({
                    id: exercise.exercise_id, // Используем exercise_id вместо id!
                    name: exercise.name,
                    sets: exercise.sets || exercise.pivot?.sets || 3,
                    reps: exercise.reps || exercise.pivot?.reps || 12,
                    weight: exercise.weight || exercise.pivot?.weight || 0,
                    rest: exercise.rest || exercise.pivot?.rest || 60,
                    time: exercise.time || exercise.pivot?.time || 0,
                    distance: exercise.distance || exercise.pivot?.distance || 0,
                    tempo: exercise.tempo || exercise.pivot?.tempo || '',
                    notes: exercise.notes || exercise.pivot?.notes || '',
                    category: exercise.category || '',
                    fields_config: exercise.fields_config || ['sets', 'reps', 'weight', 'rest']
                }));
                
                this.displaySelectedExercises(formattedExercises);
            } else {
                // Очищаем форму упражнений
                document.getElementById('selectedExercisesContainer').style.display = 'none';
                document.getElementById('emptyExercisesState').style.display = 'block';
            }
        },
        
        showView(workoutId) {
            this.currentView = 'view';
            this.currentWorkout = this.workouts.find(w => w.id === workoutId);
            // Загружаем сохраненный прогресс при открытии тренировки
            this.loadExerciseProgress(workoutId);
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
                    
                    // Обновляем статус упражнения
                    if (this.exerciseStatuses[exerciseId]) {
                        if (!exercise.progress) {
                            exercise.progress = {};
                        }
                        exercise.progress.status = this.exerciseStatuses[exerciseId];
                        
                        // Обновляем комментарий
                        if (this.exerciseComments[exerciseId]) {
                            exercise.progress.athlete_comment = this.exerciseComments[exerciseId];
                        }
                        
                        // Обновляем данные по подходам
                        if (this.exerciseSetsData[exerciseId]) {
                            exercise.progress.sets_data = this.exerciseSetsData[exerciseId];
                        }
                    }
                });
                
                // Принудительно обновляем реактивность Alpine.js
                this.$nextTick(() => {
                    // Триггерим обновление через изменение массива
                    this.workouts = [...this.workouts];
                });
            }
        },
        
        // Методы для работы с упражнениями (скопированы из athlete/workouts.blade.php)
        
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
                    // Поля остаются свернутыми по умолчанию
                    this.exerciseSetsExpanded[exerciseId] = false;
                }
            } else {
                // Если статус не "частично", очищаем комментарий и данные по подходам
                delete this.exerciseComments[exerciseId];
                delete this.exerciseSetsData[exerciseId];
                delete this.exerciseSetsExpanded[exerciseId];
            }
            
            // Немедленно обновляем данные в списке
            this.updateWorkoutProgressInList();
            
            // Автосохранение через 2 секунды после изменения
            this.autoSave();
        },
        
        // Получение статуса упражнения
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

        // Управление сворачиванием/разворачиванием упражнений в карточках
        toggleExercisesExpanded(workoutId) {
            this.exercisesExpanded[workoutId] = !this.exercisesExpanded[workoutId];
        },

        // Проверка, развернуты ли упражнения в карточке
        isExercisesExpanded(workoutId) {
            return this.exercisesExpanded[workoutId] || false;
        },
        
        // Автосохранение
        autoSave() {
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            
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
                showInfo('Сохранение...', 'Сохраняем прогресс...', 2000);

                const response = await fetch('/trainer/exercise-progress', {
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
        
        // Загрузка сохраненного прогресса (только из данных упражнений)
        loadExerciseProgress(workoutId) {
            // Загружаем данные из уже существующего прогресса в упражнениях
            if (this.currentWorkout && this.currentWorkout.exercises) {
                this.currentWorkout.exercises.forEach(exercise => {
                    const exerciseId = exercise.exercise_id || exercise.id;
                    if (exercise.progress && exercise.progress.status) {
                        this.exerciseStatuses[exerciseId] = exercise.progress.status;
                        
                        if (exercise.progress.athlete_comment) {
                            this.exerciseComments[exerciseId] = exercise.progress.athlete_comment;
                        }
                        
                        // Загружаем данные по подходам из прогресса спортсмена
                        if (exercise.progress.sets_data) {
                            this.exerciseSetsData[exerciseId] = exercise.progress.sets_data;
                        }
                        
                        // Поля подходов свернуты по умолчанию
                        this.exerciseSetsExpanded[exerciseId] = false;
                    }
                });
            }
            
            // Дополнительно: если есть данные в exerciseSetsData, но нет статуса, устанавливаем "частично"
            Object.keys(this.exerciseSetsData).forEach(exerciseId => {
                if (!this.exerciseStatuses[exerciseId] && this.exerciseSetsData[exerciseId] && this.exerciseSetsData[exerciseId].length > 0) {
                    this.exerciseStatuses[exerciseId] = 'partial';
                    this.exerciseSetsExpanded[exerciseId] = false; // Свернуто по умолчанию
                }
            });
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
        
        // Пагинация
        get totalPages() {
            // Клиентская пагинация
            return Math.ceil(this.filteredWorkouts.length / this.itemsPerPage) || 1;
        },
        
        get visiblePages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 5) {
                // Если страниц 5 или меньше, показываем все
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Показываем максимум 5 страниц
                let start = Math.max(1, current - 2);
                let end = Math.min(total, start + 4);
                
                if (end - start < 4) {
                    start = Math.max(1, end - 4);
                }
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
            }
            
            return pages;
        },
        
        get paginatedWorkouts() {
            // Клиентская пагинация
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredWorkouts.slice(start, end);
        },
        
        goToPage(page) {
            this.currentPage = page;
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        // Вспомогательные методы
        getStatusLabel(status) {
            const labels = {
                'completed': 'Завершена',
                'cancelled': 'Отменена',
                'planned': 'Запланирована'
            };
            return labels[status] || status;
        },
        
        // Сбор данных упражнений
        collectExerciseData() {
            const exercises = [];
            const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
            
            exerciseElements.forEach(element => {
                const exerciseId = element.dataset.exerciseId;
                
                // Ищем название упражнения - это второй span с классом font-medium
                const nameSpans = element.querySelectorAll('.font-medium');
                const exerciseName = nameSpans.length > 1 ? nameSpans[1].textContent : nameSpans[0].textContent;
                
                // Собираем все поля динамически
                const exerciseData = {
                    exercise_id: parseInt(exerciseId),
                    name: exerciseName
                };
                
                // Находим все input поля для этого упражнения
                const inputs = element.querySelectorAll('input');
                inputs.forEach(input => {
                    const name = input.name;
                    if (name.startsWith('notes_')) {
                        exerciseData.notes = input.value || '';
                    } else {
                        // Извлекаем название поля (sets, reps, weight, etc.)
                        const fieldName = name.replace(`_${exerciseId}`, '');
                        const value = input.type === 'number' ? 
                            (parseFloat(input.value) || 0) : 
                            (input.value || '');
                        exerciseData[fieldName] = value;
                    }
                });
                
                exercises.push(exerciseData);
            });
            
            return exercises;
        },
        
        // Сохранение
        async saveWorkout() {
            console.log('💾 Начинаем сохранение тренировки...');
            try {
                // Собираем данные упражнений
                const exercises = this.collectExerciseData();
                console.log('📋 Собрано упражнений:', exercises.length);
                
                const workoutData = {
                    title: this.formTitle,
                    description: this.formDescription,
                    athlete_id: this.formAthleteId,
                    date: this.formDate,
                    time: this.formTime,
                    duration: this.formDuration,
                    status: this.formStatus,
                    exercises: exercises
                };
                
                const url = this.currentWorkout && this.currentWorkout.id ? 
                    `/workouts/${this.currentWorkout.id}` : '/workouts';
                const method = this.currentWorkout && this.currentWorkout.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(workoutData)
                });
                
                const result = await response.json();
                console.log('📡 Ответ сервера:', result);
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentWorkout && this.currentWorkout.id ? 'Тренировка обновлена' : 'Тренировка создана',
                            message: this.currentWorkout && this.currentWorkout.id ? 
                                'Тренировка успешно обновлена' : 
                                'Тренировка успешно добавлена в календарь'
                        }
                    }));
                    
                    // Обновляем список тренировок
                    if (this.currentWorkout && this.currentWorkout.id) {
                        // Редактирование - обновляем существующую
                        console.log('🔄 Обновляем существующую тренировку:', this.currentWorkout.id);
                        const index = this.workouts.findIndex(w => w.id === this.currentWorkout.id);
                        if (index !== -1) {
                            console.log('📝 Найден индекс тренировки:', index);
                            // Обновляем упражнения с сохранением fields_config
                            const updatedExercises = exercises.map(exercise => {
                                // Находим оригинальное упражнение для получения fields_config
                                // Ищем по exercise_id, так как в this.workouts у нас id != exercise_id
                                const originalExercise = this.workouts[index].exercises?.find(ex => ex.exercise_id === exercise.exercise_id);
                                
                                return {
                                    id: exercise.exercise_id, // Это правильный ID из базы данных
                                    exercise_id: exercise.exercise_id, // Сохраняем exercise_id для поиска
                                    name: exercise.name,
                                    category: originalExercise?.category || '',
                                    fields_config: originalExercise?.fields_config || ['sets', 'reps', 'weight', 'rest'],
                                    pivot: {
                                        sets: exercise.sets,
                                        reps: exercise.reps,
                                        weight: exercise.weight,
                                        rest: exercise.rest,
                                        time: exercise.time,
                                        distance: exercise.distance,
                                        tempo: exercise.tempo,
                                        notes: exercise.notes
                                    }
                                };
                            });
                            
                            this.workouts[index] = { 
                                ...this.currentWorkout, 
                                ...workoutData,
                                exercises: updatedExercises
                            };
                            console.log('✅ Тренировка обновлена в массиве');
                            
                            // Обновляем currentWorkout если мы сейчас просматриваем эту тренировку
                            if (this.currentWorkout && this.currentWorkout.id === this.workouts[index].id) {
                                this.currentWorkout = this.workouts[index];
                                console.log('🔄 Обновлен currentWorkout');
                            }
                            
                            // Загружаем прогресс для всех тренировок
                            console.log('🔄 После обновления тренировки загружаем прогресс...');
                            await this.loadAllExerciseProgress();
                        }
                    } else {
                        // Создание - добавляем новую
                        this.workouts.unshift(result.workout);
                    }
                    
                    // Переключаемся на список
                    this.showList();
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении тренировки'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: 'Ошибка',
                        message: 'Произошла ошибка при сохранении тренировки'
                    }
                }));
            }
        },
        
        // Удаление
        deleteWorkout(id) {
            const workout = this.workouts.find(w => w.id === id);
            const workoutTitle = workout ? workout.title : 'тренировку';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: 'Удалить тренировку',
                    message: `Вы уверены, что хотите удалить тренировку "${workoutTitle}"?`,
                    confirmText: 'Удалить',
                    cancelText: 'Отмена',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/workouts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: 'Тренировка удалена',
                            message: 'Тренировка успешно удалена из календаря'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.workouts = this.workouts.filter(w => w.id !== id);
                    
                    // Если удалили все тренировки на текущей странице, переходим на предыдущую
                    if (this.paginatedWorkouts.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении тренировки'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: 'Ошибка',
                        message: 'Произошла ошибка при удалении тренировки'
                    }
                }));
            }
        },
        
        // Загрузка прогресса для всех тренировок
        async loadAllExerciseProgress() {
            console.log('🔄 Загружаем прогресс для всех тренировок...');
            try {
                for (let workout of this.workouts) {
                    console.log(`📋 Тренировка ${workout.id}:`, workout.exercises?.length || 0, 'упражнений');
                    if (workout.exercises) {
                        for (let exercise of workout.exercises) {
                            console.log(`🏋️ Упражнение ${exercise.exercise_id || exercise.id}:`, exercise.name);
                            const response = await fetch(`/trainer/exercise-progress?workout_id=${workout.id}`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            
                            if (response.ok) {
                                const progressData = await response.json();
                                console.log('📊 Данные прогресса:', progressData);
                                const exerciseId = exercise.exercise_id || exercise.id;
                                const progress = progressData.find(p => p.exercise_id === exerciseId);
                                
                                if (progress) {
                                    console.log('✅ Найден прогресс:', progress);
                                    exercise.progress = {
                                        status: progress.status,
                                        athlete_comment: progress.athlete_comment,
                                        completed_at: progress.completed_at
                                    };
                                } else {
                                    console.log('❌ Прогресс не найден для упражнения', exerciseId);
                                    exercise.progress = {
                                        status: null,
                                        athlete_comment: null,
                                        completed_at: null
                                    };
                                }
                            } else {
                                console.error('❌ Ошибка загрузки прогресса:', response.status);
                            }
                        }
                    }
                }
                console.log('✅ Прогресс загружен для всех тренировок');
            } catch (error) {
                console.error('❌ Ошибка загрузки прогресса:', error);
            }
        },
        
        
        // Отображение выбранных упражнений в форме
        displaySelectedExercises(exercises) {
            const container = document.getElementById('selectedExercisesContainer');
            const list = document.getElementById('selectedExercisesList');
            const emptyState = document.getElementById('emptyExercisesState');
            
            if (!container || !list || !emptyState) {
                console.error('Не найдены элементы для отображения упражнений');
                return;
            }
            
            if (exercises.length > 0) {
                // Скрываем пустое состояние
                emptyState.style.display = 'none';
                
                // Показываем контейнер с упражнениями
                container.style.display = 'block';
                
                // Отображаем упражнения с динамическими полями
                list.innerHTML = exercises.map((exercise, index) => {
                    const fieldsConfig = exercise.fields_config || ['sets', 'reps', 'weight', 'rest'];
                    const fieldsHtml = this.generateFieldsHtml(exercise.id, fieldsConfig, exercise);
                    
                    return `
                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm" data-exercise-id="${exercise.id}">
                            <!-- Заголовок упражнения -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1 cursor-pointer" onclick="toggleExerciseDetails(${exercise.id})">
                                    <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                                    <span class="text-sm font-medium text-gray-900">${exercise.name}</span>
                                    <span class="text-xs text-gray-500">${exercise.category || ''}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg id="chevron-${exercise.id}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                    <button onclick="removeExercise(${exercise.id})" class="text-red-500 hover:text-red-700 text-sm">
                                        Удалить
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Параметры упражнения - сворачиваемые -->
                            <div id="details-${exercise.id}" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                <div class="exercise-params-grid grid grid-cols-4 gap-4">
                                    ${fieldsHtml}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                // Показываем пустое состояние
                emptyState.style.display = 'block';
                container.style.display = 'none';
            }
        },
        
        // Генерация HTML для полей упражнения (точная копия оригинальной функции)
        generateFieldsHtml(exerciseId, fieldsConfig, exerciseData = null) {
            const fieldConfigs = {
                'sets': {
                    label: 'Подходы',
                    icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    color: 'indigo',
                    type: 'number',
                    min: '1',
                    max: '20',
                    value: '3'
                },
                'reps': {
                    label: 'Повторения',
                    icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                    color: 'green',
                    type: 'number',
                    min: '1',
                    max: '100',
                    value: '10'
                },
                'weight': {
                    label: 'Вес (кг)',
                    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
                    color: 'orange',
                    type: 'number',
                    min: '0',
                    max: '1000',
                    step: '0.5',
                    value: '0'
                },
                'rest': {
                    label: 'Отдых (мин)',
                    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'purple',
                    type: 'number',
                    min: '0',
                    max: '60',
                    step: '0.5',
                    value: '2'
                },
                'time': {
                    label: 'Время (сек)',
                    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'blue',
                    type: 'number',
                    min: '0',
                    max: '3600',
                    step: '1',
                    value: '0'
                },
                'distance': {
                    label: 'Дистанция (м)',
                    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                    color: 'green',
                    type: 'number',
                    min: '0',
                    max: '10000',
                    step: '1',
                    value: '0'
                },
                'tempo': {
                    label: 'Темп/Скорость',
                    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                    color: 'purple',
                    type: 'text',
                    placeholder: '2-1-2 или 8 км/ч',
                    value: ''
                }
            };

            const getColorClasses = (color) => {
                const colors = {
                    'indigo': {
                        input: 'bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200',
                        focusRing: 'focus:ring-indigo-100'
                    },
                    'green': {
                        input: 'bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200',
                        focusRing: 'focus:ring-green-100'
                    },
                    'orange': {
                        input: 'bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200',
                        focusRing: 'focus:ring-orange-100'
                    },
                    'purple': {
                        input: 'bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200',
                        focusRing: 'focus:ring-purple-100'
                    },
                    'blue': {
                        input: 'bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200',
                        focusRing: 'focus:ring-blue-100'
                    }
                };
                return colors[color] || colors['gray'];
            };

            let html = '';
            
            // Генерируем поля из конфигурации
            fieldsConfig.forEach(field => {
                if (fieldConfigs[field]) {
                    const config = fieldConfigs[field];
                    const colorClasses = getColorClasses(config.color);
                    
                    // Получаем значение из данных упражнения или из текущей тренировки
                    let value = config.value;
                    if (exerciseData && exerciseData[field] !== undefined) {
                        value = exerciseData[field];
                    } else {
                        const savedValue = this.getExerciseFieldValue(exerciseId, field);
                        if (savedValue) {
                            value = savedValue;
                        }
                    }
                    
                    html += `
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-${config.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}"/>
                                </svg>
                                ${config.label}
                            </label>
                            <div class="relative">
                                <input type="${config.type}" 
                                       name="${field}_${exerciseId}" 
                                       ${config.min ? `min="${config.min}"` : ''}
                                       ${config.max ? `max="${config.max}"` : ''}
                                       ${config.step ? `step="${config.step}"` : ''}
                                       ${config.placeholder ? `placeholder="${config.placeholder}"` : ''}
                                       value="${value}"
                                       class="w-full px-4 py-3 text-lg font-semibold text-center ${colorClasses.input} focus:ring-4 ${colorClasses.focusRing} focus:border-${config.color}-400 transition-all duration-200 hover:border-${config.color}-300 rounded-lg">
                            </div>
                        </div>
                    `;
                }
            });
            
            // Всегда добавляем примечания
            const notesValue = exerciseData && exerciseData.notes !== undefined ? exerciseData.notes : '';
            html += `
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Примечания
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="notes_${exerciseId}" 
                               value="${notesValue}"
                               placeholder="Дополнительные заметки..."
                               class="w-full px-4 py-3 text-sm bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-200 rounded-lg focus:ring-4 focus:ring-gray-100 focus:border-gray-400 transition-all duration-200 hover:border-gray-300 placeholder-gray-500">
                    </div>
                </div>
            `;
            
            return html;
        },
        
        // Получение значения поля упражнения
        getExerciseFieldValue(exerciseId, field) {
            if (this.currentWorkout && this.currentWorkout.exercises) {
                const exercise = (this.currentWorkout.exercises || []).find(ex => ex.id === exerciseId);
                return exercise && exercise[field] ? exercise[field] : '';
            }
            return '';
        },
        
        // Форматирование даты для отображения
        formatDate(dateString) {
            if (!dateString) return '';
            
            // Если дата уже в формате YYYY-MM-DD, просто форматируем её
            if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month, day] = dateString.split('-');
                return `${day}.${month}.${year}`;
            }
            
            // Если дата в формате YYYY-MM-DDTHH:mm:ss.sssZ (ISO), извлекаем только дату
            if (typeof dateString === 'string' && dateString.includes('T')) {
                const datePart = dateString.split('T')[0];
                const [year, month, day] = datePart.split('-');
                return `${day}.${month}.${year}`;
            }
            
            // Иначе используем стандартное форматирование
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU');
        },
        
        // Методы для работы с видео модальным окном
        openVideoModal(url, title) {
            console.log('Opening video modal:', { url, title });
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
                            Открыть видео
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
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route("crm.exercises.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
        <a href="{{ route('crm.trainer.subscription') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Подписка
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="{{ route('crm.trainer.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
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
    <a href="{{ route("crm.exercises.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
        <a href="{{ route('crm.trainer.subscription') }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Подписка
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="{{ route('crm.trainer.settings') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("header-actions")
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
@endsection

@section("content")
<div x-data="workoutApp()" x-cloak class="space-y-6">
    

    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <style>
                .filters-row {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 1rem !important;
                }
                
                .workout-details-grid {
                    display: grid !important;
                    gap: 1.5rem !important;
                    grid-template-columns: repeat(2, 1fr) !important;
                }
                
                @media (min-width: 768px) {
                    .workout-details-grid {
                        grid-template-columns: repeat(4, 1fr) !important;
                    }
                }
                
                .exercise-params-grid {
                    display: grid !important;
                    gap: 1rem !important;
                    grid-template-columns: repeat(2, 1fr) !important;
                }
                
                @media (min-width: 768px) {
                    .exercise-params-grid {
                        grid-template-columns: repeat(4, 1fr) !important;
                    }
                }
                
                /* Специальные отступы для полей упражнений */
                .exercise-field .mr-2 {
                    margin-right: 0.2rem !important;
                }
                
                @media (min-width: 768px) {
                    .exercise-field .mr-2 {
                        margin-right: 0.5rem !important;
                    }
                }
                
                /* Уменьшенный padding для мобилки */
                .p-6 {
                    padding: 1rem !important;
                }
                
                @media (min-width: 768px) {
                    .p-6 {
                        padding: 1.5rem !important;
                    }
                }
                
                /* Поля подходов - колонка на мобилке, 3 колонки на десктопе */
                .sets-fields-grid {
                    display: grid !important;
                    gap: 1rem !important;
                    grid-template-columns: 1fr !important;
                }
                
                @media (min-width: 768px) {
                    .sets-fields-grid {
                        grid-template-columns: repeat(3, 1fr) !important;
                    }
                }
                
                /* Убираем стрелочки у полей ввода чисел */
                input[type="number"]::-webkit-outer-spin-button,
                input[type="number"]::-webkit-inner-spin-button {
                    -webkit-appearance: none !important;
                    margin: 0 !important;
                }
                
                input[type="number"] {
                    -moz-appearance: textfield !important;
                }
                
                /* Уменьшенный padding для .p-4 на мобилке */
                .p-4 {
                    padding: 0.5rem !important;
                }
                
                @media (min-width: 768px) {
                    .p-4 {
                        padding: 1rem !important;
                    }
                }
                
                /* Статус выполнения - колонка на мобилке, ряд на десктопе */
                .exercise-status-section {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 0.5rem !important;
                }
                
                @media (min-width: 768px) {
                    .exercise-status-section {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                }
                
                /* Заголовок упражнения - колонка на мобилке, ряд на десктопе */
                .exercise-header-section {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 0.5rem !important;
                    margin-bottom: 0.75rem !important;
                }
                
                @media (min-width: 768px) {
                    .exercise-header-section {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                }
                
                /* Заголовок тренировки - колонка на мобилке, ряд на десктопе */
                .workout-title-section {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 0.5rem !important;
                }
                
                @media (min-width: 768px) {
                    .workout-title-section {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                }
                .filters-row > div {
                    width: 100% !important;
                }
                .filters-row .buttons-container {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 0.75rem !important;
                }
                @media (min-width: 640px) {
                    .filters-row .buttons-container {
                        flex-direction: row !important;
                    }
                }
                @media (min-width: 1024px) {
                    .filters-row {
                        display: flex !important;
                        flex-direction: row !important;
                        align-items: center !important;
                        gap: 1rem !important;
                    }
                    .filters-row > div {
                        width: auto !important;
                    }
                    .filters-row .search-container {
                        flex: 1 !important;
                        min-width: 200px !important;
                    }
                    .filters-row .status-container {
                        width: 200px !important;
                    }
                    .filters-row .buttons-container {
                        display: flex !important;
                        flex-direction: row !important;
                        gap: 0.75rem !important;
                        flex-shrink: 0 !important;
                    }
                }
                
                /* Стили для заголовка и кнопок упражнений */
                .exercise-header-row {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 1rem !important;
                    margin-bottom: 1rem !important;
                }
                
                .exercise-buttons-container {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 0.75rem !important;
                    width: 100% !important;
                }
                
                @media (min-width: 768px) {
                    .exercise-header-row {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                        gap: 1rem !important;
                    }
                    
                    .exercise-buttons-container {
                        flex-direction: row !important;
                        width: auto !important;
                        flex-shrink: 0 !important;
                    }
                }
                
                /* Стили для названия и спортсмена в одном ряду */
                .workout-title-athlete-row {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 1.5rem !important;
                }
                
                .workout-title-field,
                .workout-athlete-field {
                    flex: 1 !important;
                }
                
                @media (min-width: 1024px) {
                    .workout-title-athlete-row {
                        flex-direction: row !important;
                        gap: 1rem !important;
                    }
                }
                
                /* Стили для даты и продолжительности в одном ряду */
                .workout-date-duration-row {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 1.5rem !important;
                }
                
                .workout-date-field,
                .workout-time-field,
                .workout-duration-field {
                    flex: 1 !important;
                }
                
                /* Стили для иконки часов */
                .workout-time-field .relative {
                    position: relative;
                }
                
                .workout-time-field .absolute {
                    position: absolute;
                    top: 0;
                    right: 0;
                    bottom: 0;
                    display: flex;
                    align-items: center;
                    padding-right: 0.75rem;
                    pointer-events: none;
                }
                
                .workout-time-field input[type="time"] {
                    padding-right: 3rem !important;
                }
                
                
                
                
                @media (min-width: 1024px) {
                    .workout-date-duration-row {
                        flex-direction: row !important;
                        gap: 1rem !important;
                    }
                }
            </style>
            <div class="filters-row">
                <!-- Поиск -->
                <div class="search-container">
                    <input type="text" 
                           x-model="search" 
                           placeholder="Поиск тренировок..." 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр статуса -->
                <div class="status-container">
                    <select x-model="status" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все статусы</option>
                        <option value="planned">Запланирована</option>
                        <option value="completed">Завершена</option>
                        <option value="cancelled">Отменена</option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    @if(auth()->user()->hasRole('trainer'))
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            Добавить тренировку
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search || status" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">Активные фильтры:</span>
                
                <span x-show="search" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Поиск: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
                
                <span x-show="status" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Статус: <span x-text="getStatusLabel(status)"></span>
                    <button @click="status = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
            </div>
        </div>
    </div>

    <!-- СПИСОК ТРЕНИРОВОК -->
    <div x-show="currentView === 'list'" class="space-y-4">
        <template x-for="workout in paginatedWorkouts" :key="workout.id">
            <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200 overflow-hidden">
                <!-- Статус индикатор -->
                <div class="absolute top-0 left-0 w-full h-1" 
                     :class="{
                         'bg-green-500': workout.status === 'completed',
                         'bg-red-500': workout.status === 'cancelled',
                         'bg-blue-500': workout.status === 'planned'
                     }">
                </div>
                
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <!-- Аватарка спортсмена -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-semibold text-lg">
                                <span x-text="(workout.athlete?.name || workout.trainer?.name || '?').charAt(0).toUpperCase()"></span>
                            </div>
                        </div>
                        
                        <!-- Статус -->
                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                              :class="{
                                  'bg-green-100 text-green-800': workout.status === 'completed',
                                  'bg-red-100 text-red-800': workout.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': workout.status === 'planned'
                              }"
                              x-text="getStatusLabel(workout.status)">
                        </span>
                    </div>
                    
                    <!-- Заголовок и описание -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors" x-text="workout.title"></h3>
                        <p class="text-gray-600 text-sm line-clamp-2" x-text="workout.description || ''"></p>
                        
                        <!-- Упражнения -->
                        <div x-show="(workout.exercises || []).length > 0" class="mt-3">
                            <div class="mb-2">
                                <div class="text-xs font-medium text-gray-500">Упражнения:</div>
                            </div>
                            <div class="flex flex-wrap gap-1">
                                <!-- Отображаем все упражнения через Alpine.js -->
                                <template x-for="(exercise, index) in (workout.exercises || [])" :key="`exercise-${workout.id}-${index}`">
                                    <span x-show="index < 3 || isExercisesExpanded(workout.id)"
                                          class="inline-block px-2 py-1 text-xs rounded-full font-medium"
                                          :class="{
                                              'bg-green-100 text-green-800': exercise.progress?.status === 'completed',
                                              'bg-yellow-100 text-yellow-800': exercise.progress?.status === 'partial',
                                              'bg-red-100 text-red-800': exercise.progress?.status === 'not_done',
                                              'bg-gray-100 text-gray-600': !exercise.progress || !exercise.progress.status
                                          }"
                                          @click="console.log('🏋️ Упражнение:', exercise.name, 'Прогресс:', exercise.progress)"
                                          :title="exercise.progress?.athlete_comment ? 'Комментарий: ' + exercise.progress.athlete_comment : ''"
                                          x-text="exercise.name || 'Без названия'">
                                    </span>
                                </template>
                                
                                <!-- Кнопка разворачивания/сворачивания -->
                                <button x-show="(workout.exercises || []).length > 3" 
                                        @click="toggleExercisesExpanded(workout.id)" 
                                        class="inline-block px-2 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 text-xs rounded-full transition-colors cursor-pointer">
                                    <span x-text="isExercisesExpanded(workout.id) ? 'Свернуть' : '+' + ((workout.exercises || []).length - 3) + ' еще'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Мета информация -->
                    <div class="space-y-2 mb-4">
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Дата:</span>
                            <span x-text="formatDate(workout.date)"></span>
                        </div>
                        
                        <div class="text-sm text-gray-500" x-show="workout.time">
                            <span class="font-medium text-gray-700">Время:</span>
                            <span x-text="workout.time ? workout.time.substring(0, 5) : ''"></span>
                        </div>
                        
                        
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Участник:</span>
                            <span x-text="workout.athlete?.name || workout.trainer?.name || 'Неизвестно'"></span>
                        </div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="flex space-x-2">
                        <button @click="showView(workout.id)" 
                                class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            Просмотр
                        </button>
                        @if(auth()->user()->hasRole('trainer'))
                            <button @click="showEdit(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button @click="deleteWorkout(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Пагинация -->
        <div x-show="workouts.length > 0 && totalPages > 1" class="mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-center">
                    <!-- Навигация -->
                    <div class="flex items-center space-x-2">
                        <!-- Предыдущая страница -->
                        <button @click="previousPage()" 
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Номера страниц -->
                        <template x-for="page in visiblePages" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'"
                                    class="px-3 py-2 text-sm font-medium border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        
                        <!-- Следующая страница -->
                        <button @click="nextPage()" 
                                :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Пустое состояние -->
        <div x-show="filteredWorkouts.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">💪</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет тренировок</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">У вас пока нет запланированных тренировок. Создайте первую тренировку для начала работы.</p>
            @if(auth()->user()->hasRole('trainer'))
                <button @click="showCreate()" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Создать первую тренировку
                </button>
            @endif
        </div>
    </div>

    <!-- СОЗДАНИЕ/РЕДАКТИРОВАНИЕ ТРЕНИРОВКИ -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
                <span x-text="currentWorkout?.id ? 'Редактировать тренировку' : 'Создать тренировку'"></span>
            </h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Назад
            </button>
        </div>
        
        <form @submit.prevent="saveWorkout()" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Основная информация -->
                <div class="space-y-6">
                    <!-- Название и спортсмен в одном ряду на десктопе -->
                    <div class="workout-title-athlete-row">
                        <div class="workout-title-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Название тренировки *
                            </label>
                            <input type="text" 
                                   x-model="formTitle"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="Например: Силовая тренировка"
                                   required>
                        </div>

                        <div class="workout-athlete-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Спортсмен *
                            </label>
                            <select x-model="formAthleteId" 
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                    required>
                                <option value="">Выберите спортсмена</option>
                                @foreach($athletes ?? [] as $athlete)
                                    <option value="{{ $athlete->id }}">{{ $athlete->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Дата, время и продолжительность в одном ряду на десктопе -->
                    <div class="workout-date-duration-row">
                        <div class="workout-date-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Дата тренировки *
                            </label>
                            <input type="date" 
                                   x-model="formDate"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   required>
                        </div>

                        <div class="workout-time-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Время тренировки
                            </label>
                            <div class="relative">
                                <input type="time" 
                                       x-model="formTime"
                                       id="timeInput"
                                       step="3600"
                                       class="block w-full px-3 py-3 pr-12 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="workout-duration-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Продолжительность (минуты)
                            </label>
                            <input type="number" 
                                   x-model="formDuration"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="60"
                                   min="1">
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Описание тренировки
                        </label>
                        <textarea x-model="formDescription"
                                  rows="6"
                                  class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                  placeholder="Опишите план тренировки, упражнения, цели..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Статус
                        </label>
                        <select x-model="formStatus" 
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="planned">Запланирована</option>
                            <option value="completed">Завершена</option>
                            <option value="cancelled">Отменена</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Секция упражнений -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <div class="exercise-header-row">
                    <h3 style="font-size: 1.125rem; font-weight: 500; color: #111827; margin: 0;">Упражнения</h3>
                    <div class="exercise-buttons-container">
                        <button type="button" onclick="openExerciseModal()" 
                                style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить упражнение
                        </button>
                        <button type="button" onclick="openTemplateModal()" 
                                style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Добавить шаблон
                        </button>
                    </div>
                </div>
                
                <!-- Выбранные упражнения -->
                <div id="selectedExercisesContainer" class="space-y-3" style="display: none;">
                    <h4 class="text-sm font-medium text-gray-700">Выбранные упражнения:</h4>
                    <div id="selectedExercisesList" class="space-y-2">
                        <!-- Здесь будут отображаться выбранные упражнения -->
                    </div>
                </div>
                
                <!-- Пустое состояние -->
                <div id="emptyExercisesState" class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                    <p>Добавьте упражнения или выберите шаблон тренировки</p>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        @click="showList()" 
                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Отмена
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentWorkout?.id ? 'Обновить' : 'Создать'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- ПРОСМОТР ТРЕНИРОВКИ -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Просмотр тренировки</h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Назад
            </button>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <!-- Заголовок и статус -->
            <div class="workout-title-section">
                <h4 class="text-2xl font-bold text-gray-900" x-text="currentWorkout?.title"></h4>
                <span class="px-3 py-1 rounded-full text-sm font-semibold"
                      :class="{
                          'bg-green-100 text-green-800': currentWorkout?.status === 'completed',
                          'bg-red-100 text-red-800': currentWorkout?.status === 'cancelled',
                          'bg-blue-100 text-blue-800': currentWorkout?.status === 'planned'
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
            <div class="workout-details-grid">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Дата</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4" x-show="currentWorkout?.time">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Время</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.time ? currentWorkout.time.substring(0, 5) : ''"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4" x-show="currentWorkout?.duration">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Продолжительность</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.duration + ' мин'"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Участник</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.athlete?.name || currentWorkout?.trainer?.name || 'Неизвестно'"></p>
                </div>
            </div>
            
            <!-- Упражнения -->
            <div x-show="(currentWorkout?.exercises || []).length > 0" class="pt-6 border-t border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900 mb-4">Упражнения</h5>
                <div class="space-y-4">
                    <template x-for="(exercise, index) in (currentWorkout?.exercises || [])" :key="`view-exercise-${index}`">
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="exercise-header-section">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-indigo-600 font-medium" x-text="(index + 1) + '.'"></span>
                                    <span class="text-sm font-medium text-gray-900" x-text="exercise.name || 'Без названия'"></span>
                                    <span class="text-xs text-gray-500" x-text="exercise.category || ''"></span>
                                </div>
                                <!-- Ссылка на видео упражнения -->
                                <div x-show="exercise.video_url" class="exercise-video-link">
                                    <!-- Кнопка для модального окна -->
                                    <button @click="openSimpleModal(exercise.video_url, exercise.name)"
                                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full transition-colors cursor-pointer">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Видео
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Параметры упражнения -->
                            <div class="exercise-params-grid">
                                <!-- Подходы -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('sets')" 
                                     class="exercise-field bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-4">
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
                                     class="exercise-field bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
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
                                     class="exercise-field bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-orange-800">Вес (кг)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-orange-900" x-text="exercise.weight || exercise.pivot?.weight || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Отдых -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('rest')" 
                                     class="exercise-field bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-purple-800">Отдых (мин)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-purple-900" x-text="exercise.rest || exercise.pivot?.rest || 0"></div>
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
                                            <span class="text-sm font-semibold text-blue-800">Время (сек)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-blue-900" x-text="exercise.time || exercise.pivot?.time || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Дистанция -->
                                <div x-show="(exercise.fields_config || ['sets', 'reps', 'weight', 'rest']).includes('distance')" 
                                     class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800">Дистанция (м)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900" x-text="exercise.distance || exercise.pivot?.distance || 0"></div>
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
                                            <span class="text-sm font-semibold text-purple-800">Темп/Скорость</span>
                                        </div>
                                        <div class="text-2xl font-bold text-purple-900" x-text="exercise.tempo || exercise.pivot?.tempo || ''"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Примечания -->
                            <div x-show="exercise.notes || exercise.pivot?.notes" class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-700">Примечания</span>
                                </div>
                                <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3" x-text="exercise.notes || exercise.pivot?.notes"></div>
                            </div>
                            
                            <!-- Комментарий спортсмена -->
                            <div x-show="exercise.progress?.status === 'partial' && exercise.progress?.athlete_comment" class="mt-3 pt-3 border-t border-yellow-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-yellow-700">Комментарий спортсмена</span>
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">Частично выполнено</span>
                                </div>
                                <div class="text-sm text-gray-700 bg-yellow-50 rounded-lg p-3 border border-yellow-200" x-text="exercise.progress?.athlete_comment || ''"></div>
                            </div>
                            
                            
                            <!-- Статус выполнения упражнения (для тренера) -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="exercise-status-section mb-3">
                                    <span class="text-sm font-medium text-gray-700 mb-2 block">Статус выполнения:</span>
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
                                            <p class="text-xs text-yellow-700 mb-4">Укажите, что именно выполнил спортсмен в каждом подходе:</p>
                                        
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
                                                    
                                                    <div class="sets-fields-grid">
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
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Сообщение если нет упражнений -->
            <div x-show="(currentWorkout?.exercises || []).length === 0" class="pt-6 border-t border-gray-200">
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">Упражнения не добавлены</p>
                </div>
            </div>
            
            <!-- Действия -->
            @if(auth()->user()->hasRole('trainer'))
                <div class="flex space-x-2 pt-6 border-t border-gray-200">
                    <button @click="showEdit(currentWorkout?.id)" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        Редактировать
                    </button>
                    
                    <button @click="deleteWorkout(currentWorkout?.id)" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                        Удалить
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Красивое модальное окно для упражнений -->
<div id="exerciseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Выбор упражнений</h3>
            <button onclick="closeExerciseModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <!-- Поиск и фильтры -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                <!-- Поиск -->
                <input type="text" 
                       id="exercise-search" 
                       placeholder="Поиск упражнений..." 
                       style="flex: 1; min-width: 200px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;"
                       onkeyup="filterExercises()"
                       onfocus="this.style.borderColor = '#4f46e5'"
                       onblur="this.style.borderColor = '#d1d5db'">
                
                <!-- Фильтр категории -->
                <select id="category-filter" 
                        onchange="filterExercises()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все категории</option>
                    <option value="Грудь">Грудь</option>
                    <option value="Спина">Спина</option>
                    <option value="Ноги">Ноги</option>
                    <option value="Плечи">Плечи</option>
                    <option value="Руки">Руки</option>
                    <option value="Кардио">Кардио</option>
                    <option value="Гибкость">Гибкость</option>
                </select>
                
                <!-- Фильтр оборудования -->
                <select id="equipment-filter" 
                        onchange="filterExercises()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все оборудование</option>
                    <option value="Штанга">Штанга</option>
                    <option value="Гантели">Гантели</option>
                    <option value="Собственный вес">Собственный вес</option>
                    <option value="Тренажеры">Тренажеры</option>
                    <option value="Скакалка">Скакалка</option>
                    <option value="Турник">Турник</option>
                </select>
            </div>
            
            <div id="exercises-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                <p style="color: black;">Загрузка упражнений...</p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">Упражнения не найдены</h3>
                <p style="font-size: 14px;">Попробуйте изменить параметры поиска</p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeExerciseModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Отмена</button>
            <button onclick="addSelectedExercises()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;">Готово</button>
        </div>
    </div>
</div>

<!-- Красивое модальное окно для шаблонов -->
<div id="templateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Выбор шаблона тренировки</h3>
            <button onclick="closeTemplateModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <!-- Поиск и фильтры для шаблонов -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                <!-- Поиск -->
                <input type="text" 
                       id="template-search" 
                       placeholder="Поиск шаблонов..." 
                       style="flex: 1; min-width: 200px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;"
                       onkeyup="filterTemplates()"
                       onfocus="this.style.borderColor = '#4f46e5'"
                       onblur="this.style.borderColor = '#d1d5db'">
                
                <!-- Фильтр категории -->
                <select id="template-category-filter" 
                        onchange="filterTemplates()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все категории</option>
                    <option value="strength">Силовая</option>
                    <option value="cardio">Кардио</option>
                    <option value="flexibility">Гибкость</option>
                    <option value="mixed">Смешанная</option>
                </select>
                
                <!-- Фильтр сложности -->
                <select id="template-difficulty-filter" 
                        onchange="filterTemplates()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все уровни</option>
                    <option value="beginner">Начинающий</option>
                    <option value="intermediate">Средний</option>
                    <option value="advanced">Продвинутый</option>
                </select>
            </div>
            
            <div id="templates-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                <p style="color: black;">Загрузка шаблонов...</p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-templates-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">Шаблоны не найдены</h3>
                <p style="font-size: 14px;">Создайте шаблон тренировки для быстрого планирования</p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeTemplateModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Отмена</button>
            <button onclick="addSelectedTemplate()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;">Готово</button>
        </div>
    </div>
</div>

<script>
// Глобальные переменные для модальных окон
let exercises = [];
let templates = [];
let selectedTemplate = null;

// Функции для работы с модальными окнами
function openExerciseModal() {
    document.getElementById('exerciseModal').style.display = 'block';
    loadExercises();
    // Применяем фильтрацию после загрузки упражнений
    setTimeout(() => {
        filterExercises();
    }, 100);
}

function closeExerciseModal() {
    document.getElementById('exerciseModal').style.display = 'none';
}

function openTemplateModal() {
    document.getElementById('templateModal').style.display = 'block';
    loadTemplates();
}

function closeTemplateModal() {
    document.getElementById('templateModal').style.display = 'none';
}

// Загрузка упражнений
async function loadExercises() {
    try {
        const response = await fetch('/api/exercises');
        const data = await response.json();
        if (data.success) {
            exercises = data.exercises;
            renderExercises();
        }
    } catch (error) {
        console.error('Ошибка загрузки упражнений:', error);
    }
}

// Загружаем упражнения при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    loadExercises();
});

// Загрузка шаблонов
async function loadTemplates() {
    try {
        const response = await fetch('/api/workout-templates');
        const data = await response.json();
        if (data.success) {
            templates = data.templates;
            renderTemplates();
        }
    } catch (error) {
        console.error('Ошибка загрузки шаблонов:', error);
    }
}

// Отображение упражнений
function renderExercises() {
    const container = document.getElementById('exercises-container');
    if (exercises.length === 0) {
        container.innerHTML = '<p style="color: black;">Упражнения не найдены</p>';
        return;
    }
    
    container.innerHTML = exercises.map(exercise => `
        <div data-exercise-id="${exercise.id}" 
             data-exercise-name="${exercise.name}" 
             data-exercise-category="${exercise.category}" 
             data-exercise-equipment="${exercise.equipment}"
             style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s;" 
             onclick="toggleExercise(this, ${exercise.id}, '${exercise.name}', '${exercise.category}', '${exercise.equipment}')">
            <h4 style="font-weight: 500; color: #111827; margin-bottom: 8px;">${exercise.name}</h4>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">${exercise.category}</p>
            <p style="font-size: 14px; color: #9ca3af;">${exercise.equipment}</p>
        </div>
    `).join('');
    
    // Применяем фильтрацию после рендеринга
    filterExercises();
}

// Отображение шаблонов
function renderTemplates() {
    const container = document.getElementById('templates-container');
    if (templates.length === 0) {
        container.innerHTML = '<p style="color: black;">Шаблоны не найдены</p>';
        return;
    }
    
    container.innerHTML = templates.map(template => {
        const exerciseCount = (template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises.length : (template.exercises ? template.exercises.length : 0);
        const duration = template.estimated_duration ? `${template.estimated_duration} мин` : 'Не указано';
        const difficulty = template.difficulty_label || template.difficulty || 'Не указано';
        const category = template.category || '';
        
        return `
            <div class="template-item" 
                 data-template-id="${template.id}" 
                 data-template-category="${category}"
                 data-template-difficulty="${template.difficulty || ''}"
                 style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s; hover:border-blue-300;" 
                 onclick="toggleTemplate(this, ${template.id}, '${template.name}', ${JSON.stringify((template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises : (template.exercises || [])).replace(/"/g, '&quot;')})">
                <h4 style="font-weight: 500; color: #111827; margin-bottom: 8px;">${template.name}</h4>
                <p style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">${exerciseCount} упражнений • ${duration}</p>
                <p style="font-size: 12px; color: #9ca3af; margin-bottom: 8px;">Сложность: ${difficulty}</p>
                <p style="font-size: 14px; color: #9ca3af;">${template.description || ''}</p>
            </div>
        `;
    }).join('');
}

// Переключение упражнения
function toggleExercise(element, id, name, category, equipment) {
    const isSelected = element.style.backgroundColor === 'rgb(239, 246, 255)';
    
    if (isSelected) {
        element.style.backgroundColor = 'white';
        element.style.borderColor = '#e5e7eb';
    } else {
        element.style.backgroundColor = 'rgb(239, 246, 255)';
        element.style.borderColor = 'rgb(147, 197, 253)';
    }
    
    // Сохраняем данные в data-атрибуты
    element.dataset.selected = !isSelected;
    element.dataset.exerciseId = id;
    element.dataset.exerciseName = name;
    element.dataset.exerciseCategory = category;
    element.dataset.exerciseEquipment = equipment;
}

// Переключение шаблона
function toggleTemplate(element, id, name, exercises) {
    // Убираем выделение с других элементов
    document.querySelectorAll('.template-item').forEach(el => {
        el.style.backgroundColor = 'white';
        el.style.borderColor = '#e5e7eb';
    });
    
    // Выделяем текущий элемент
    element.style.backgroundColor = 'rgb(239, 246, 255)';
    element.style.borderColor = 'rgb(147, 197, 253)';
    
    // Сохраняем выбранный шаблон
    selectedTemplate = {
        id: id,
        name: name,
        exercises: exercises
    };
}

// Фильтрация упражнений
function filterExercises() {
    const searchTerm = document.getElementById('exercise-search').value.toLowerCase();
    const categoryFilter = document.getElementById('category-filter').value.toLowerCase();
    const equipmentFilter = document.getElementById('equipment-filter').value.toLowerCase();

    // Получаем уже выбранные упражнения
    const selectedExerciseIds = getSelectedExerciseIds();

    const exerciseElements = document.querySelectorAll('#exercises-container > div[data-exercise-id]');
    const noResults = document.getElementById('no-results');
    let visibleCount = 0;

    exerciseElements.forEach(element => {
        const exerciseId = parseInt(element.dataset.exerciseId);
        const name = element.querySelector('h4').textContent.toLowerCase();
        const category = element.querySelector('p').textContent.toLowerCase();
        const equipment = element.querySelectorAll('p')[1].textContent.toLowerCase();

        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);
        const matchesEquipment = !equipmentFilter || equipment.includes(equipmentFilter);
        const isNotSelected = !selectedExerciseIds.includes(exerciseId);


        if (matchesSearch && matchesCategory && matchesEquipment && isNotSelected) {
            element.style.display = 'block';
            visibleCount++;
        } else {
            element.style.display = 'none';
        }
    });

    // Показываем/скрываем сообщение о пустых результатах
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Фильтрация шаблонов
function filterTemplates() {
    const searchTerm = document.getElementById('template-search').value.toLowerCase();
    const categoryFilter = document.getElementById('template-category-filter').value.toLowerCase();
    const difficultyFilter = document.getElementById('template-difficulty-filter').value.toLowerCase();

    const templateElements = document.querySelectorAll('#templates-container > .template-item');
    const noResults = document.getElementById('no-templates-results');
    let visibleCount = 0;

    templateElements.forEach(element => {
        const name = element.querySelector('h4').textContent.toLowerCase();
        const description = element.querySelectorAll('p')[2] ? element.querySelectorAll('p')[2].textContent.toLowerCase() : '';
        const category = element.dataset.templateCategory ? element.dataset.templateCategory.toLowerCase() : '';
        const difficulty = element.dataset.templateDifficulty ? element.dataset.templateDifficulty.toLowerCase() : '';

        const matchesSearch = name.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);
        const matchesDifficulty = !difficultyFilter || difficulty.includes(difficultyFilter);

        if (matchesSearch && matchesCategory && matchesDifficulty) {
            element.style.display = 'block';
            visibleCount++;
        } else {
            element.style.display = 'none';
        }
    });

    // Показываем/скрываем сообщение о пустых результатах
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Добавление выбранных упражнений
function addSelectedExercises() {
    const selectedElements = document.querySelectorAll('#exerciseModal [data-selected="true"]');
    const newExercises = Array.from(selectedElements).map(el => {
        // Находим полные данные упражнения из загруженного массива
        const exerciseId = parseInt(el.dataset.exerciseId);
        const fullExercise = exercises.find(ex => ex.id === exerciseId);
        
        
        return {
            id: exerciseId,
            name: el.dataset.exerciseName,
            category: el.dataset.exerciseCategory,
            equipment: el.dataset.exerciseEquipment,
            fields_config: fullExercise ? fullExercise.fields_config : ['sets', 'reps', 'weight', 'rest']
        };
    });
    
    // Получаем текущие упражнения из формы
    const currentExercises = getCurrentExercisesFromForm();
    
    // Фильтруем новые упражнения, исключая дубликаты
    const existingIds = currentExercises.map(ex => ex.id);
    const uniqueNewExercises = newExercises.filter(ex => !existingIds.includes(ex.id));
    
    // Объединяем существующие и новые уникальные упражнения
    const allExercises = [...currentExercises, ...uniqueNewExercises];
    
    // Отображаем все упражнения в форме
    displaySelectedExercises(allExercises);
    
    closeExerciseModal();
}

// Получение текущих упражнений из формы
function getCurrentExercisesFromForm() {
    const exercises = [];
    const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    
    exerciseElements.forEach(element => {
        const exerciseId = element.dataset.exerciseId;
        const nameSpans = element.querySelectorAll('.font-medium');
        const exerciseName = nameSpans.length > 1 ? nameSpans[1].textContent : nameSpans[0].textContent;
        
        const exerciseData = {
            id: parseInt(exerciseId),
            name: exerciseName,
            category: '',
            equipment: '',
            fields_config: ['sets', 'reps', 'weight', 'rest']
        };
        
        // Собираем значения полей
        const inputs = element.querySelectorAll('input');
        inputs.forEach(input => {
            const name = input.name;
            if (name.startsWith('notes_')) {
                exerciseData.notes = input.value || '';
            } else {
                const fieldName = name.replace(`_${exerciseId}`, '');
                const value = input.type === 'number' ? 
                    (parseFloat(input.value) || 0) : 
                    (input.value || '');
                exerciseData[fieldName] = value;
            }
        });
        
        exercises.push(exerciseData);
    });
    
    return exercises;
}

// Получение ID уже выбранных упражнений
function getSelectedExerciseIds() {
    const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    return Array.from(exerciseElements).map(el => parseInt(el.dataset.exerciseId));
}

// Генерация HTML для полей на основе конфигурации
function generateFieldsHtml(exerciseId, fieldsConfig) {
    const fieldConfigs = {
        'sets': {
            label: 'Подходы',
            icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            color: 'indigo',
            type: 'number',
            min: '1',
            max: '20',
            value: '3'
        },
        'reps': {
            label: 'Повторения',
            icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            color: 'green',
            type: 'number',
            min: '1',
            max: '100',
            value: '10'
        },
        'weight': {
            label: 'Вес (кг)',
            icon: 'M13 10V3L4 14h7v7l9-11h-7z',
            color: 'orange',
            type: 'number',
            min: '0',
            max: '1000',
            step: '0.5',
            value: '0'
        },
        'rest': {
            label: 'Отдых (мин)',
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'purple',
            type: 'number',
            min: '0',
            max: '60',
            step: '0.5',
            value: '2'
        },
        'time': {
            label: 'Время (сек)',
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'blue',
            type: 'number',
            min: '0',
            max: '3600',
            step: '1',
            value: '0'
        },
        'distance': {
            label: 'Дистанция (м)',
            icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            color: 'green',
            type: 'number',
            min: '0',
            max: '10000',
            step: '1',
            value: '0'
        },
        'tempo': {
            label: 'Темп/Скорость',
            icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            color: 'purple',
            type: 'text',
            placeholder: '2-1-2 или 8 км/ч',
            value: ''
        }
    };

    let html = '';
    
    // Генерируем поля из конфигурации
    fieldsConfig.forEach(field => {
        if (fieldConfigs[field]) {
            const config = fieldConfigs[field];
            const colorClasses = getColorClasses(config.color);
            
            html += `
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-${config.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}"/>
                        </svg>
                        ${config.label}
                    </label>
                    <div class="relative">
                        <input type="${config.type}" 
                               name="${field}_${exerciseId}" 
                               ${config.min ? `min="${config.min}"` : ''}
                               ${config.max ? `max="${config.max}"` : ''}
                               ${config.step ? `step="${config.step}"` : ''}
                               ${config.placeholder ? `placeholder="${config.placeholder}"` : ''}
                               value="${config.value}"
                               class="w-full px-4 py-3 text-lg font-semibold text-center ${colorClasses.input} focus:ring-4 ${colorClasses.focusRing} focus:border-${config.color}-400 transition-all duration-200 hover:border-${config.color}-300 rounded-lg">
                    </div>
                </div>
            `;
        }
    });
    
    // Всегда добавляем примечания
    html += `
        <div class="relative">
            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Примечания
            </label>
            <div class="relative">
                <input type="text" 
                       name="notes_${exerciseId}" 
                       placeholder="Дополнительные заметки..."
                       class="w-full px-4 py-3 text-sm bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-200 rounded-lg focus:ring-4 focus:ring-gray-100 focus:border-gray-400 transition-all duration-200 hover:border-gray-300 placeholder-gray-500">
            </div>
        </div>
    `;
    
    return html;
}

// Получение CSS классов для цветов
function getColorClasses(color) {
    const colors = {
        'indigo': {
            input: 'bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200',
            focusRing: 'focus:ring-indigo-100'
        },
        'green': {
            input: 'bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200',
            focusRing: 'focus:ring-green-100'
        },
        'orange': {
            input: 'bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200',
            focusRing: 'focus:ring-orange-100'
        },
        'purple': {
            input: 'bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200',
            focusRing: 'focus:ring-purple-100'
        },
        'blue': {
            input: 'bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200',
            focusRing: 'focus:ring-blue-100'
        }
    };
    
    return colors[color] || colors['gray'];
}

// Отображение выбранных упражнений в форме
function displaySelectedExercises(exercises) {
    const container = document.getElementById('selectedExercisesContainer');
    const list = document.getElementById('selectedExercisesList');
    const emptyState = document.getElementById('emptyExercisesState');
    
    // Принудительно устанавливаем 4 колонки на больших экранах
    setTimeout(() => {
        const grids = document.querySelectorAll('.exercise-params-grid');
        grids.forEach(grid => {
            if (window.innerWidth >= 768) {
                grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
            }
        });
    }, 100);
    
    if (exercises.length > 0) {
        // Скрываем пустое состояние
        emptyState.style.display = 'none';
        
        // Показываем контейнер с упражнениями
        container.style.display = 'block';
        
        // Отображаем упражнения с динамическими полями
        list.innerHTML = exercises.map((exercise, index) => {
            const fieldsConfig = exercise.fields_config || ['sets', 'reps', 'weight', 'rest'];
            const fieldsHtml = generateFieldsHtml(exercise.id, fieldsConfig);
            
            return `
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm" data-exercise-id="${exercise.id}">
                    <!-- Заголовок упражнения -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3 flex-1 cursor-pointer" onclick="toggleExerciseDetails(${exercise.id})">
                            <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                            <span class="font-medium text-gray-900">${exercise.name}</span>
                            <span class="text-sm text-gray-600">(${exercise.category} • ${exercise.equipment})</span>
                            <svg id="chevron-${exercise.id}" class="w-4 h-4 transform transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <button type="button" onclick="removeExercise(${exercise.id})" class="text-red-600 hover:text-red-800 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Параметры упражнения - сворачиваемые -->
                    <div id="details-${exercise.id}" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                        <div class="exercise-params-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                            ${fieldsHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    } else {
        // Показываем пустое состояние
        emptyState.style.display = 'block';
        container.style.display = 'none';
    }
}

// Сворачивание/разворачивание деталей упражнения
function toggleExerciseDetails(exerciseId) {
    const detailsElement = document.getElementById(`details-${exerciseId}`);
    const chevronElement = document.getElementById(`chevron-${exerciseId}`);
    
    if (detailsElement.style.display === 'none') {
        // Разворачиваем
        detailsElement.style.display = 'block';
        chevronElement.style.transform = 'rotate(0deg)';
    } else {
        // Сворачиваем
        detailsElement.style.display = 'none';
        chevronElement.style.transform = 'rotate(180deg)';
    }
}

// Удаление упражнения из списка
function removeExercise(exerciseId) {
    // Находим элемент с упражнением и удаляем его
    const exerciseElement = document.querySelector(`[data-exercise-id="${exerciseId}"]`);
    if (exerciseElement) {
        exerciseElement.remove();
    }
    
    // Проверяем, остались ли упражнения
    const remainingElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    if (remainingElements.length === 0) {
        // Если упражнений не осталось, показываем пустое состояние
        document.getElementById('selectedExercisesContainer').style.display = 'none';
        document.getElementById('emptyExercisesState').style.display = 'block';
    }
}

// Добавление выбранного шаблона
function addSelectedTemplate() {
    if (selectedTemplate) {
        // Преобразуем упражнения из шаблона в формат, который ожидает displaySelectedExercises
        const templateExercises = selectedTemplate.exercises.map(exercise => {
            // Находим полные данные упражнения из загруженного массива
            const fullExercise = exercises.find(ex => ex.id === exercise.id);
            
            return {
                id: exercise.id,
                name: exercise.name,
                category: exercise.category,
                equipment: exercise.equipment,
                fields_config: fullExercise ? fullExercise.fields_config : ['sets', 'reps', 'weight', 'rest']
            };
        });
        
        // Отображаем упражнения из шаблона
        displaySelectedExercises(templateExercises);
    }
    closeTemplateModal();
}

// Простые функции для модальных окон
document.addEventListener('DOMContentLoaded', function() {
    // Закрытие по клику на фон
    document.getElementById('exerciseModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeExerciseModal();
        }
    });
    
    document.getElementById('templateModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTemplateModal();
        }
    });
    
});
</script>

<!-- Простое модальное окно для видео -->
<div x-show="videoModal.isOpen" 
     x-cloak
     style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center;">
    
    <!-- Фон для закрытия -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" @click="closeVideoModal()"></div>
    
    <!-- Модальное окно -->
    <div style="position: relative; background: white; border-radius: 12px; padding: 20px; max-width: 90%; max-height: 90%; overflow: hidden;">
        
        <!-- Заголовок -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: bold;" x-text="videoModal.title"></h3>
            <button @click="closeVideoModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        
        <!-- Контент -->
        <div>
            <div x-show="isYouTubeUrl(videoModal.url)" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                <iframe :src="getYouTubeEmbedUrl(videoModal.url)" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                        allowfullscreen>
                </iframe>
            </div>
            <div x-show="!isYouTubeUrl(videoModal.url)" style="text-align: center;">
                <a :href="videoModal.url" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none;">
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                    </svg>
                    Открыть видео
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

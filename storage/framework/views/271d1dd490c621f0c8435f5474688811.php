<?php $__env->startSection("title", __('common.workouts')); ?>
<?php $__env->startSection("page-title", __('common.workouts')); ?>

<script src="<?php echo e(asset('js/notifications.js')); ?>"></script>
<script src="<?php echo e(asset('js/workout-drag-drop.js')); ?>"></script>

<!-- Drag and Drop функциональность для упражнений -->
<script>
// Функции drag and drop уже доступны глобально из workout-drag-drop.js

// Добавляем CSS стили для drag and drop (точно как в self-athlete)
const style = document.createElement('style');
style.textContent = `
    .drop-target {
        border: 2px dashed #3b82f6;
        background-color: #eff6ff;
    }
    
    div[data-exercise-id].drag-over {
        border-color: #4f46e5 !important;
        background-color: #f0f9ff !important;
        box-shadow: 0 0 0 2px #e0e7ff !important;
    }
    
    [draggable="true"] {
        cursor: grab;
        transition: all 0.2s ease;
    }
    
    [draggable="true"]:active {
        cursor: grabbing;
    }
    
    .drop-target {
        border-style: dashed;
    }
    
    .dragging {
        opacity: 0.5 !important;
        transform: scale(0.95) !important;
        transition: all 0.2s ease !important;
    }
`;
document.head.appendChild(style);

// function handleDragStart(event, exerciseId, exerciseIndex) {
}

// function handleDragOver(event, targetExerciseId, targetIndex) {
}

// function handleDragEnter(event, targetExerciseId, targetIndex) {
}

// function handleDragLeave(event, targetExerciseId, targetIndex) {
}

// function handleDrop(event, targetExerciseId, targetIndex) {
    event.preventDefault();
    event.stopPropagation();
    
    // Приводим к числу для корректного сравнения
    const draggedId = parseInt(draggedExerciseId);
    const targetId = parseInt(targetExerciseId);
    
    if (!draggedId || draggedId === targetId) {
        cleanupDragState();
        return;
    }
    
    // Получаем текущий список упражнений
    let exercises = [];
    
    // Проверяем, находимся ли мы в режиме редактирования
    const appElement = document.querySelector('[x-data*="workoutApp"]');
    if (appElement) {
        const workoutApp = Alpine.$data(appElement);
        if (workoutApp && workoutApp.currentWorkout && workoutApp.currentWorkout.exercises) {
            // Режим редактирования - используем данные из Alpine.js
            exercises = [...workoutApp.currentWorkout.exercises];
        }
    }
    
    // Если упражнения не найдены в Alpine.js, получаем их из DOM (режим создания)
    if (exercises.length === 0) {
        exercises = getCurrentExercisesFromForm();
    }
    
    if (exercises.length === 0) {
        cleanupDragState();
        return;
    }
    
    // Находим индексы упражнений - проверяем разные возможные поля
    const draggedIndex = exercises.findIndex(ex => 
        ex.id == draggedId || ex.exercise_id == draggedId || ex.id == parseInt(draggedId) || ex.exercise_id == parseInt(draggedId)
    );
    const targetIndexNum = exercises.findIndex(ex => 
        ex.id == targetId || ex.exercise_id == targetId || ex.id == parseInt(targetId) || ex.exercise_id == parseInt(targetId)
    );
    
    if (draggedIndex === -1 || targetIndexNum === -1) {
        cleanupDragState();
        return;
    }
    
    // Проверяем, что упражнения действительно разные
    if (draggedIndex === targetIndexNum) {
        cleanupDragState();
        return;
    }
    
    // Сначала собираем текущие значения полей из DOM
    let currentFieldValues = [];
    
    if (appElement) {
        const workoutApp = Alpine.$data(appElement);
        if (workoutApp && workoutApp.collectExerciseData) {
            // Режим редактирования - используем метод Alpine.js
            currentFieldValues = workoutApp.collectExerciseData();
        }
    }
    
    // Если не получилось собрать данные через Alpine.js, собираем через глобальную функцию
    if (currentFieldValues.length === 0) {
        currentFieldValues = collectExerciseDataFromDOM();
    }
    
    // Обновляем упражнения с текущими значениями полей
    exercises.forEach(exercise => {
        const fieldData = currentFieldValues.find(f => 
            f.exercise_id == exercise.id || f.exercise_id == exercise.exercise_id ||
            f.id == exercise.id || f.id == exercise.exercise_id
        );
        if (fieldData) {
            // Обновляем значения полей из DOM
            exercise.sets = fieldData.sets || exercise.sets;
            exercise.reps = fieldData.reps || exercise.reps;
            exercise.weight = fieldData.weight || exercise.weight;
            exercise.rest = fieldData.rest || exercise.rest;
            exercise.time = fieldData.time || exercise.time;
            exercise.distance = fieldData.distance || exercise.distance;
            exercise.tempo = fieldData.tempo || exercise.tempo;
            exercise.notes = fieldData.notes || exercise.notes;
        }
    });
    
    // Перемещаем упражнение
    const [draggedExercise] = exercises.splice(draggedIndex, 1);
    
    // Вычисляем правильную позицию для вставки
    let insertIndex = targetIndexNum;
    
    exercises.splice(insertIndex, 0, draggedExercise);
    
    // Обновляем данные в зависимости от режима
    if (appElement) {
        const workoutApp = Alpine.$data(appElement);
        if (workoutApp && workoutApp.currentWorkout && workoutApp.currentWorkout.exercises) {
            // Режим редактирования - обновляем данные в Alpine.js
            workoutApp.currentWorkout.exercises = exercises;
            workoutApp.displaySelectedExercises(exercises);
        } else {
            // Режим создания - перерисовываем через глобальную функцию
            displaySelectedExercises(exercises);
        }
    } else {
        // Режим создания - перерисовываем через глобальную функцию
        displaySelectedExercises(exercises);
    }
    
    // Показываем уведомление об успешном изменении порядка
    showSuccess('<?php echo e(__('common.success')); ?>!', '<?php echo e(__('common.exercise_order_changed')); ?>');
    
    cleanupDragState();
}

// function cleanupDragState() {
    // Удаляем все классы drag-over
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    document.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
    
    // Восстанавливаем opacity
    document.querySelectorAll('[data-exercise-id]').forEach(el => {
        el.style.opacity = '1';
    });
    
    // Сбрасываем переменные
    draggedExerciseId = null;
    draggedExerciseIndex = null;
}

// Добавляем CSS стили для drag and drop
const style = document.createElement('style');
style.textContent = `
    .drop-target {
        border: 2px dashed #3b82f6;
        background-color: #eff6ff;
    }
    
    
    [draggable="true"] {
        cursor: grab;
    }
    
    [draggable="true"]:active {
        cursor: grabbing;
    }
    
    .dragging {
        opacity: 0.5 !important;
        transform: scale(0.95) !important;
        transition: all 0.2s ease !important;
    }
    
    .drag-over {
        border-color: #4f46e5 !important;
        background-color: #f0f9ff !important;
        box-shadow: 0 0 0 2px #e0e7ff !important;
    }
`;
document.head.appendChild(style);
</script>

<script>
// SPA функциональность для тренировок
function workoutApp() {
    return {
        currentView: 'list', // list, create, edit, view
        workouts: <?php echo json_encode($workouts->items(), 15, 512) ?>,
        totalWorkouts: <?php echo e($workouts->total()); ?>,
        
        currentPage: <?php echo e($workouts->currentPage()); ?>,
        lastPage: <?php echo e($workouts->lastPage()); ?>,
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
        workoutProgress: {}, // Прогресс для каждой тренировки (как у спортсмена)
        lastChangedExercise: null, // Последнее измененное упражнение
        exercisesExpanded: {}, // Хранение состояния развернутости упражнений в карточках
        
        // Модальное окно для видео
        videoModal: {
            isOpen: false,
            url: '',
            title: ''
        },
        
        // Модальное окно для деталей упражнения
        exerciseDetailModal: {
            isOpen: false,
            exercise: null
        },

        // Навигация
        showList() {
            // Обновляем данные в списке перед возвратом
            if (this.currentWorkout && Object.keys(this.exerciseStatuses).length > 0) {
                this.updateWorkoutProgressInList();
            }
            
            this.currentView = 'list';
            this.currentWorkout = null;
            
            // Очищаем форму упражнений при возврате к списку
            const exercisesList = document.getElementById('selectedExercisesList');
            if (exercisesList) {
                exercisesList.innerHTML = '';
            }
            document.getElementById('selectedExercisesContainer').style.display = 'none';
            document.getElementById('emptyExercisesState').style.display = 'block';
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
            // Очищаем список выбранных упражнений
            const exercisesList = document.getElementById('selectedExercisesList');
            if (exercisesList) {
                exercisesList.innerHTML = '';
            }
            
            // Автоматически добавляем упражнение "Разминка" при создании тренировки
            const warmupExercise = exercises.find(ex => ex.name === 'Разминка');
            if (warmupExercise) {
                const warmupData = {
                    id: warmupExercise.id,
                    name: warmupExercise.name,
                    category: warmupExercise.category || '',
                    equipment: warmupExercise.equipment || '',
                    fields_config: warmupExercise.fields_config || ['time']
                };
                this.displaySelectedExercises([warmupData], false);
            }
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
                const formattedExercises = exercises.map(exercise => {
                    // Функция для безопасного получения значения поля
                    function safeValue(value, defaultValue = '') {
                        if (value === null || value === undefined || value === 'null' || value === '') {
                            return defaultValue;
                        }
                        return value;
                    }
                    
                    return {
                    id: exercise.exercise_id, // Используем exercise_id вместо id!
                    name: exercise.name,
                        sets: safeValue(exercise.sets || exercise.pivot?.sets, 3),
                        reps: safeValue(exercise.reps || exercise.pivot?.reps, 12),
                        weight: safeValue(exercise.weight || exercise.pivot?.weight, 0),
                        rest: safeValue(exercise.rest || exercise.pivot?.rest, 60),
                        time: safeValue(exercise.time || exercise.pivot?.time, 0),
                        distance: safeValue(exercise.distance || exercise.pivot?.distance, 0),
                        tempo: safeValue(exercise.tempo || exercise.pivot?.tempo, ''),
                        notes: safeValue(exercise.notes || exercise.pivot?.notes, ''),
                    category: exercise.category || '',
                    fields_config: exercise.fields_config
                    };
                });
                
                this.displaySelectedExercises(formattedExercises, false); // false = режим редактирования, свернуто
                
                // Привязываем события drag and drop к упражнениям
                setTimeout(() => {
                    bindDragDropEvents();
                }, 100);
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
            // Загружаем данные подходов из workoutProgress
            this.loadSetsDataFromProgress(workoutId);
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
            // Проверяем текущий статус упражнения
            const currentStatus = this.exerciseStatuses[exerciseId] || 
                                (this.currentWorkout && this.workoutProgress[this.currentWorkout.id] && 
                                 this.workoutProgress[this.currentWorkout.id][exerciseId]?.status) || 
                                null;
            
            // Если статус тот же самый, отменяем его (возвращаем к null)
            if (currentStatus === status) {
                // Очищаем статус
                delete this.exerciseStatuses[exerciseId];
                this.lastChangedExercise = { id: exerciseId, status: null };
                
                // Очищаем workoutProgress
                if (this.currentWorkout && this.currentWorkout.id && this.workoutProgress[this.currentWorkout.id]) {
                    delete this.workoutProgress[this.currentWorkout.id][exerciseId];
                }
                
                // Очищаем связанные данные
                delete this.exerciseComments[exerciseId];
                delete this.exerciseSetsData[exerciseId];
                delete this.exerciseSetsExpanded[exerciseId];
                
                // Обновляем список и автосохранение
                this.updateWorkoutProgressInList();
                this.autoSave();
                
                showSuccess('<?php echo e(__('common.progress_saved')); ?>', 'Статус упражнения отменен');
                return;
            }
            
            // Устанавливаем новый статус
            this.exerciseStatuses[exerciseId] = status;
            this.lastChangedExercise = { id: exerciseId, status: status };
            
            // Обновляем workoutProgress для корректной работы в режиме просмотра
            if (this.currentWorkout && this.currentWorkout.id) {
                if (!this.workoutProgress[this.currentWorkout.id]) {
                    this.workoutProgress[this.currentWorkout.id] = {};
                }
                this.workoutProgress[this.currentWorkout.id][exerciseId] = {
                    status: status,
                    athlete_comment: this.workoutProgress[this.currentWorkout.id][exerciseId]?.athlete_comment || null,
                    sets_data: this.workoutProgress[this.currentWorkout.id][exerciseId]?.sets_data || null,
                    completed_at: this.workoutProgress[this.currentWorkout.id][exerciseId]?.completed_at || null
                };
            }
            
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
                const defaultRest = exercise?.rest || exercise?.pivot?.rest || 1.0; // По умолчанию 1 <?php echo e(__('common.min')); ?>

                
                for (let i = 0; i < totalSets; i++) {
                    this.exerciseSetsData[exerciseId].push({
                        set_number: i + 1,
                        reps: '',
                        weight: '',
                        rest: defaultRest // Автоматически заполняем отдых в <?php echo e(__('common.minutes')); ?>

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
        
        // Проверка, заполнено ли поле упражнения
        isFieldFilled(exercise, fieldName) {
            const value = exercise[fieldName] || exercise.pivot?.[fieldName];
            return value !== null && value !== undefined && value !== '' && value !== 0;
        },
        
        // Форматирование числа без лишних нулей
        formatNumber(value) {
            if (!value || value === 0) return '0';
            const num = parseFloat(value);
            // Убираем незначащие нули после запятой
            return num % 1 === 0 ? num.toString() : num.toString().replace(/\.?0+$/, '');
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
                let exercises = Object.keys(this.exerciseStatuses).map(exerciseId => ({
                    exercise_id: parseInt(exerciseId),
                    status: this.exerciseStatuses[exerciseId],
                    athlete_comment: this.exerciseComments[exerciseId] || null,
                    sets_data: this.exerciseSetsData[exerciseId] || null
                }));
                
                // Если нет упражнений в exerciseStatuses, но есть данные в exerciseSetsData, 
                // значит пользователь редактирует поля в частично выполненных упражнениях
                if (exercises.length === 0 && Object.keys(this.exerciseSetsData).length > 0) {
                    exercises = Object.keys(this.exerciseSetsData).map(exerciseId => ({
                        exercise_id: parseInt(exerciseId),
                        status: 'partial', // Устанавливаем статус "частично"
                        athlete_comment: this.exerciseComments[exerciseId] || null,
                        sets_data: this.exerciseSetsData[exerciseId] || null
                    }));
                }
                
                // Если все еще нет упражнений для сохранения, не отправляем запрос
                if (exercises.length === 0) {
                    return;
                }

                // Показываем индикатор загрузки
                showInfo('<?php echo e(__('common.saving')); ?>', '<?php echo e(__('common.saving_progress')); ?>', 2000);

                const requestData = {
                    workout_id: this.currentWorkout.id,
                    exercises: exercises
                };

                const response = await fetch('/trainer/exercise-progress', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                const text = await response.text();
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    throw new Error('<?php echo e(__('common.received_html_instead_json')); ?>');
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    throw new Error('<?php echo e(__('common.invalid_server_response')); ?>');
                }
                
                if (result.success) {
                    // Записываем время последнего сохранения
                    this.lastSaved = new Date();
                    
                    // Обновляем workoutProgress для корректного отображения в списке
                    if (this.currentWorkout && this.currentWorkout.id) {
                        if (!this.workoutProgress[this.currentWorkout.id]) {
                            this.workoutProgress[this.currentWorkout.id] = {};
                        }
                        
                        // Обновляем данные для всех сохраненных упражнений
                        exercises.forEach(exercise => {
                            this.workoutProgress[this.currentWorkout.id][exercise.exercise_id] = {
                                status: exercise.status,
                                athlete_comment: exercise.athlete_comment,
                                sets_data: exercise.sets_data,
                                completed_at: new Date().toISOString()
                            };
                        });
                    }
                    
                    // Показываем уведомление только для последнего измененного упражнения
                    let title = '';
                    let message = '';
                    
                    if (this.lastChangedExercise) {
                        const { status, id } = this.lastChangedExercise;
                        
                        if (status === 'completed') {
                            title = '<?php echo e(__('common.progress_saved')); ?>';
                            message = '<?php echo e(__('common.exercise_completed')); ?>';
                        } else if (status === 'partial') {
                            // Проверяем, есть ли данные по подходам
                            const hasSetsData = this.exerciseSetsData[id] && 
                                               this.exerciseSetsData[id].some(set => 
                                                   set.reps || set.weight || set.rest
                                               );
                            
                            if (hasSetsData) {
                                title = '<?php echo e(__('common.progress_saved')); ?>';
                                message = '<?php echo e(__('common.exercise_saved_with_details')); ?>';
                            } else {
                                title = '<?php echo e(__('common.status_updated')); ?>';
                                message = '<?php echo e(__('common.exercise_partially_completed')); ?>';
                            }
                        } else if (status === 'not_done') {
                            title = '<?php echo e(__('common.status_updated')); ?>';
                            message = '<?php echo e(__('common.exercise_not_completed')); ?>';
                        } else if (status === null) {
                            // Если статус был отменен
                            title = '<?php echo e(__('common.progress_saved')); ?>';
                            message = 'Статус упражнения отменен';
                        }
                        
                        // Сбрасываем последнее измененное упражнение
                        this.lastChangedExercise = null;
                    } else {
                        // Fallback для случая, если lastChangedExercise не установлен
                        title = '<?php echo e(__('common.status_updated')); ?>';
                        message = `Обновлено ${exercises.length} упражнений`;
                    }
                    
                    showSuccess(title, message);
                    
                    // Обновляем данные в списке тренировок
                    this.updateWorkoutProgressInList();
                } else {
                    showError('<?php echo e(__('common.saving_error')); ?>', result.message || '<?php echo e(__('common.failed_to_save_progress')); ?>');
                }
            } catch (error) {
                console.error('<?php echo e(__('common.update_error')); ?>:', error);
                showError('<?php echo e(__('common.connection_error')); ?>', '<?php echo e(__('common.check_internet_connection')); ?>');
            }
        },
        
        // Загрузка сохраненного прогресса с сервера
        async loadExerciseProgress(workoutId) {
            try {
                const response = await fetch(`/trainer/exercise-progress?workout_id=${workoutId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    
                    if (result.success && result.progress) {
                        // Инициализируем workoutProgress для этой тренировки
                        if (!this.workoutProgress[workoutId]) {
                            this.workoutProgress[workoutId] = {};
                        }
                        
                        // Загружаем прогресс для каждого упражнения
                        Object.keys(result.progress).forEach(exerciseId => {
                            const progressData = result.progress[exerciseId];
                            
                            // Сохраняем в exerciseStatuses для текущего просмотра
                            if (progressData.status) {
                                this.exerciseStatuses[exerciseId] = progressData.status;
                            }
                            
                            if (progressData.athlete_comment) {
                                this.exerciseComments[exerciseId] = progressData.athlete_comment;
                            }
                            
                            if (progressData.sets_data) {
                                this.exerciseSetsData[exerciseId] = progressData.sets_data;
                                this.exerciseSetsExpanded[exerciseId] = false;
                            }
                            
                            // Сохраняем в workoutProgress для отображения в списке
                            this.workoutProgress[workoutId][exerciseId] = progressData;
                        });
                    }
                }
            } catch (error) {
                console.error('Ошибка загрузки прогресса:', error);
            }
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
                'completed': '<?php echo e(__('common.completed')); ?>',
                'cancelled': '<?php echo e(__('common.cancelled')); ?>',
                'planned': '<?php echo e(__('common.planned')); ?>'
            };
            return labels[status] || status;
        },
        // Обновление статуса тренировки
        async updateWorkoutStatus(workoutId, newStatus) {
            try {
                // Проверяем текущий статус тренировки
                const currentWorkout = this.currentWorkout && this.currentWorkout.id === workoutId ? this.currentWorkout : 
                                     this.workouts.find(w => w.id === workoutId);
                
                if (!currentWorkout) {
                    showError('<?php echo e(__('common.error')); ?>', 'Тренировка не найдена');
                    return;
                }
                
                const oldStatus = currentWorkout.status;
                
                // Если статус не изменился, не отправляем запрос
                if (oldStatus === newStatus) {
                    // Убираем подсветку (возвращаем к исходному статусу)
                    if (this.currentWorkout && this.currentWorkout.id === workoutId) {
                        this.currentWorkout.status = 'planned'; // Возвращаем к планированной
                        this.formStatus = 'planned'; // Обновляем также формы
                    }
                    
                    const workoutInList = this.workouts.find(w => w.id === workoutId);
                    if (workoutInList) {
                        workoutInList.status = 'planned'; // Возвращаем к планированной
                    }
                    
                    showSuccess('<?php echo e(__('common.status_updated')); ?>', 'Статус отменен');
                    return;
                }
                
                const response = await fetch(`/workouts/${workoutId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                });

                // Проверяем, что ответ успешный
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Проверяем, что ответ JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('<?php echo e(__('common.not_json_response')); ?>:', text);
                    throw new Error('<?php echo e(__('common.server_returned_not_json')); ?>');
                }

                const result = await response.json();
                
                if (result.success) {
                    // Обновляем статус в текущей тренировке
                    if (this.currentWorkout && this.currentWorkout.id === workoutId) {
                        this.currentWorkout.status = newStatus;
                        // Обновляем также статус в форме, чтобы при сохранении не перезаписывался
                        this.formStatus = newStatus;
                    }
                    
                    // Обновляем статус в списке тренировок
                    const workoutInList = this.workouts.find(w => w.id === workoutId);
                    if (workoutInList) {
                        workoutInList.status = newStatus;
                    }
                    
                    // Показываем уведомление
                    const statusLabels = {
                        'planned': '<?php echo e(__('common.planned')); ?>',
                        'completed': '<?php echo e(__('common.completed')); ?>',
                        'cancelled': '<?php echo e(__('common.cancelled')); ?>'
                    };
                    
                    showSuccess('<?php echo e(__('common.status_updated')); ?>', `<?php echo e(__('common.workout')); ?>: ${statusLabels[newStatus]}`);
                } else {
                    showError('<?php echo e(__('common.error')); ?>', result.message || '<?php echo e(__('common.failed_to_update_workout_status')); ?>');
                }
            } catch (error) {
                console.error('<?php echo e(__('common.status_update_error')); ?>:', error);
                showError('<?php echo e(__('common.connection_error')); ?>', `<?php echo e(__('common.error')); ?>: ${error.message}`);
            }
        },
        
        // Сбор данных упражнений
        collectExerciseData() {
            const exercises = [];
            
            // ВСЕГДА используем DOM для сбора данных (более надежно)
            // Режим создания - используем порядок из DOM
            const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
            
            exerciseElements.forEach((element, index) => {
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
            
            // Если упражнения не найдены в DOM, но это режим редактирования с существующими упражнениями
            if (exercises.length === 0 && this.currentWorkout && this.currentWorkout.exercises && this.currentWorkout.exercises.length > 0) {
                console.warn('Упражнения не найдены в DOM, используем данные из currentWorkout');
                // Возвращаем упражнения из currentWorkout в нужном формате
                return this.currentWorkout.exercises.map(exercise => ({
                    exercise_id: exercise.exercise_id || exercise.id,
                    name: exercise.name,
                    sets: exercise.pivot?.sets || exercise.sets || 3,
                    reps: exercise.pivot?.reps || exercise.reps || 12,
                    weight: exercise.pivot?.weight || exercise.weight || 0,
                    rest: exercise.pivot?.rest || exercise.rest || 60,
                    time: exercise.pivot?.time || exercise.time || 0,
                    distance: exercise.pivot?.distance || exercise.distance || 0,
                    tempo: exercise.pivot?.tempo || exercise.tempo || '',
                    notes: exercise.pivot?.notes || exercise.notes || ''
                }));
            }
            
            return exercises;
        },
        
        // Сохранение
        async saveWorkout() {
            try {
                // Собираем данные упражнений
                const exercises = this.collectExerciseData();
                
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
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentWorkout && this.currentWorkout.id ? '<?php echo e(__('common.workout_updated')); ?>' : '<?php echo e(__('common.workout_created')); ?>',
                            message: this.currentWorkout && this.currentWorkout.id ? 
                                '<?php echo e(__('common.workout_successfully_updated')); ?>' : 
                                '<?php echo e(__('common.workout_successfully_created')); ?>'
                        }
                    }));
                    
                    // Обновляем список тренировок
                    if (this.currentWorkout && this.currentWorkout.id) {
                        // Редактирование - обновляем существующую
                        const index = this.workouts.findIndex(w => w.id === this.currentWorkout.id);
                        if (index !== -1) {
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
                                    fields_config: originalExercise?.fields_config,
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
                            
                            // Обновляем currentWorkout если мы сейчас просматриваем эту тренировку
                            if (this.currentWorkout && this.currentWorkout.id === this.workouts[index].id) {
                                this.currentWorkout = this.workouts[index];
                            }
                            
                            // Загружаем прогресс для всех тренировок
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
                            title: '<?php echo e(__('common.saving_error')); ?>',
                            message: result.message || '<?php echo e(__('common.error_occurred')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('<?php echo e(__('common.error')); ?>:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.error_occurred')); ?>'
                    }
                }));
            }
        },
        
        // Удаление
        deleteWorkout(id) {
            const workout = this.workouts.find(w => w.id === id);
            const workoutTitle = workout ? workout.title : '<?php echo e(__('common.workout')); ?>';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete_workout')); ?>',
                    message: `<?php echo e(__('common.are_you_sure_delete_workout')); ?> "${workoutTitle}"?`,
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
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
                            title: '<?php echo e(__('common.workout_deleted')); ?>',
                            message: '<?php echo e(__('common.workout_successfully_deleted')); ?>'
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
                            title: '<?php echo e(__('common.error_deleting')); ?>',
                            message: result.message || '<?php echo e(__('common.error_deleting_workout')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('<?php echo e(__('common.error')); ?>:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.error_deleting_workout')); ?>'
                    }
                }));
            }
        },
        
        // Загрузка прогресса для всех тренировок
        async loadAllExerciseProgress() {
            try {
                for (let workout of this.workouts) {
                    if (workout.exercises) {
                        for (let exercise of workout.exercises) {
                            const response = await fetch(`/trainer/exercise-progress?workout_id=${workout.id}`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            
                            if (response.ok) {
                                const progressData = await response.json();
                                const exerciseId = exercise.exercise_id || exercise.id;
                                const progress = progressData.find(p => p.exercise_id === exerciseId);
                                
                                if (progress) {
                                    exercise.progress = {
                                        status: progress.status,
                                        athlete_comment: progress.athlete_comment,
                                        completed_at: progress.completed_at
                                    };
                                } else {
                                    exercise.progress = {
                                        status: null,
                                        athlete_comment: null,
                                        completed_at: null
                                    };
                                }
                            } else {
                            }
                        }
                    }
                }
            } catch (error) {
            }
        },
        
        
        // Отображение выбранных упражнений в форме
        displaySelectedExercises(exercises, isViewMode = false) {
            const container = document.getElementById('selectedExercisesContainer');
            const list = document.getElementById('selectedExercisesList');
            const emptyState = document.getElementById('emptyExercisesState');
            
            if (!container || !list || !emptyState) {
                console.error('<?php echo e(__('common.elements_not_found_for_exercises')); ?>');
                return;
            }
            
            if (exercises.length > 0) {
                // Скрываем пустое состояние
                emptyState.style.display = 'none';
                
                // Показываем контейнер с упражнениями
                container.style.display = 'block';
                
                // Отображаем упражнения с динамическими полями и drag and drop
                list.innerHTML = exercises.map((exercise, index) => {
                    const fieldsConfig = exercise.fields_config;
                    const exerciseId = exercise.exercise_id || exercise.id;
                    const fieldsHtml = this.generateFieldsHtml(exerciseId, fieldsConfig, exercise);
                    
                    return `
                        <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md" 
                             data-exercise-id="${exerciseId}" 
                             data-exercise-index="${index}">
                            <!-- Заголовок упражнения -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3 flex-1" title="<?php echo e(__('common.drag_to_reorder')); ?>">
                                    <!-- Drag Handle -->
                                    <div class="text-gray-400 hover:text-gray-600 cursor-move" 
                                         draggable="true" 
                                         data-exercise-id="${exerciseId}" 
                                         data-exercise-index="${index}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>
                                    <div class="cursor-pointer flex-1" onclick="toggleExerciseDetails(${exercise.id})" onmousedown="event.stopPropagation()">
                                        <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                                        <span class="text-sm font-medium text-gray-900">${exercise.name}</span>
                                        <span class="text-xs text-gray-500">${exercise.category || ''}</span>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div onclick="toggleExerciseDetails(${exercise.id})" 
                                         onmousedown="event.stopPropagation()" 
                                         class="cursor-pointer">
                                        <svg id="chevron-${exercise.id}" class="w-4 h-4 text-gray-400 transition-transform" style="transform: ${isViewMode ? 'rotate(0deg)' : 'rotate(-90deg)'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                    <button onclick="removeExercise(${exercise.id})" 
                                            onmousedown="event.stopPropagation()" 
                                            class="text-red-500 hover:text-red-700 text-sm">
                                        <?php echo e(__('common.delete')); ?>

                                    </button>
                                </div>
                            </div>
                            
                            <!-- Параметры упражнения - сворачиваемые -->
                            <div id="details-${exercise.id}" class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm" style="display: ${isViewMode ? 'block' : 'none'}">
                                <div class="exercise-params-grid grid grid-cols-4 gap-4">
                                    ${fieldsHtml}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                // Глобальная замена всех "null" значений в HTML
                const cleanedHtml = list.innerHTML.replace(/value="null"/g, 'value=""').replace(/value='null'/g, 'value=""');
                list.innerHTML = cleanedHtml;
                
                // Привязываем события drag and drop через общий файл
                setTimeout(() => {
                    bindDragDropEvents();
                }, 0);
            } else {
                // Показываем пустое состояние
                emptyState.style.display = 'block';
                container.style.display = 'none';
            }
        },
        // Генерация HTML для полей упражнения (точная копия оригинальной функции)
        generateFieldsHtml(exerciseId, fieldsConfig, exerciseData = null) {
            const fieldConfigs = {
                'weight': {
                    label: '<?php echo e(__('common.weight_kg')); ?>',
                    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
                    color: 'orange',
                    type: 'number',
                    min: '0',
                    max: '1000',
                    step: '0.5',
                    value: '0'
                },
                'reps': {
                    label: '<?php echo e(__('common.repetitions')); ?>',
                    icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                    color: 'green',
                    type: 'number',
                    min: '1',
                    max: '100',
                    value: '10'
                },
                'sets': {
                    label: '<?php echo e(__('common.sets')); ?>',
                    icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    color: 'indigo',
                    type: 'number',
                    min: '1',
                    max: '20',
                    value: '3'
                },
                'rest': {
                    label: '<?php echo e(__('common.rest')); ?> (<?php echo e(__('common.min')); ?>)',
                    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'purple',
                    type: 'number',
                    min: '0',
                    max: '60',
                    step: '0.5',
                    value: '2'
                },
                'time': {
                    label: '<?php echo e(__('common.time_sec')); ?>',
                    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'blue',
                    type: 'number',
                    min: '0',
                    max: '3600',
                    step: '1',
                    value: '0'
                },
                'distance': {
                    label: '<?php echo e(__('common.distance_m')); ?>',
                    icon: 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                    color: 'green',
                    type: 'number',
                    min: '0',
                    max: '10000',
                    step: '1',
                    value: '0'
                },
                'tempo': {
                    label: '<?php echo e(__('common.pace_speed')); ?>',
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
                    
                    // Форматируем значение - убираем лишние нули после запятой
                    if (config.type === 'number' && value) {
                        const num = parseFloat(value);
                        value = num % 1 === 0 ? num.toString() : num.toString().replace(/\.?0+$/, '');
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
                        <?php echo e(__('common.notes')); ?>

                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="notes_${exerciseId}" 
                               value="${notesValue}"
                               placeholder="<?php echo e(__('common.additional_notes')); ?>"
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
            
            // Если дата в формате YYYY-MM-DDTHH:mm:ss.sssZ (ISO), используем локальную дату
            if (typeof dateString === 'string' && dateString.includes('T')) {
                // Создаем объект Date и используем локальную дату
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${day}.${month}.${year}`;
            }
            
            // Иначе используем стандартное форматирование
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU');
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
        
        // Открытие модального окна с деталями упражнения
        openExerciseDetailModal(exercise) {
            // Создаем модальное окно динамически (как openSimpleModal)
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10px;
            `;
            
            // Создаем контент модального окна
            const content = document.createElement('div');
            content.style.cssText = `
                position: relative;
                background: white;
                border-radius: 12px;
                padding: 0;
                max-width: 1200px;
                width: 100%;
                max-height: 96vh;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            `;
            
            // Заголовок
            const header = document.createElement('div');
            header.style.cssText = 'padding: 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;';
            header.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0; font-size: 22px; font-weight: bold; color: #111827;">${exercise.name}</h3>
                        <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                            <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 12px; font-weight: 500;">
                                ${exercise.category || 'Не указано'}
                            </span>
                            <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 12px; font-weight: 500;">
                                ${exercise.equipment || 'Не указано'}
                            </span>
                        </div>
                    </div>
                    <button style="background: #f3f4f6; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #6b7280; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; margin-left: 12px;">
                        <span style="line-height: 1;">&times;</span>
                    </button>
                </div>
            `;
            
            const closeButton = header.querySelector('button');
            closeButton.addEventListener('click', () => modal.remove());
            
            // Контент с прокруткой
            const body = document.createElement('div');
            body.style.cssText = 'padding: 20px; overflow-y: auto; flex: 1;';
            
            let bodyHTML = '';
            
            // Изображения
            const hasImage1 = exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null;
            const hasImage2 = exercise.image_url_2 && exercise.image_url_2 !== 'null' && exercise.image_url_2 !== null;
            const isImage2Gif = hasImage2 && exercise.image_url_2.toLowerCase().endsWith('.gif');
            
            // Если вторая картинка - GIF, не показываем первую
            const showImage1 = hasImage1 && !isImage2Gif;
            
            if (showImage1 || hasImage2) {
                // Определяем количество колонок
                const gridColumns = (showImage1 && hasImage2) ? '1fr 1fr' : '1fr';
                bodyHTML += `<div style="display: grid; grid-template-columns: ${gridColumns}; gap: 16px; margin-bottom: 24px;">`;
                
                if (showImage1) {
                    bodyHTML += `
                        <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                            <img src="/storage/${exercise.image_url}" alt="${exercise.name}" style="width: 100%; height: 350px; object-fit: contain;">
                            <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                Фаза 1
                            </div>
                        </div>
                    `;
                }
                
                if (hasImage2) {
                    bodyHTML += `
                        <div style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                            <img src="/storage/${exercise.image_url_2}" alt="${exercise.name}" style="width: 100%; height: 350px; object-fit: contain;">
                            <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                                Фаза 2
                            </div>
                        </div>
                    `;
                }
                
                bodyHTML += '</div>';
            }
            
            // Описание
            if (exercise.description) {
                bodyHTML += `
                    <div style="margin-bottom: 24px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Описание</h4>
                        <p style="color: #6b7280; margin: 0; line-height: 1.6;">${exercise.description}</p>
                    </div>
                `;
            }
            
            // Инструкции
            if (exercise.instructions) {
                bodyHTML += `
                    <div style="margin-bottom: 24px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Инструкции</h4>
                        <div style="background: #f9fafb; border-left: 4px solid #6366f1; padding: 16px; border-radius: 8px;">
                            <p style="color: #374151; margin: 0; white-space: pre-line; line-height: 1.8;">${exercise.instructions}</p>
                        </div>
                    </div>
                `;
            }
            
            // Группы мышц
            if (exercise.muscle_groups && exercise.muscle_groups.length > 0) {
                bodyHTML += `
                    <div style="margin-bottom: 24px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Группы мышц</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            ${exercise.muscle_groups.map(muscle => `
                                <span style="display: inline-flex; align-items: center; padding: 6px 14px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 9999px; font-size: 13px; font-weight: 500;">${muscle}</span>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Видео
            if (exercise.video_url) {
                bodyHTML += '<div style="margin-bottom: 24px;"><h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">Видео</h4>';
                
                if (exercise.video_url.includes('youtube.com') || exercise.video_url.includes('youtu.be')) {
                    const embedUrl = this.getYouTubeEmbedUrl(exercise.video_url);
                    bodyHTML += `
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                            <iframe src="${embedUrl}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; border-radius: 12px;" allowfullscreen></iframe>
                        </div>
                    `;
                } else {
                    bodyHTML += `
                        <a href="${exercise.video_url}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                            <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Открыть видео
                        </a>
                    `;
                }
                
                bodyHTML += '</div>';
            }
            
            body.innerHTML = bodyHTML;
            
            // Собираем все вместе
            content.appendChild(header);
            content.appendChild(body);
            modal.appendChild(content);
            
            // Добавляем в DOM
            document.body.appendChild(modal);
            
            // Закрытие по клику на фон
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
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
        },
        
        // Получение статуса упражнения для списка тренировок (как у спортсмена)
        getExerciseStatusForList(workoutId, exerciseId) {
            return this.workoutProgress[workoutId]?.[exerciseId]?.status || null;
        },
        
        // Загрузка данных подходов из workoutProgress
        loadSetsDataFromProgress(workoutId) {
            if (this.workoutProgress[workoutId]) {
                Object.keys(this.workoutProgress[workoutId]).forEach(exerciseId => {
                    const progress = this.workoutProgress[workoutId][exerciseId];
                    if (progress.sets_data) {
                        // Загружаем данные подходов в exerciseSetsData
                        this.exerciseSetsData[exerciseId] = progress.sets_data;
                        // Поля остаются свернутыми по умолчанию
                        this.exerciseSetsExpanded[exerciseId] = false;
                    }
                });
            }
        },
        
        // Загрузка прогресса для всех тренировок (как у спортсмена)
        async loadAllWorkoutProgress() {
            for (let workout of this.workouts) {
                if (workout.exercises && workout.exercises.length > 0) {
                    try {
                        const response = await fetch(`/trainer/exercise-progress?workout_id=${workout.id}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        
                        const result = await response.json();
                        
                        // Поддерживаем оба формата ответа: массив и объект {success: true, progress: {...}}
                        if (result.success && result.progress) {
                            // Формат: {success: true, progress: {exerciseId: {...}}}
                            this.workoutProgress[workout.id] = {};
                            
                            Object.keys(result.progress).forEach(exerciseId => {
                                this.workoutProgress[workout.id][exerciseId] = result.progress[exerciseId];
                            });
                        } else if (Array.isArray(result) && result.length > 0) {
                            // Формат: [{exercise_id: ..., status: ...}, ...]
                            this.workoutProgress[workout.id] = {};
                            
                            result.forEach(progress => {
                                this.workoutProgress[workout.id][progress.exercise_id] = {
                                    status: progress.status,
                                    athlete_comment: progress.athlete_comment,
                                    sets_data: progress.sets_data,
                                    completed_at: progress.completed_at
                                };
                            });
                        }
                    } catch (error) {
                        console.error(`Ошибка загрузки прогресса для тренировки ${workout.id}:`, error);
                    }
                }
            }
        },
        
        // Инициализация
        init() {
            // Загружаем прогресс для всех тренировок при загрузке страницы
            this.loadAllWorkoutProgress();
            
            // Сбрасываем пагинацию при изменении фильтров
            this.$watch('search', () => {
                this.currentPage = 1;
            });
            
            this.$watch('status', () => {
                this.currentPage = 1;
            });
        }
    }
}


</script>

<?php $__env->startSection("sidebar"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="<?php echo e(route("crm.workouts.index")); ?>" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="<?php echo e(route("crm.exercises.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <?php if(auth()->user()->hasRole('trainer')): ?>
        <a href="<?php echo e(route("crm.trainer.athletes")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
        <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Подписка
        </a>
    <?php else: ?>
        <a href="<?php echo e(route("crm.nutrition.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    <?php endif; ?>
    <a href="<?php echo e(route('crm.trainer.settings')); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("mobile-menu"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?>">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="<?php echo e(route("crm.workouts.index")); ?>" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="<?php echo e(route("crm.exercises.index")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <?php if(auth()->user()->hasRole('trainer')): ?>
        <a href="<?php echo e(route("crm.trainer.athletes")); ?>" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
        <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Подписка
        </a>
    <?php else: ?>
        <a href="<?php echo e(route("crm.nutrition.index")); ?>" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    <?php endif; ?>
    <a href="<?php echo e(route('crm.trainer.settings')); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("header-actions"); ?>
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>
<div x-data="workoutApp()" x-init="init()" x-cloak class="space-y-6">
    

    <!-- Фильтры и поиск -->
    <div x-show="currentView === 'list'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                
                /* Убираем только рамку при фокусе для полей в развернутых подходах */
                .sets-fields-grid input[type="number"]:focus {
                    outline: none !important;
                    border: none !important;
                    box-shadow: none !important;
                    background: transparent !important;
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
                           placeholder="<?php echo e(__('common.search_workouts')); ?>" 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр статуса -->
                <div class="status-container">
                    <select x-model="status" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value=""><?php echo e(__('common.all_statuses')); ?></option>
                        <option value="planned"><?php echo e(__('common.planned')); ?></option>
                        <option value="completed"><?php echo e(__('common.completed')); ?></option>
                        <option value="cancelled"><?php echo e(__('common.cancelled')); ?></option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    <?php if(auth()->user()->hasRole('trainer')): ?>
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            <?php echo e(__('common.create_workout')); ?>

                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search || status" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500"><?php echo e(__('common.active_filters')); ?></span>
                
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
                    <!-- Вся информация в одной строке -->
                    <div class="flex items-center gap-4 mb-4">
                        <!-- Аватарка спортсмена -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-semibold text-lg">
                                <span x-text="(workout.athlete?.name || workout.trainer?.name || '?').charAt(0).toUpperCase()"></span>
                            </div>
                        </div>
                        
                        <!-- Название тренировки -->
                        <div class="flex-shrink-0 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors" x-text="workout.title"></h3>
                        </div>
                        
                        <!-- Информация о дате, времени и участнике -->
                        <div class="flex-1 flex items-center gap-6 text-sm text-gray-600">
                            <span class="flex-shrink-0">
                                <span class='font-medium text-gray-700'><?php echo e(__('common.date')); ?>:</span>
                                <span x-text="formatDate(workout.date)" class="text-gray-900 font-semibold"></span>
                            </span>
                            <span class="flex-shrink-0">
                                <span class='font-medium text-gray-700'><?php echo e(__('common.time')); ?>:</span>
                                <span x-text="workout.time ? workout.time.substring(0, 5) : '<?php echo e(__('common.not_specified')); ?>'" class="text-gray-900 font-semibold"></span>
                            </span>
                            <span class="flex-shrink-0">
                                <span class="font-medium text-gray-700"><?php echo e(__('common.participant')); ?></span>
                                <span x-text="workout.athlete?.name || workout.trainer?.name || '<?php echo e(__('common.not_specified')); ?>'" class="text-gray-900 font-semibold"></span>
                            </span>
                        </div>
                        
                        <!-- Статус -->
                        <span class="px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                              :class="{
                                  'bg-green-100 text-green-800': workout.status === 'completed',
                                  'bg-red-100 text-red-800': workout.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': workout.status === 'planned'
                              }"
                              x-text="getStatusLabel(workout.status)">
                        </span>
                    </div>
                    
                    <!-- Описание -->
                    <div class="mb-4">
                        <p class="text-gray-600 text-sm line-clamp-2" x-text="workout.description || ''"></p>
                        
                        <!-- Упражнения -->
                        <div x-show="(workout.exercises || []).length > 0" class="mt-3">
                            <div class="flex flex-wrap gap-1 items-center">
                                <div class="text-xs font-medium text-gray-500"><?php echo e(__('common.exercises')); ?></div>
                                <!-- Отображаем все упражнения через Alpine.js -->
                                <template x-for="(exercise, index) in (workout.exercises || [])" :key="`exercise-${workout.id}-${index}`">
                                    <span x-show="index < 5 || isExercisesExpanded(workout.id)"
                                          class="inline-block px-2 py-1 text-xs rounded-full font-medium"
                                          :class="{
                                              'bg-green-100 text-green-800': getExerciseStatusForList(workout.id, exercise.exercise_id || exercise.id) === 'completed',
                                              'bg-yellow-100 text-yellow-800': getExerciseStatusForList(workout.id, exercise.exercise_id || exercise.id) === 'partial',
                                              'bg-red-100 text-red-800': getExerciseStatusForList(workout.id, exercise.exercise_id || exercise.id) === 'not_done',
                                              'bg-gray-100 text-gray-600': !getExerciseStatusForList(workout.id, exercise.exercise_id || exercise.id)
                                          }"
                                          :title="exercise.progress?.athlete_comment ? '<?php echo e(__('common.athlete_comment')); ?>: ' + exercise.progress.athlete_comment : ''"
                                          x-text="exercise.name || '<?php echo e(__('common.no_title')); ?>'">
                                    </span>
                                </template>
                                
                                <!-- Кнопка разворачивания/сворачивания -->
                                <button x-show="(workout.exercises || []).length > 5" 
                                        @click="toggleExercisesExpanded(workout.id)" 
                                        class="inline-block px-2 py-1 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 text-xs rounded-full transition-colors cursor-pointer">
                                    <span x-text="isExercisesExpanded(workout.id) ? '<?php echo e(__('common.collapse')); ?>' : '+' + ((workout.exercises || []).length - 5) + ' <?php echo e(__('common.more')); ?>'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Действия -->
                    <div class="flex space-x-2">
                        <button @click="showView(workout.id)" 
                                class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            <?php echo e(__('common.view')); ?>

                        </button>
                        <?php if(auth()->user()->hasRole('trainer')): ?>
                            <button @click="showEdit(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                <?php echo e(__('common.edit')); ?>

                            </button>
                            <button @click="deleteWorkout(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                <?php echo e(__('common.delete')); ?>

                            </button>
                        <?php endif; ?>
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
            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo e(__('common.no_workouts')); ?></h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto"><?php echo e(__('common.no_workouts_description')); ?></p>
            <?php if(auth()->user()->hasRole('trainer')): ?>
                <button @click="showCreate()" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <?php echo e(__('common.create_first_workout')); ?>

                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- СОЗДАНИЕ/РЕДАКТИРОВАНИЕ ТРЕНИРОВКИ -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
                <span x-text="currentWorkout?.id ? '<?php echo e(__('common.edit_workout')); ?>' : '<?php echo e(__('common.create_workout')); ?>'"></span>
            </h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back_to_list')); ?>

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
                                <?php echo e(__('common.workout_title')); ?> *
                            </label>
                            <input type="text" 
                                   x-model="formTitle"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="<?php echo e(__('common.workout_title_placeholder')); ?>"
                                   required>
                        </div>

                        <div class="workout-athlete-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo e(__('common.athlete')); ?> *
                            </label>
                            <select x-model="formAthleteId" 
                                    class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                    required>
                                <option value=""><?php echo e(__('common.select_athlete')); ?></option>
                                <?php $__currentLoopData = $athletes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $athlete): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($athlete->id); ?>"><?php echo e($athlete->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <!-- Дата, время и продолжительность в одном ряду на десктопе -->
                    <div class="workout-date-duration-row">
                        <div class="workout-date-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo e(__('common.workout_date')); ?> *
                            </label>
                            <input type="date" 
                                   x-model="formDate"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   required>
                        </div>

                        <div class="workout-time-field">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo e(__('common.workout_time')); ?>

                            </label>
                            <div class="relative">
                                <input type="time" 
                                       x-model="formTime"
                                       id="timeInput"
                                       step="60"
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
                                <?php echo e(__('common.duration')); ?> (<?php echo e(__('common.minutes')); ?>)
                            </label>
                            <input type="number" 
                                   x-model="formDuration"
                                   class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   placeholder="<?php echo e(__('common.workout_duration_placeholder')); ?>"
                                   min="1">
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('common.workout_description')); ?>

                        </label>
                        <textarea x-model="formDescription"
                                  rows="6"
                                  class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                  placeholder="<?php echo e(__('common.workout_description_placeholder')); ?>"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <?php echo e(__('common.status')); ?>

                        </label>
                        <select x-model="formStatus" 
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="planned"><?php echo e(__('common.planned')); ?></option>
                            <option value="completed"><?php echo e(__('common.completed')); ?></option>
                            <option value="cancelled"><?php echo e(__('common.cancelled')); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Секция упражнений -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <div class="exercise-header-row">
                    <h3 style="font-size: 1.125rem; font-weight: 500; color: #111827; margin: 0;"><?php echo e(__('common.exercises')); ?></h3>
                    <div class="exercise-buttons-container">
                        <button type="button" onclick="openExerciseModal()" 
                                style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <?php echo e(__('common.add_exercise')); ?>

                        </button>
                        <button type="button" onclick="openTemplateModal()" 
                                style="display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #374151; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                            <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <?php echo e(__('common.add_template')); ?>

                        </button>
                    </div>
                </div>
                
                <!-- Выбранные упражнения -->
                <div id="selectedExercisesContainer" class="space-y-3" style="display: none;">
                    <h4 class="text-sm font-medium text-gray-700"><?php echo e(__('common.selected_exercises')); ?></h4>
                    <div id="selectedExercisesList" class="space-y-2">
                        <!-- Здесь будут отображаться выбранные упражнения -->
                    </div>
                </div>
                
                <!-- Пустое состояние -->
                <div id="emptyExercisesState" class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                    <p><?php echo e(__('common.add_exercises_or_template')); ?></p>
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
                    <span x-text="currentWorkout?.id ? '<?php echo e(__('common.update')); ?>' : '<?php echo e(__('common.create')); ?>'"></span>
                </button>
            </div>
        </form>
    </div>
    <!-- ПРОСМОТР ТРЕНИРОВКИ -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900"><?php echo e(__('common.view_workout')); ?></h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back_to_list')); ?>

            </button>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <!-- Заголовок и статус -->
            <div class="workout-title-section">
                <h4 class="text-2xl font-bold text-gray-900" x-text="currentWorkout?.title"></h4>
                
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
                                📅 Запланирована
                            </button>
                            <button @click="updateWorkoutStatus(currentWorkout.id, 'completed'); statusDropdownOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-green-50 transition-colors"
                                    :class="currentWorkout?.status === 'completed' ? 'bg-green-100 text-green-800' : 'text-gray-700'">
                                ✅ Завершена
                            </button>
                            <button @click="updateWorkoutStatus(currentWorkout.id, 'cancelled'); statusDropdownOpen = false"
                                    class="w-full px-4 py-2 text-left text-sm hover:bg-red-50 transition-colors"
                                    :class="currentWorkout?.status === 'cancelled' ? 'bg-red-100 text-red-800' : 'text-gray-700'">
                                ❌ Отменена
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Описание -->
            <div class="prose max-w-none" x-show="currentWorkout?.description">
                <h5 class="text-lg font-semibold text-gray-900 mb-3"><?php echo e(__('common.workout_description')); ?></h5>
                <p class="text-gray-600 whitespace-pre-line" x-text="currentWorkout?.description"></p>
            </div>
            
            <!-- Детали -->
            <div class="workout-details-grid">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500"><?php echo e(__('common.date')); ?></span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4" x-show="currentWorkout?.time">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500"><?php echo e(__('common.time')); ?></span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.time ? currentWorkout.time.substring(0, 5) : ''"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4" x-show="currentWorkout?.duration">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500"><?php echo e(__('common.duration')); ?></span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.duration + ' <?php echo e(__('common.min')); ?>'"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500"><?php echo e(__('common.participant')); ?></span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.athlete?.name || currentWorkout?.trainer?.name || '<?php echo e(__('common.unknown')); ?>'"></p>
                </div>
            </div>
            
            <!-- Упражнения -->
            <div x-show="(currentWorkout?.exercises || []).length > 0" class="pt-6 border-t border-gray-200">
                <h5 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.exercises')); ?></h5>
                <div class="space-y-4">
                    <template x-for="(exercise, index) in (currentWorkout?.exercises || [])" :key="`view-exercise-${index}`">
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="exercise-header-section">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-indigo-600 font-medium" x-text="(index + 1) + '.'"></span>
                                    
                                    <!-- Картинка упражнения (кликабельна) -->
                                    <div x-show="exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null" 
                                         @click="openExerciseDetailModal(exercise)"
                                         class="cursor-pointer hover:opacity-80 transition-opacity">
                                        <img :src="(exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null) ? '/storage/' + exercise.image_url : ''" 
                                             :alt="exercise.name"
                                             class="w-12 h-12 object-cover rounded-lg shadow-sm"
                                             onerror="this.parentElement.style.display='none'">
                                    </div>
                                    
                                    <!-- Название упражнения (кликабельно) -->
                                    <span class="text-sm font-medium text-gray-900 cursor-pointer hover:text-indigo-600 transition-colors" 
                                          @click="openExerciseDetailModal(exercise)"
                                          x-text="exercise.name || '<?php echo e(__('common.no_title')); ?>'"></span>
                                    <span class="text-xs text-gray-500" x-text="(exercise.category || '') + (exercise.category && exercise.equipment ? ' • ' : '') + (exercise.equipment || '')"></span>
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
                                <!-- Вес -->
                                <div x-show="exercise.fields_config?.includes('weight')" 
                                     class="exercise-field bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-orange-800"><?php echo e(__('common.weight_kg')); ?></span>
                                        </div>
                                        <div class="text-2xl font-bold text-orange-900" x-text="formatNumber(exercise.weight || exercise.pivot?.weight || 0)"></div>
                                    </div>
                                </div>
                                
                                <!-- Повторения -->
                                <div x-show="exercise.fields_config?.includes('reps')" 
                                     class="exercise-field bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800"><?php echo e(__('common.repetitions')); ?></span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900" x-text="formatNumber(exercise.reps || exercise.pivot?.reps || 0)"></div>
                                    </div>
                                </div>
                                
                                <!-- Подходы -->
                                <div x-show="exercise.fields_config?.includes('sets')" 
                                     class="exercise-field bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-indigo-800"><?php echo e(__('common.sets')); ?></span>
                                        </div>
                                        <div class="text-2xl font-bold text-indigo-900" x-text="formatNumber(exercise.sets || exercise.pivot?.sets || 0)"></div>
                                    </div>
                                </div>
                                
                                <!-- Отдых -->
                                <div x-show="exercise.fields_config?.includes('rest')" 
                                     class="exercise-field bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-purple-800"><?php echo e(__('common.rest')); ?> (<?php echo e(__('common.min')); ?>)</span>
                                        </div>
                                        <div class="text-2xl font-bold text-purple-900" x-text="formatNumber(exercise.rest || exercise.pivot?.rest || 0)"></div>
                                    </div>
                                </div>
                                
                                <!-- Время -->
                                <div x-show="exercise.fields_config?.includes('time')" 
                                     class="bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-blue-800"><?php echo e(__('common.time_sec')); ?></span>
                                        </div>
                                        <div class="text-2xl font-bold text-blue-900" x-text="exercise.time || exercise.pivot?.time || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Дистанция -->
                                <div x-show="exercise.fields_config?.includes('distance')" 
                                     class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-green-800"><?php echo e(__('common.distance_m')); ?></span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900" x-text="exercise.distance || exercise.pivot?.distance || 0"></div>
                                    </div>
                                </div>
                                
                                <!-- Темп -->
                                <div x-show="exercise.fields_config?.includes('tempo')" 
                                     class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4">
                                    <div class="text-center">
                                        <div class="flex items-center justify-center mb-2">
                                            <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-purple-800"><?php echo e(__('common.pace_speed')); ?></span>
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
                                    <span class="text-sm font-semibold text-gray-700"><?php echo e(__('common.notes')); ?></span>
                                </div>
                                <div class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3" x-text="exercise.notes || exercise.pivot?.notes"></div>
                            </div>
                            
                            <!-- Комментарий спортсмена -->
                            <div x-show="currentWorkout && getExerciseStatusForList(currentWorkout.id, exercise.exercise_id || exercise.id) === 'partial' && workoutProgress[currentWorkout.id]?.[exercise.exercise_id || exercise.id]?.athlete_comment" class="mt-3 pt-3 border-t border-yellow-200">
                                <div class="flex items-center mb-2">
                                    <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-yellow-700"><?php echo e(__('common.athlete_comment')); ?></span>
                                    <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium"><?php echo e(__('common.partially_completed')); ?></span>
                                </div>
                                <div class="text-sm text-gray-700 bg-yellow-50 rounded-lg p-3 border border-yellow-200" x-text="currentWorkout ? (workoutProgress[currentWorkout.id]?.[exercise.exercise_id || exercise.id]?.athlete_comment || '') : ''"></div>
                            </div>
                            
                            
                            <!-- Статус выполнения упражнения (для тренера) -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="exercise-status-section mb-3">
                                    <span class="text-sm font-medium text-gray-700 mb-2 block"><?php echo e(__('common.execution_status')); ?></span>
                                    <div class="exercise-status-buttons flex space-x-2">
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'completed')" 
                                                :class="currentWorkout && getExerciseStatusForList(currentWorkout.id, exercise.exercise_id || exercise.id) === 'completed' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ✅ <?php echo e(__('common.exercise_status_completed')); ?>

                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'partial')" 
                                                :class="currentWorkout && getExerciseStatusForList(currentWorkout.id, exercise.exercise_id || exercise.id) === 'partial' ? 'bg-yellow-100 text-yellow-800 border-yellow-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ⚠️ <?php echo e(__('common.exercise_status_partial')); ?>

                                        </button>
                                        <button @click="setExerciseStatus(exercise.exercise_id || exercise.id, 'not_done')" 
                                                :class="currentWorkout && getExerciseStatusForList(currentWorkout.id, exercise.exercise_id || exercise.id) === 'not_done' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-100 text-gray-600 border-gray-300'"
                                                class="px-3 py-1 text-xs font-medium border rounded-full transition-colors">
                                            ❌ <?php echo e(__('common.exercise_status_not_done')); ?>

                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Поля для каждого подхода (появляется при выборе "Частично") -->
                                <div x-show="currentWorkout && (getExerciseStatusForList(currentWorkout.id, exercise.exercise_id || exercise.id) === 'partial' || workoutProgress[currentWorkout.id]?.[exercise.exercise_id || exercise.id]?.sets_data)" class="mt-4">
                                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3 cursor-pointer rounded-lg p-2 -m-2"
                                             @click="toggleSetsExpanded(exercise.exercise_id || exercise.id)">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <h6 class="text-sm font-semibold text-yellow-800"><?php echo e(__('common.sets_breakdown')); ?></h6>
                                            </div>
                                            <div class="flex items-center text-xs text-yellow-700 hover:text-yellow-800 transition-colors">
                                                <span x-text="isSetsExpanded(exercise.exercise_id || exercise.id) ? '<?php echo e(__('common.collapse')); ?>' : '<?php echo e(__('common.expand')); ?>'"></span>
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
                                                <span><?php echo e(__('common.sets_fields_collapsed')); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div x-show="isSetsExpanded(exercise.exercise_id || exercise.id)" x-transition>
                                            <p class="text-xs text-yellow-700 mb-4"><?php echo e(__('common.specify_what_athlete_completed')); ?></p>
                                        
                                        <div class="space-y-3">
                                            <template x-for="(set, setIndex) in getSetsData(exercise.exercise_id || exercise.id)" :key="`set-${exercise.exercise_id || exercise.id}-${setIndex}`">
                                                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                            </svg>
                                                            <span class="text-sm font-semibold text-yellow-800"><?php echo e(__('common.set')); ?> <span x-text="setIndex + 1"></span> <?php echo e(__('common.of')); ?> <span x-text="exercise.sets || exercise.pivot?.sets || 0"></span></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="sets-fields-grid">
                                                        <!-- Вес -->
                                                        <div x-show="exercise.fields_config?.includes('weight')" 
                                                             class="bg-gradient-to-r from-purple-50 to-violet-50 border-2 border-purple-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'weight')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-purple-800"><?php echo e(__('common.weight_kg')); ?></span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.5"
                                                                    x-model="set.weight"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'weight', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-purple-900 bg-transparent border-none outline-none focus:outline-none focus:ring-0 focus:border-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Повторения -->
                                                        <div x-show="exercise.fields_config?.includes('reps')" 
                                                             class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'reps')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-green-800"><?php echo e(__('common.repetitions')); ?></span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    x-model="set.reps"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'reps', $event.target.value)"
                                                                    placeholder="0"
                                                                    class="w-full text-center text-lg font-bold text-green-900 bg-transparent border-none outline-none focus:outline-none focus:ring-0 focus:border-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Отдых -->
                                                        <div x-show="exercise.fields_config?.includes('rest')" 
                                                             class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-lg p-3"
                                                             :class="getSetFieldBorderClass(exercise, set, 'rest')">
                                                            <div class="text-center">
                                                                <div class="flex items-center justify-center mb-2">
                                                                    <svg class="w-4 h-4 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    <span class="text-xs font-semibold text-orange-800"><?php echo e(__('common.rest')); ?> (<?php echo e(__('common.min')); ?>)</span>
                                                                </div>
                                                                <input 
                                                                    type="number" 
                                                                    step="0.1"
                                                                    x-model="set.rest"
                                                                    @input="updateSetData(exercise.exercise_id || exercise.id, setIndex, 'rest', $event.target.value)"
                                                                    placeholder="1.0"
                                                                    class="w-full text-center text-lg font-bold text-orange-900 bg-transparent border-none outline-none focus:outline-none focus:ring-0 focus:border-none"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        
                                            <div class="text-xs text-yellow-600 mt-3">
                                                💡 <?php echo e(__('common.changes_save_automatically')); ?>

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
                    <p class="text-gray-500 text-sm"><?php echo e(__('common.no_exercises_added')); ?></p>
                </div>
            </div>
            
            <!-- Действия -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <button @click="showList()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    ← Назад к списку
                </button>
                <?php if(auth()->user()->hasRole('trainer')): ?>
                    <div class="flex space-x-2">
                        <button @click="showEdit(currentWorkout?.id)" 
                                class="px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                            <?php echo e(__('common.edit')); ?>

                        </button>
                        
                        <button @click="deleteWorkout(currentWorkout?.id)" 
                                class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                            <?php echo e(__('common.delete')); ?>

                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Красивое модальное окно для упражнений -->
<div id="exerciseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;"><?php echo e(__('common.exercise_selection')); ?></h3>
            <button onclick="closeExerciseModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <!-- Поиск и фильтры -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                <!-- Поиск -->
                <input type="text" 
                       id="exercise-search" 
                       placeholder="<?php echo e(__('common.search_exercises')); ?>" 
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
                    <option value=""><?php echo e(__('common.all_categories')); ?></option>
                    <option value="Грудь"><?php echo e(__('common.chest')); ?></option>
                    <option value="Спина"><?php echo e(__('common.back')); ?></option>
                    <option value="Ноги(Бедра)"><?php echo e(__('common.legs_thighs')); ?></option>
                    <option value="Ноги(Икры)"><?php echo e(__('common.legs_calves')); ?></option>
                    <option value="Плечи"><?php echo e(__('common.shoulders')); ?></option>
                    <option value="Руки(Трицепс)">Руки(Трицепс)</option>
                    <option value="Руки(Бицепс)">Руки(Бицепс)</option>
                    <option value="Руки(Предплечье)">Руки(Предплечье)</option>
                    <option value="Пресс"><?php echo e(__('common.abs')); ?></option>
                    <option value="Шея">Шея</option>
                    <option value="Кардио"><?php echo e(__('common.cardio')); ?></option>
                    <option value="Гибкость"><?php echo e(__('common.flexibility')); ?></option>
                </select>
                
                <!-- Фильтр оборудования -->
                <select id="equipment-filter" 
                        onchange="filterExercises()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 600; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value=""><?php echo e(__('common.all_equipment')); ?></option>
                </select>
            </div>
            
            <div id="exercises-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; width: 100%;">
                <p style="color: black;"><?php echo e(__('common.loading_exercises')); ?></p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;"><?php echo e(__('common.exercises_not_found')); ?></h3>
                <p style="font-size: 14px;"><?php echo e(__('common.try_changing_search_params')); ?></p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeExerciseModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.cancel')); ?></button>
            <button onclick="addSelectedExercises()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.done')); ?></button>
        </div>
    </div>
</div>

<!-- Красивое модальное окно для шаблонов -->
<div id="templateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;"><?php echo e(__('common.workout_template_selection')); ?></h3>
            <button onclick="closeTemplateModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <!-- Поиск и фильтры для шаблонов -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                <!-- Поиск -->
                <input type="text" 
                       id="template-search" 
                       placeholder="<?php echo e(__('common.search_templates')); ?>" 
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
                    <option value="strength"><?php echo e(__('common.strength')); ?></option>
                    <option value="cardio"><?php echo e(__('common.cardio')); ?></option>
                    <option value="flexibility"><?php echo e(__('common.flexibility')); ?></option>
                    <option value="mixed"><?php echo e(__('common.mixed')); ?></option>
                </select>
                
                <!-- Фильтр сложности -->
                <select id="template-difficulty-filter" 
                        onchange="filterTemplates()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value=""><?php echo e(__('common.all_levels')); ?></option>
                    <option value="beginner"><?php echo e(__('common.beginner')); ?></option>
                    <option value="intermediate"><?php echo e(__('common.intermediate')); ?></option>
                    <option value="advanced"><?php echo e(__('common.advanced')); ?></option>
                </select>
            </div>
            
            <div id="templates-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                <p style="color: black;"><?php echo e(__('common.loading_templates')); ?></p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-templates-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;"><?php echo e(__('common.templates_not_found')); ?></h3>
                <p style="font-size: 14px;"><?php echo e(__('common.create_workout_template')); ?></p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeTemplateModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.cancel')); ?></button>
            <button onclick="addSelectedTemplate()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.done')); ?></button>
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
        const response = await fetch('/api/exercises', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            exercises = data.exercises;
            renderExercises();
        } else {
            console.error('API вернул ошибку:', data);
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
        container.innerHTML = '<p style="color: black;"><?php echo e(__('common.exercises_not_found')); ?></p>';
        return;
    }
    
    container.innerHTML = exercises.map(exercise => {
        const hasImage = exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null;
        const imageUrl = hasImage ? `/storage/${exercise.image_url}` : '';
        
        // Экранируем специальные символы для безопасного использования в HTML атрибутах
        const escapeName = (exercise.name || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        const escapeCategory = (exercise.category || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        const escapeEquipment = (exercise.equipment || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        
        return `
            <div data-exercise-id="${exercise.id}" 
                 data-exercise-name="${escapeName}" 
                 data-exercise-category="${escapeCategory}" 
                 data-exercise-equipment="${escapeEquipment}"
                 style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; cursor: pointer; background: white; display: flex; flex-direction: row; align-items: flex-start; gap: 14px; max-width: 100%; box-sizing: border-box; min-height: 160px;"
                 onclick="toggleExercise(this, ${exercise.id})">
                ${hasImage ? `
                    <img src="${imageUrl}" 
                         alt="${escapeName}" 
                         style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                ` : ''}
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; color: #111827; margin-bottom: 5px; font-size: 15px; word-wrap: break-word; line-height: 1.3;">${exercise.name}</div>
                    <div style="font-size: 13px; color: #6b7280; margin-bottom: 3px;">${exercise.category}</div>
                    <div style="font-size: 13px; color: #9ca3af;">${exercise.equipment}</div>
                </div>
            </div>
        `;
    }).join('');
    
    // Применяем фильтрацию после рендеринга
    filterExercises();
}

// Отображение шаблонов
function renderTemplates() {
    const container = document.getElementById('templates-container');
    if (templates.length === 0) {
        container.innerHTML = '<p style="color: black;"><?php echo e(__('common.templates_not_found')); ?></p>';
        return;
    }
    
    container.innerHTML = templates.map(template => {
        const exerciseCount = (template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises.length : (template.exercises ? template.exercises.length : 0);
        const duration = template.estimated_duration ? `${template.estimated_duration} <?php echo e(__('common.min')); ?>` : '<?php echo e(__('common.not_specified')); ?>';
        const difficulty = template.difficulty_label || template.difficulty || '<?php echo e(__('common.not_specified')); ?>';
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
                <p style="font-size: 12px; color: #9ca3af; margin-bottom: 8px;"><?php echo e(__('common.difficulty')); ?>: ${difficulty}</p>
                <p style="font-size: 14px; color: #9ca3af;">${template.description || ''}</p>
            </div>
        `;
    }).join('');
}

// Переключение упражнения
function toggleExercise(element, id) {
    const isSelected = element.style.backgroundColor === 'rgb(239, 246, 255)';
    
    if (isSelected) {
        element.style.backgroundColor = 'white';
        element.style.borderColor = '#e5e7eb';
    } else {
        element.style.backgroundColor = 'rgb(239, 246, 255)';
        element.style.borderColor = 'rgb(147, 197, 253)';
    }
    
    // Сохраняем состояние выделения
    element.dataset.selected = !isSelected;
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
    const category = document.getElementById('category-filter').value;
    const equipment = document.getElementById('equipment-filter').value;
    const container = document.getElementById('exercises-container');
    const noResults = document.getElementById('no-results');

    // Динамически заполняем список оборудования по выбранной категории
    const equipmentSelect = document.getElementById('equipment-filter');
    const prevValue = equipment;
    const equipmentSet = new Set();
    (exercises || []).forEach(ex => {
        if (!category || ex.category === category) {
            if (ex.equipment && ex.equipment !== 'null' && ex.equipment !== null) {
                equipmentSet.add(ex.equipment);
            }
        }
    });
    const currentOptions = Array.from(equipmentSelect.options).map(o => o.value);
    const desiredOptions = [''].concat(Array.from(equipmentSet).sort());
    if (JSON.stringify(currentOptions) !== JSON.stringify(desiredOptions)) {
        equipmentSelect.innerHTML = '';
        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = '<?php echo e(__('common.all_equipment')); ?>';
        equipmentSelect.appendChild(emptyOpt);
        Array.from(equipmentSet).sort().forEach(eq => {
            const opt = document.createElement('option');
            opt.value = eq;
            opt.textContent = eq;
            equipmentSelect.appendChild(opt);
        });
        // Восстанавливаем значение, если оно по-прежнему доступно
        if (desiredOptions.includes(prevValue)) {
            equipmentSelect.value = prevValue;
        } else {
            equipmentSelect.value = '';
        }
    }

    // Фильтрация карточек упражнений
    let visibleCount = 0;
    Array.from(container.children).forEach(element => {
        const name = element.dataset.exerciseName.toLowerCase();
        const elementCategory = element.dataset.exerciseCategory;
        const elementEquipment = element.dataset.exerciseEquipment;

        const matchesSearch = !searchTerm || name.includes(searchTerm);
        const matchesCategory = !category || elementCategory === category;
        const matchesEquipment = !equipment || elementEquipment === equipment;

        if (matchesSearch && matchesCategory && matchesEquipment) {
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
        
        // Используем данные из data-атрибутов как fallback
        const category = fullExercise ? fullExercise.category : el.dataset.exerciseCategory;
        const equipment = fullExercise ? fullExercise.equipment : el.dataset.exerciseEquipment;
        
        return {
            id: exerciseId,
            name: el.dataset.exerciseName,
            category: category || '<?php echo e(__('common.not_specified')); ?>',
            equipment: equipment || 'Не указано',
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
    
    // Проверяем, находимся ли мы в режиме редактирования
    const appElement = document.querySelector('[x-data*="workoutApp"]');
    
    if (appElement) {
        const workoutApp = Alpine.$data(appElement);
        if (workoutApp && workoutApp.currentWorkout && workoutApp.currentWorkout.exercises) {
            // Режим редактирования - обновляем данные в Alpine.js
            workoutApp.currentWorkout.exercises = allExercises;
            workoutApp.displaySelectedExercises(allExercises);
        } else {
            // Режим создания - используем глобальную функцию
    displaySelectedExercises(allExercises);
        }
    } else {
        // Режим создания - используем глобальную функцию
        displaySelectedExercises(allExercises);
    }
    
    // Привязываем события drag and drop к новым упражнениям
    setTimeout(() => {
        bindDragDropEvents();
    }, 100);
    
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
        
        // Определяем fields_config на основе видимых полей в DOM
        const visibleFields = [];
        const fieldInputs = element.querySelectorAll('input[name*="_"]');
        fieldInputs.forEach(input => {
            const fieldName = input.name.split('_')[0];
            if (!visibleFields.includes(fieldName)) {
                visibleFields.push(fieldName);
            }
        });
        
        // Извлекаем категорию и оборудование из DOM
        const categoryEquipmentSpan = element.querySelector('.text-gray-600');
        let category = '';
        let equipment = '';
        
        if (categoryEquipmentSpan) {
            const text = categoryEquipmentSpan.textContent;
            const match = text.match(/\(([^•]+)•([^)]+)\)/);
            if (match) {
                category = match[1].trim();
                equipment = match[2].trim();
            }
        }
        
        const exerciseData = {
            id: parseInt(exerciseId),
            name: exerciseName,
            category: category,
            equipment: equipment,
            fields_config: visibleFields.length > 0 ? visibleFields : ['sets', 'reps', 'weight', 'rest']
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

// Сбор данных упражнений из DOM для режима создания
function collectExerciseDataFromDOM() {
    const exercises = [];
    const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    
    exerciseElements.forEach(element => {
        const exerciseId = element.dataset.exerciseId;
        
        // Ищем название упражнения
        const nameSpans = element.querySelectorAll('.font-medium');
        const exerciseName = nameSpans.length > 1 ? nameSpans[1].textContent : nameSpans[0].textContent;
        
        // Собираем все поля динамически
        const exerciseData = {
            exercise_id: parseInt(exerciseId),
            name: exerciseName,
            sets: 3,
            reps: 12,
            weight: 0,
            rest: 60,
            time: 0,
            distance: 0,
            tempo: '',
            notes: ''
        };
        
        // Собираем значения всех полей
        ['sets', 'reps', 'weight', 'rest', 'time', 'distance', 'tempo', 'notes'].forEach(field => {
            const input = element.querySelector(`input[name="${field}_${exerciseId}"]`);
            if (input) {
                exerciseData[field] = input.value || '';
            }
        });
        
        exercises.push(exerciseData);
    });
    
    return exercises;
}

// Генерация HTML для полей на основе конфигурации
function generateFieldsHtml(exerciseId, fieldsConfig, exerciseData = null) {
    // Функция для безопасного получения значения поля
    function getFieldValue(fieldName, defaultValue = '') {
        if (!exerciseData || exerciseData[fieldName] === undefined || exerciseData[fieldName] === null || exerciseData[fieldName] === 'null' || exerciseData[fieldName] === '') {
            return defaultValue;
        }
        // Дополнительная проверка для строковых значений
        if (typeof exerciseData[fieldName] === 'string' && exerciseData[fieldName].trim() === '') {
            return defaultValue;
        }
        return exerciseData[fieldName];
    }
    const fieldConfigs = {
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
        'reps': {
            label: 'Повторения',
            icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            color: 'green',
            type: 'number',
            min: '1',
            max: '100',
            value: '10'
        },
        'sets': {
            label: 'Подходы',
            icon: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            color: 'indigo',
            type: 'number',
            min: '1',
            max: '20',
            value: '3'
        },
        'rest': {
            label: '<?php echo e(__('common.rest')); ?> (<?php echo e(__('common.min')); ?>)',
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'purple',
            type: 'number',
            min: '0',
            max: '60',
            step: '0.5',
            value: '2'
        },
        'time': {
            label: 'Время (мин)',
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            color: 'blue',
            type: 'number',
            min: '0',
            max: '120',
            step: '0.5',
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
                               value="${getFieldValue(field, config.value).toString().replace('null', '')}"
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
                <?php echo e(__('common.notes')); ?>

            </label>
            <div class="relative">
                <input type="text" 
                       name="notes_${exerciseId}" 
                       placeholder="Дополнительные заметки..."
                       value="${getFieldValue('notes', '').toString().replace('null', '')}"
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
function displaySelectedExercises(exercises, isViewMode = false) {
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
        const htmlContent = exercises.map((exercise, index) => {
            const fieldsConfig = exercise.fields_config;
            const exerciseId = exercise.exercise_id || exercise.id;
            const fieldsHtml = generateFieldsHtml(exerciseId, fieldsConfig, exercise);
            
            return `
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md" 
                     data-exercise-id="${exerciseId}" 
                     data-exercise-index="${index}">
                    <!-- Заголовок упражнения -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3 flex-1" title="Перетащите для изменения порядка">
                            <!-- Drag Handle -->
                            <div class="text-gray-400 hover:text-gray-600 cursor-move" 
                                 draggable="true" 
                                 data-exercise-id="${exerciseId}" 
                                 data-exercise-index="${index}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </div>
                            <div class="flex-1 cursor-pointer" onclick="toggleExerciseDetails(${exercise.id})" onmousedown="event.stopPropagation()">
                            <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                            <span class="font-medium text-gray-900">${exercise.name}</span>
                            <span class="text-sm text-gray-600">(${exercise.category || '<?php echo e(__('common.not_specified')); ?>'} • ${exercise.equipment || '<?php echo e(__('common.not_specified')); ?>'})</span>
                        </div>
                            <div onclick="toggleExerciseDetails(${exercise.id})" 
                                 onmousedown="event.stopPropagation()" 
                                 class="cursor-pointer">
                                <svg id="chevron-${exercise.id}" class="w-4 h-4 text-gray-400 transition-transform" style="transform: ${isViewMode ? 'rotate(0deg)' : 'rotate(-90deg)'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <button type="button" onclick="removeExercise(${exercise.id})" class="text-red-600 hover:text-red-800 ml-2" onmousedown="event.stopPropagation()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Параметры упражнения - сворачиваемые -->
                    <div id="details-${exercise.id}" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200" style="display: ${isViewMode ? 'block' : 'none'}">
                        <div class="exercise-params-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                            ${fieldsHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        // Глобальная замена всех "null" значений в HTML
        const cleanedHtml = htmlContent.replace(/value="null"/g, 'value=""').replace(/value='null'/g, 'value=""');
        
        list.innerHTML = cleanedHtml;
        
        // Привязываем события drag and drop к новым элементам
        const exerciseElements = list.querySelectorAll('[data-exercise-id]');
        
        exerciseElements.forEach((element, index) => {
            const exerciseId = parseInt(element.dataset.exerciseId);
            
            // Обновляем data-exercise-index на реальный индекс в DOM
            element.setAttribute('data-exercise-index', index);
            
            // События drag and drop привязываются через общий файл
        });
        
        // Привязываем события drag and drop через общий файл
        setTimeout(() => {
            bindDragDropEvents();
        }, 0);
    } else {
        // Показываем пустое состояние
        emptyState.style.display = 'block';
        container.style.display = 'none';
    }
}

// Сворачивание/разворачивание деталей упражнения
// Загрузка истории упражнения
async function loadExerciseHistory(exerciseId) {
    try {
        // Получаем ID текущего спортсмена из Alpine.js
        const athleteSelect = document.querySelector('select[x-model="formAthleteId"]');
        const athleteId = athleteSelect?.value;
        console.log('Выбранный спортсмен ID:', athleteId);
        if (!athleteId) {
            console.log('Спортсмен не выбран, история не загружается');
            return;
        }
        
        const response = await fetch(`/trainer/exercises/${exerciseId}/history?athlete_id=${athleteId}`);
        const data = await response.json();
        
        if (data.success && data.has_history) {
            console.log(`История упражнения ${exerciseId}:`, data);
            
            // Автозаполняем поля значениями из последней тренировки
            const fieldsToFill = data.plan;
            
            // Если есть факт - используем его, иначе план
            const valuesToUse = data.fact || fieldsToFill;
            
            for (const [field, value] of Object.entries(valuesToUse)) {
                const input = document.querySelector(`input[name="${field}_${exerciseId}"]`);
                if (input && value) {
                    input.value = value;
                }
            }
            
            // Добавляем компактную подсказку над полями
            const detailsDiv = document.getElementById(`details-${exerciseId}`);
            if (detailsDiv) {
                // Удаляем существующую подсказку если есть
                const existingHint = detailsDiv.querySelector('.exercise-history-hint');
                if (existingHint) {
                    existingHint.remove();
                }
                
                const date = new Date(data.workout_date).toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
                
                // Получаем разрешённые поля для упражнения
                const allowedFields = data.fields_config || [];
                const isFieldAllowed = (field) => allowedFields.length === 0 || allowedFields.includes(field);
                
                // Формируем строку с планом (показываем только разрешённые и заполненные поля)
                let planText = '';
                if (isFieldAllowed('weight') && data.plan.weight > 0) planText += `${data.plan.weight} кг`;
                if (isFieldAllowed('reps') && data.plan.reps > 0) planText += (planText ? ' × ' : '') + `${data.plan.reps} раз`;
                if (isFieldAllowed('sets') && data.plan.sets > 0) planText += (planText ? ' × ' : '') + `${data.plan.sets} подх`;
                if (isFieldAllowed('time') && data.plan.time > 0) planText += (planText ? ', ' : '') + `${data.plan.time} мин`;
                if (isFieldAllowed('distance') && data.plan.distance > 0) planText += (planText ? ', ' : '') + `${data.plan.distance} м`;
                
                // Формируем строку с фактом (если есть)
                let factText = '';
                if (data.fact) {
                    if (isFieldAllowed('weight') && data.fact.weight > 0) factText += `${data.fact.weight} кг`;
                    if (isFieldAllowed('reps') && data.fact.reps > 0) factText += (factText ? ' × ' : '') + `${data.fact.reps} раз`;
                    if (isFieldAllowed('sets') && data.fact.sets > 0) factText += (factText ? ' × ' : '') + `${data.fact.sets} подх`;
                    if (isFieldAllowed('time') && data.fact.time > 0) factText += (factText ? ', ' : '') + `${data.fact.time} мин`;
                    if (isFieldAllowed('distance') && data.fact.distance > 0) factText += (factText ? ', ' : '') + `${data.fact.distance} м`;
                }
                
                const hint = document.createElement('div');
                hint.className = 'exercise-history-hint';
                hint.style.cssText = 'margin-bottom: 12px; padding: 12px; background: linear-gradient(to right, #eff6ff, #e0e7ff); border: 1px solid #bfdbfe; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;';
                
                const factColor = data.exercise_status === 'completed' ? '#15803d' : (data.exercise_status === 'partial' ? '#c2410c' : '#dc2626');
                const factIcon = data.exercise_status === 'completed' ? '✅' : (data.exercise_status === 'partial' ? '⚠️' : '❌');
                const factLabel = data.exercise_status === 'completed' ? 'Выполнено:' : (data.exercise_status === 'partial' ? 'Частично:' : 'Факт:');
                
                hint.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 16px; height: 16px; color: #2563eb; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span style="font-size: 12px; font-weight: 600; color: #1e40af;">С прошлого раза (${date})</span>
                        </div>
                        <div style="font-size: 12px; color: #374151;">
                            📋 <span style="font-weight: 500;">План:</span> ${planText || 'не указан'}
                        </div>
                        ${data.fact ? `
                            <div style="font-size: 12px; color: ${factColor};">
                                ${factIcon} <span style="font-weight: 500;">${factLabel}</span> ${factText}
                            </div>
                        ` : '<div style="font-size: 12px; color: #9ca3af; font-style: italic;">Не выполнено</div>'}
                    </div>
                    <button type="button" 
                            onclick="showExerciseHistoryModal(${exerciseId})"
                            style="font-size: 12px; color: #2563eb; font-weight: 500; display: flex; align-items: center; gap: 4px; background: white; padding: 6px 12px; border-radius: 6px; border: 1px solid #93c5fd; cursor: pointer; flex-shrink: 0; transition: all 0.2s;"
                            onmouseover="this.style.borderColor='#60a5fa'; this.style.color='#1d4ed8';"
                            onmouseout="this.style.borderColor='#93c5fd'; this.style.color='#2563eb';">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>История</span>
                    </button>
                `;
                detailsDiv.insertBefore(hint, detailsDiv.firstChild);
            }
        }
    } catch (error) {
        console.error('Ошибка загрузки истории упражнения:', error);
    }
}
function toggleExerciseDetails(exerciseId) {
    console.log('toggleExerciseDetails вызван для упражнения:', exerciseId);
    const detailsElement = document.getElementById(`details-${exerciseId}`);
    const chevronElement = document.getElementById(`chevron-${exerciseId}`);
    
    if (detailsElement.style.display === 'none') {
        console.log('Разворачиваем упражнение, загружаем историю...');
        // Разворачиваем
        detailsElement.style.display = 'block';
        chevronElement.style.transform = 'rotate(0deg)'; // стрелочка вниз
        
        // Загружаем историю при первом открытии
        loadExerciseHistory(exerciseId);
    } else {
        console.log('Сворачиваем упражнение');
        // Сворачиваем
        detailsElement.style.display = 'none';
        chevronElement.style.transform = 'rotate(-90deg)'; // стрелочка вправо
    }
}

// Удаление упражнения из списка
function removeExercise(exerciseId) {
    // Проверяем, находимся ли мы в режиме редактирования
    const appElement = document.querySelector('[x-data*="workoutApp"]');
    
    if (appElement) {
        const workoutApp = Alpine.$data(appElement);
        if (workoutApp && workoutApp.currentWorkout && workoutApp.currentWorkout.exercises) {
            // Режим редактирования - удаляем из данных Alpine.js
            workoutApp.currentWorkout.exercises = workoutApp.currentWorkout.exercises.filter(
                ex => ex.id !== parseInt(exerciseId) && ex.exercise_id !== parseInt(exerciseId)
            );
            
            // Перерисовываем упражнения через Alpine.js
            if (workoutApp.currentWorkout.exercises.length > 0) {
                workoutApp.displaySelectedExercises(workoutApp.currentWorkout.exercises);
            } else {
                // Если упражнений не осталось, показываем пустое состояние
                document.getElementById('selectedExercisesContainer').style.display = 'none';
                document.getElementById('emptyExercisesState').style.display = 'block';
            }
            return;
        }
    }
    
    // Режим создания - удаляем из DOM напрямую
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
    } else {
        // Пересчитываем индексы для оставшихся упражнений
        remainingElements.forEach((element, index) => {
            // Обновляем data-exercise-index
            element.setAttribute('data-exercise-index', index);
            
            // Обновляем отображаемый номер
            const numberSpan = element.querySelector('.text-indigo-600');
            if (numberSpan) {
                numberSpan.textContent = `${index + 1}.`;
            }
        });
    }
}

// Добавление выбранного шаблона
function addSelectedTemplate() {
    if (selectedTemplate) {
        // Преобразуем упражнения из шаблона в формат, который ожидает displaySelectedExercises
        const templateExercises = selectedTemplate.exercises.map(exercise => {
            // Находим полные данные упражнения из загруженного массива
            const fullExercise = exercises.find(ex => ex.id === exercise.id);
            
            // Используем данные из полного упражнения или из шаблона как fallback
            const category = fullExercise ? fullExercise.category : (exercise.category || 'Не указано');
            const equipment = fullExercise ? fullExercise.equipment : (exercise.equipment || 'Не указано');
            
            return {
                id: exercise.id,
                name: exercise.name,
                category: category,
                equipment: equipment,
                fields_config: fullExercise ? fullExercise.fields_config : ['sets', 'reps', 'weight', 'rest']
            };
        });
        
        // Проверяем, находимся ли мы в режиме редактирования
        const appElement = document.querySelector('[x-data*="workoutApp"]');
        
        if (appElement) {
            const workoutApp = Alpine.$data(appElement);
            if (workoutApp && workoutApp.currentWorkout && workoutApp.currentWorkout.exercises) {
                // Режим редактирования - добавляем к существующим упражнениям
                const currentExercises = workoutApp.currentWorkout.exercises || [];
                const existingIds = currentExercises.map(ex => ex.id);
                const uniqueTemplateExercises = templateExercises.filter(ex => !existingIds.includes(ex.id));
                const allExercises = [...currentExercises, ...uniqueTemplateExercises];
                
                workoutApp.currentWorkout.exercises = allExercises;
                workoutApp.displaySelectedExercises(allExercises);
            } else {
                // Режим создания - заменяем все упражнения
        displaySelectedExercises(templateExercises);
            }
        } else {
            // Режим создания - заменяем все упражнения
            displaySelectedExercises(templateExercises);
        }
        
        // Привязываем события drag and drop к новым упражнениям из шаблона
        setTimeout(() => {
            bindDragDropEvents();
        }, 100);
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

// Модальное окно истории упражнения
async function showExerciseHistoryModal(exerciseId) {
    try {
        // Получаем ID текущего спортсмена из Alpine.js
        const athleteSelect = document.querySelector('select[x-model="formAthleteId"]');
        const athleteId = athleteSelect?.value;
        if (!athleteId) {
            alert('Сначала выберите спортсмена');
            return;
        }
        
        const response = await fetch(`/trainer/exercises/${exerciseId}/history?athlete_id=${athleteId}`);
        const data = await response.json();
        
        if (data.success && data.has_history) {
            // Получаем разрешённые поля для упражнения
            const allowedFields = data.fields_config || [];
            const isFieldAllowed = (field) => allowedFields.length === 0 || allowedFields.includes(field);
            
            // Функция для форматирования плана
            const formatPlan = (plan) => {
                let text = '';
                if (isFieldAllowed('weight') && plan.weight > 0) text += `${plan.weight} кг`;
                if (isFieldAllowed('reps') && plan.reps > 0) text += (text ? ' × ' : '') + `${plan.reps} раз`;
                if (isFieldAllowed('sets') && plan.sets > 0) text += (text ? ' × ' : '') + `${plan.sets} подх`;
                if (isFieldAllowed('time') && plan.time > 0) text += (text ? ', ' : '') + `${plan.time} мин`;
                if (isFieldAllowed('distance') && plan.distance > 0) text += (text ? ', ' : '') + `${plan.distance} м`;
                return text || 'не указан';
            };
            
            // Функция для форматирования факта
            const formatFact = (fact) => {
                if (!fact) return '';
                let text = '';
                if (isFieldAllowed('weight') && fact.weight > 0) text += `${fact.weight} кг`;
                if (isFieldAllowed('reps') && fact.reps > 0) text += (text ? ' × ' : '') + `${fact.reps} раз`;
                if (isFieldAllowed('sets') && fact.sets > 0) text += (text ? ' × ' : '') + `${fact.sets} подх`;
                if (isFieldAllowed('time') && fact.time > 0) text += (text ? ', ' : '') + `${fact.time} мин`;
                if (isFieldAllowed('distance') && fact.distance > 0) text += (text ? ', ' : '') + `${fact.distance} м`;
                return text;
            };
            
            // Создаем модальное окно
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            const content = document.createElement('div');
            content.style.cssText = `
                background: white;
                border-radius: 12px;
                padding: 0;
                max-width: 700px;
                width: 90%;
                max-height: 85vh;
                overflow: hidden;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            `;
            
            // Заголовок
            const header = document.createElement('div');
            header.style.cssText = `
                padding: 20px 24px 16px 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f9fafb;
            `;
            
            header.innerHTML = `
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #111827;">История упражнения</h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280; padding: 4px; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f3f4f6'; this.style.color='#374151';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#6b7280';">&times;</button>
            `;
            
            const closeButton = header.querySelector('button');
            closeButton.addEventListener('click', () => modal.remove());
            
            // Контент
            const body = document.createElement('div');
            body.style.cssText = `
                padding: 24px;
                max-height: 65vh;
                overflow-y: auto;
            `;
            
            // Формируем HTML для всех тренировок
            const allWorkouts = data.all_workouts || [];
            let workoutsHTML = '';
            
            if (allWorkouts.length > 0) {
                workoutsHTML = allWorkouts.map((workout, index) => {
                    const workoutDate = new Date(workout.workout_date).toLocaleDateString('ru-RU', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        year: 'numeric' 
                    });
                    
                    const planText = formatPlan(workout.plan);
                    const factText = formatFact(workout.fact);
                    
                    const factColor = workout.exercise_status === 'completed' ? '#15803d' : (workout.exercise_status === 'partial' ? '#c2410c' : '#dc2626');
                    const factIcon = workout.exercise_status === 'completed' ? '✅' : (workout.exercise_status === 'partial' ? '⚠️' : '❌');
                    const factLabel = workout.exercise_status === 'completed' ? 'Выполнено:' : (workout.exercise_status === 'partial' ? 'Частично:' : 'Факт:');
                    
                    // Определяем фон и границу в зависимости от статуса
                    let factBgColor = '#fef2f2'; // по умолчанию красный
                    let factBorderColor = '#fecaca';
                    
                    if (workout.exercise_status === 'completed') {
                        factBgColor = '#f0fdf4'; // зеленый
                        factBorderColor = '#bbf7d0';
                    } else if (workout.exercise_status === 'partial') {
                        factBgColor = '#fef3c7'; // желтый (bg-yellow-100)
                        factBorderColor = '#fde68a'; // желтый border (border-yellow-200)
                    }
                    
                    return `
                        <div style="margin-bottom: ${index < allWorkouts.length - 1 ? '20px' : '0'}; padding-bottom: ${index < allWorkouts.length - 1 ? '20px' : '0'}; border-bottom: ${index < allWorkouts.length - 1 ? '1px solid #e5e7eb' : 'none'};">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <svg style="width: 18px; height: 18px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span style="font-size: 15px; font-weight: 600; color: #1e40af;">${workout.workout_title} (${workoutDate})</span>
                            </div>
                            
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                    <span style="font-size: 13px; font-weight: 600; color: #374151;">📋 План:</span>
                                </div>
                                <div style="font-size: 13px; color: #4b5563; margin-left: 20px;">
                                    ${planText}
                                </div>
                            </div>
                            
                            ${workout.fact ? `
                                <div style="background: ${factBgColor}; border: 1px solid ${factBorderColor}; border-radius: 8px; padding: 12px; margin-bottom: 0;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <span style="font-size: 13px; font-weight: 600; color: ${factColor};">${factIcon} ${factLabel}</span>
                                    </div>
                                    <div style="font-size: 13px; color: #4b5563; margin-left: 20px; margin-bottom: ${workout.sets_details && workout.sets_details.length > 0 && workout.exercise_status === 'partial' ? '12px' : '0'};">
                                        ${factText}
                                    </div>
                                    ${workout.sets_details && workout.sets_details.length > 0 && workout.exercise_status === 'partial' ? `
                                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid ${factBorderColor};">
                                            <div style="font-size: 13px; font-weight: 600; color: ${factColor}; margin-bottom: 8px;">Детали подходов:</div>
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                ${workout.sets_details.map((set, setIndex) => `
                                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 10px; background: rgba(255, 255, 255, 0.7); border-radius: 6px; border: 1px solid ${factBorderColor};">
                                                        <span style="font-size: 12px; font-weight: 500; color: #6b7280;">Подход ${setIndex + 1}</span>
                                                        <span style="font-size: 12px; color: #374151;">
                                                            ${set.weight > 0 ? `${set.weight} кг` : ''}
                                                            ${set.reps > 0 ? (set.weight > 0 ? ' × ' : '') + `${set.reps} раз` : ''}
                                                            ${set.time > 0 ? (set.weight > 0 || set.reps > 0 ? ', ' : '') + `${set.time} мин` : ''}
                                                            ${set.distance > 0 ? (set.weight > 0 || set.reps > 0 || set.time > 0 ? ', ' : '') + `${set.distance} м` : ''}
                                                        </span>
                                                    </div>
                                                `).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            ` : `
                                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-bottom: 0;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 13px; font-weight: 600; color: #dc2626;">❌ Не выполнено</span>
                                    </div>
                                </div>
                            `}
                        </div>
                    `;
                }).join('');
            } else {
                workoutsHTML = '<div style="text-align: center; padding: 40px; color: #6b7280;">Нет истории тренировок</div>';
            }
            
            body.innerHTML = workoutsHTML;
            
            // Кнопки
            const footer = document.createElement('div');
            footer.style.cssText = `
                padding: 16px 24px 20px 24px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                background: #f9fafb;
            `;
            
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.textContent = 'Закрыть';
            closeBtn.style.cssText = 'padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;';
            closeBtn.addEventListener('mouseover', function() {
                this.style.backgroundColor = '#4b5563';
            });
            closeBtn.addEventListener('mouseout', function() {
                this.style.backgroundColor = '#6b7280';
            });
            closeBtn.addEventListener('click', () => modal.remove());
            
            footer.appendChild(closeBtn);
            
            // Собираем все вместе
            content.appendChild(header);
            content.appendChild(body);
            content.appendChild(footer);
            modal.appendChild(content);
            
            // Добавляем в DOM
            document.body.appendChild(modal);
            
            // Закрытие по клику на фон
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }
    } catch (error) {
        console.error('Ошибка загрузки истории упражнения:', error);
    }
}

// Копирование плана в поля
function copyPlanToFields(exerciseId) {
    // Находим все поля для данного упражнения и заполняем их значениями из плана
    const inputs = document.querySelectorAll(`input[name$="_${exerciseId}"]`);
    inputs.forEach(input => {
        const fieldName = input.name.replace(`_${exerciseId}`, '');
        // Здесь можно добавить логику для заполнения полей значениями из плана
        // Пока что просто фокусируемся на поле
        input.focus();
    });
}
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
<!-- Модальное окно деталей упражнения -->
<div x-show="exerciseDetailModal.isOpen" 
     x-cloak
     style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 20px;">
    
    <!-- Фон для закрытия -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" @click="closeExerciseDetailModal()"></div>
    
    <!-- Модальное окно -->
    <div style="position: relative; background: white; border-radius: 12px; padding: 0; max-width: 800px; width: 100%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
        
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    <h3 style="margin: 0; font-size: 22px; font-weight: bold; color: #111827;" x-text="exerciseDetailModal.exercise?.name"></h3>
                    <div style="margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap;">
                        <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 12px; font-weight: 500;">
                            <span x-text="exerciseDetailModal.exercise?.category || '<?php echo e(__('common.not_specified')); ?>'"></span>
                        </span>
                        <span style="display: inline-flex; align-items: center; padding: 4px 12px; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 12px; font-weight: 500;">
                            <span x-text="exerciseDetailModal.exercise?.equipment || '<?php echo e(__('common.not_specified')); ?>'"></span>
                        </span>
                    </div>
                </div>
                <button @click="closeExerciseDetailModal()" style="background: #f3f4f6; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; color: #6b7280; font-size: 20px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; margin-left: 12px;">
                    <span style="line-height: 1;">&times;</span>
                </button>
            </div>
        </div>
        
        <!-- Контент с прокруткой -->
        <div style="padding: 20px; overflow-y: auto; flex: 1;">
            
            <!-- Изображения -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div x-show="exerciseDetailModal.exercise?.image_url && exerciseDetailModal.exercise.image_url !== 'null' && exerciseDetailModal.exercise.image_url !== null" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <img :src="exerciseDetailModal.exercise?.image_url && exerciseDetailModal.exercise.image_url !== 'null' && exerciseDetailModal.exercise.image_url !== null ? '/storage/' + exerciseDetailModal.exercise.image_url : ''" 
                         :alt="exerciseDetailModal.exercise?.name"
                         style="width: 100%; height: 280px; object-fit: cover;">
                    <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                        Фаза 1
                    </div>
                </div>
                <div x-show="exerciseDetailModal.exercise?.image_url_2 && exerciseDetailModal.exercise.image_url_2 !== 'null' && exerciseDetailModal.exercise.image_url_2 !== null" style="position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <img :src="exerciseDetailModal.exercise?.image_url_2 && exerciseDetailModal.exercise.image_url_2 !== 'null' && exerciseDetailModal.exercise.image_url_2 !== null ? '/storage/' + exerciseDetailModal.exercise.image_url_2 : ''" 
                         :alt="exerciseDetailModal.exercise?.name"
                         style="width: 100%; height: 280px; object-fit: cover;">
                    <div style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 500;">
                        Фаза 2
                    </div>
                </div>
            </div>
            
            <!-- Описание -->
            <div x-show="exerciseDetailModal.exercise?.description" style="margin-bottom: 24px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">
                    <?php echo e(__('common.description')); ?>

                </h4>
                <p style="color: #6b7280; margin: 0; line-height: 1.6;" x-text="exerciseDetailModal.exercise?.description"></p>
            </div>
            
            <!-- Инструкции -->
            <div x-show="exerciseDetailModal.exercise?.instructions" style="margin-bottom: 24px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">
                    <?php echo e(__('common.instructions')); ?>

                </h4>
                <div style="background: #f9fafb; border-left: 4px solid #6366f1; padding: 16px; border-radius: 8px;">
                    <p style="color: #374151; margin: 0; white-space: pre-line; line-height: 1.8;" x-text="exerciseDetailModal.exercise?.instructions"></p>
                </div>
            </div>
            
            <!-- Группы мышц -->
            <div x-show="exerciseDetailModal.exercise?.muscle_groups && exerciseDetailModal.exercise.muscle_groups.length > 0" style="margin-bottom: 24px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">
                    <?php echo e(__('common.muscle_groups')); ?>

                </h4>
                <div style="display: flex; flex-wrap: gap; gap: 8px;">
                    <template x-for="muscle in (exerciseDetailModal.exercise?.muscle_groups || [])" :key="muscle">
                        <span style="display: inline-flex; align-items: center; padding: 6px 14px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 9999px; font-size: 13px; font-weight: 500;" x-text="muscle"></span>
                    </template>
                </div>
            </div>
            
            <!-- Видео -->
            <div x-show="exerciseDetailModal.exercise?.video_url" style="margin-bottom: 24px;">
                <h4 style="font-size: 16px; font-weight: 600; color: #374151; margin: 0 0 12px 0;">
                    <?php echo e(__('common.video')); ?>

                </h4>
                <div x-show="isYouTubeUrl(exerciseDetailModal.exercise?.video_url)" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <iframe :src="getYouTubeEmbedUrl(exerciseDetailModal.exercise?.video_url)" 
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; border-radius: 12px;" 
                            allowfullscreen>
                    </iframe>
                </div>
                <div x-show="!isYouTubeUrl(exerciseDetailModal.exercise?.video_url)">
                    <a :href="exerciseDetailModal.exercise?.video_url" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                        <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                        <?php echo e(__('common.open_video')); ?>

                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make("crm.layouts.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/trainer/workouts/index.blade.php ENDPATH**/ ?>
// Drag and Drop функциональность для упражнений в тренировках
// Общий файл для тренера и self-athlete

// Глобальные переменные для drag and drop
let draggedExerciseId = null;
let draggedExerciseIndex = null;

// Обработчики событий drag and drop
function handleDragStart(event, exerciseId, exerciseIndex) {
    // Проверяем, что перетаскивание НЕ началось с кнопки "Удалить"
    if (event.target.closest('button[onclick*="removeExercise"]')) {
        event.preventDefault();
        return false;
    }
    
    // Очищаем предыдущее состояние перетаскивания
    cleanupDragState();
    
    draggedExerciseId = parseInt(exerciseId);
    draggedExerciseIndex = parseInt(exerciseIndex);
    
    // Добавляем класс для визуального эффекта
    event.target.classList.add('dragging');
    
    return true;
}

function handleDragOver(event, targetExerciseId, targetIndex) {
    event.preventDefault();
    return false;
}

function handleDragEnter(event, targetExerciseId, targetIndex) {
    event.preventDefault();
    const target = event.currentTarget;
    if (target && !target.classList.contains('drag-over')) {
        target.classList.add('drag-over');
    }
    return false;
}

function handleDragLeave(event, targetExerciseId, targetIndex) {
    const target = event.currentTarget;
    if (target) {
        target.classList.remove('drag-over');
    }
    return false;
}

function handleDrop(event, targetExerciseId, targetIndex) {
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
    
    // Находим индексы упражнений в DOM (не в массиве данных!)
    const draggedElement = document.querySelector(`[data-exercise-id="${draggedId}"]`);
    const targetElement = document.querySelector(`[data-exercise-id="${targetId}"]`);
    
    if (!draggedElement || !targetElement) {
        cleanupDragState();
        return;
    }
    
    // Получаем реальные позиции в DOM
    const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    const draggedIndex = Array.from(exerciseElements).indexOf(draggedElement);
    const targetIndexNum = Array.from(exerciseElements).indexOf(targetElement);
    
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
        const fieldData = currentFieldValues.find(f => f.exercise_id === exercise.id);
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
    
    cleanupDragState();
}

function cleanupDragState() {
    // Удаляем все классы drag-over
    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
    document.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
    
    // Очищаем переменные
    draggedExerciseId = null;
    draggedExerciseIndex = null;
    
    // Удаляем класс dragging
    document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
}

// Функция для привязки событий drag and drop к элементам
function bindDragDropEvents() {
    // Ищем элементы упражнений во всех возможных контейнерах
    const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id], .space-y-4 > div[data-exercise-id], [data-exercise-id][draggable="true"]');
    
    exerciseElements.forEach((element, index) => {
        const exerciseId = parseInt(element.dataset.exerciseId);
        
        // Обновляем data-exercise-index на реальный индекс в DOM
        element.setAttribute('data-exercise-index', index);
        
        // Удаляем старые обработчики, если есть
        element.removeEventListener('dragstart', element._dragStartHandler);
        element.removeEventListener('dragover', element._dragOverHandler);
        element.removeEventListener('drop', element._dropHandler);
        element.removeEventListener('dragenter', element._dragEnterHandler);
        element.removeEventListener('dragleave', element._dragLeaveHandler);
        element.removeEventListener('dragend', element._dragEndHandler);
        
        // Создаем новые обработчики
        element._dragStartHandler = (event) => {
            const currentIndex = parseInt(element.dataset.exerciseIndex);
            handleDragStart(event, exerciseId, currentIndex);
        };
        
        element._dragOverHandler = (event) => {
            const currentIndex = parseInt(element.dataset.exerciseIndex);
            handleDragOver(event, exerciseId, currentIndex);
        };
        
        element._dropHandler = (event) => {
            const currentIndex = parseInt(element.dataset.exerciseIndex);
            handleDrop(event, exerciseId, currentIndex);
        };
        
        element._dragEnterHandler = (event) => {
            const currentIndex = parseInt(element.dataset.exerciseIndex);
            handleDragEnter(event, exerciseId, currentIndex);
        };
        
        element._dragLeaveHandler = (event) => {
            const currentIndex = parseInt(element.dataset.exerciseIndex);
            handleDragLeave(event, exerciseId, currentIndex);
        };
        
        element._dragEndHandler = (event) => {
            cleanupDragState();
        };
        
        // Привязываем новые обработчики
        element.addEventListener('dragstart', element._dragStartHandler);
        element.addEventListener('dragover', element._dragOverHandler);
        element.addEventListener('drop', element._dropHandler);
        element.addEventListener('dragenter', element._dragEnterHandler);
        element.addEventListener('dragleave', element._dragLeaveHandler);
        element.addEventListener('dragend', element._dragEndHandler);
    });
}

// Экспортируем функции для использования в других файлах
window.WorkoutDragDrop = {
    handleDragStart,
    handleDragOver,
    handleDrop,
    handleDragEnter,
    handleDragLeave,
    cleanupDragState,
    bindDragDropEvents
};

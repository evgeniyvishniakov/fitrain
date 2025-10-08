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
    
    // Включаем прокрутку страницы при перетаскивании
    enableScrollDuringDrag();
    
    // Находим родительский блок упражнения (не саму иконку)
    let exerciseContainer = event.target.closest('[data-exercise-id]');
    
    // Если мы нашли саму иконку drag handle, ищем её родительский блок упражнения
    if (exerciseContainer && exerciseContainer.hasAttribute('draggable')) {
        // Ищем родительский блок, который содержит весь контент упражнения
        exerciseContainer = exerciseContainer.parentElement.closest('[data-exercise-id]');
    }
    
    if (exerciseContainer) {
        // Добавляем класс для визуального эффекта на оригинальный блок
        exerciseContainer.classList.add('dragging');
        
        // ПРИМЕНЯЕМ ПРЯМЫЕ СТИЛИ ДЛЯ УМЕНЬШЕНИЯ И ЗАТЕМНЕНИЯ (как в self-athlete)
        exerciseContainer.style.setProperty('opacity', '0.5', 'important');
        exerciseContainer.style.setProperty('transform', 'scale(0.95)', 'important');
        exerciseContainer.style.setProperty('transition', 'all 0.2s ease', 'important');
        
    } else {
        // Fallback - добавляем класс на target
        event.target.classList.add('dragging');
        event.target.style.setProperty('opacity', '0.5', 'important');
        event.target.style.setProperty('transform', 'scale(0.95)', 'important');
        event.target.style.setProperty('transition', 'all 0.2s ease', 'important');
    }
    
    return true;
}

function handleDragEnd(event, exerciseId, exerciseIndex) {
    // Очищаем состояние перетаскивания
    cleanupDragState();
}

function handleDragOver(event, targetExerciseId, targetIndex) {
    event.preventDefault();
    
    // Прокрутка страницы при перетаскивании
    if (draggedExerciseId !== null) {
        const scrollThreshold = 150; // Увеличиваем зону прокрутки
        const scrollSpeed = 15; // Увеличиваем скорость прокрутки
        
        // Прокрутка вверх
        if (event.clientY < scrollThreshold) {
            window.scrollBy(0, -scrollSpeed);
        }
        // Прокрутка вниз
        else if (event.clientY > window.innerHeight - scrollThreshold) {
            window.scrollBy(0, scrollSpeed);
        }
        
        // Также пробуем прокрутку по горизонтали
        const horizontalThreshold = 150;
        if (event.clientX < horizontalThreshold) {
            window.scrollBy(-scrollSpeed, 0);
        } else if (event.clientX > window.innerWidth - horizontalThreshold) {
            window.scrollBy(scrollSpeed, 0);
        }
    }
    
    return false;
}

function handleDragEnter(event, targetExerciseId, targetIndex) {
    event.preventDefault();
    event.stopPropagation();
    const target = event.currentTarget;
    if (target && !target.classList.contains('drag-over')) {
        // Убираем подсветку с других элементов
        document.querySelectorAll('.drag-over').forEach(el => {
            if (el !== target) {
                el.classList.remove('drag-over');
                // Убираем прямые стили ТОЛЬКО если это НЕ перетаскиваемый элемент
                if (!el.classList.contains('dragging')) {
                    el.style.borderColor = '';
                    el.style.backgroundColor = '';
                    el.style.boxShadow = '';
                    el.style.transform = ''; // УБИРАЕМ УВЕЛИЧЕНИЕ
                }
            }
        });
        target.classList.add('drag-over');
        
        // ПРИМЕНЯЕМ СТИЛИ НАПРЯМУЮ ЧЕРЕЗ JAVASCRIPT
        target.style.borderColor = '#4f46e5';
        target.style.backgroundColor = '#f0f9ff';
        target.style.boxShadow = '0 0 0 2px #e0e7ff';
        target.style.transform = 'scale(1.02)'; // УВЕЛИЧИВАЕМ ПРИ НАВЕДЕНИИ
    }
    return false;
}

function handleDragLeave(event, targetExerciseId, targetIndex) {
    const target = event.currentTarget;
    if (target) {
        // Проверяем, что курсор действительно покинул элемент
        // Если relatedTarget является дочерним элементом, не убираем подсветку
        if (!target.contains(event.relatedTarget)) {
            target.classList.remove('drag-over');
            // Убираем прямые стили ТОЛЬКО если это НЕ перетаскиваемый элемент
            if (!target.classList.contains('dragging')) {
                target.style.borderColor = '';
                target.style.backgroundColor = '';
                target.style.boxShadow = '';
                target.style.transform = ''; // УБИРАЕМ УВЕЛИЧЕНИЕ
            }
        }
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
    
    // Получаем реальные позиции в DOM - ищем родительские элементы упражнений
    const exerciseContainers = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    const draggedContainer = draggedElement.closest('[data-exercise-id]');
    const targetContainer = targetElement.closest('[data-exercise-id]');
    
    const draggedIndex = Array.from(exerciseContainers).indexOf(draggedContainer);
    const targetIndexNum = Array.from(exerciseContainers).indexOf(targetContainer);
    
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
    // Удаляем все классы drag-over и прямые стили
    document.querySelectorAll('.drag-over').forEach(el => {
        el.classList.remove('drag-over');
        el.style.borderColor = '';
        el.style.backgroundColor = '';
        el.style.boxShadow = '';
    });
    document.querySelectorAll('.drop-target').forEach(el => el.classList.remove('drop-target'));
    
    // Очищаем переменные
    draggedExerciseId = null;
    draggedExerciseIndex = null;
    
    // Удаляем класс dragging и восстанавливаем стили
    document.querySelectorAll('.dragging').forEach(el => {
        el.classList.remove('dragging');
        // ВОССТАНАВЛИВАЕМ СТИЛИ (убираем уменьшение и затемнение)
        el.style.opacity = '1';
        el.style.transform = 'scale(1)';
        el.style.transition = '';
    });
    
    // Отключаем прокрутку страницы
    disableScrollDuringDrag();
}

// Включение прокрутки страницы при перетаскивании
function enableScrollDuringDrag() {
    // Добавляем обработчик на window для лучшей совместимости
    window.addEventListener('wheel', handleWheelDuringDrag, { passive: false, capture: true });
    document.addEventListener('wheel', handleWheelDuringDrag, { passive: false, capture: true });
}

// Отключение прокрутки страницы
function disableScrollDuringDrag() {
    window.removeEventListener('wheel', handleWheelDuringDrag, { capture: true });
    document.removeEventListener('wheel', handleWheelDuringDrag, { capture: true });
}

// Обработчик прокрутки колесиком мыши во время drag
function handleWheelDuringDrag(event) {
    if (draggedExerciseId !== null) {
        // Прокручиваем страницу с учетом скорости
        const scrollSpeed = event.deltaY * 0.5; // Уменьшаем скорость для более плавной прокрутки
        window.scrollBy(0, scrollSpeed);
        event.preventDefault();
        event.stopPropagation();
    }
}

// Функция для привязки событий drag and drop к элементам
function bindDragDropEvents() {
    // Ищем drag handle элементы (иконки с двумя полосочками)
    const dragHandles = document.querySelectorAll('[draggable="true"]');
    
    // Ищем все блоки упражнений для drop target
    const exerciseContainers = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
    
    
    // Привязываем события drag к handle элементам
    dragHandles.forEach((handle, index) => {
        const exerciseContainer = handle.closest('[data-exercise-id]');
        if (!exerciseContainer) return;
        
        const exerciseId = parseInt(exerciseContainer.dataset.exerciseId);
        
        // Обновляем data-exercise-index на реальный индекс в DOM
        const containerIndex = Array.from(exerciseContainers).indexOf(exerciseContainer);
        exerciseContainer.setAttribute('data-exercise-index', containerIndex);
        
        // Удаляем старые обработчики с handle, если есть
        handle.removeEventListener('dragstart', handle._dragStartHandler);
        handle.removeEventListener('dragend', handle._dragEndHandler);
        
        // Создаем обработчики для drag handle
        handle._dragStartHandler = (e) => handleDragStart(e, exerciseId, containerIndex);
        handle._dragEndHandler = (e) => handleDragEnd(e, exerciseId, containerIndex);
        
        // Привязываем обработчики к drag handle
        handle.addEventListener('dragstart', handle._dragStartHandler);
        handle.addEventListener('dragend', handle._dragEndHandler);
    });
    
    // Привязываем события drop ко всем контейнерам упражнений
    exerciseContainers.forEach((container, index) => {
        const exerciseId = parseInt(container.dataset.exerciseId);
        
        // Удаляем старые обработчики с контейнера, если есть
        container.removeEventListener('dragover', container._dragOverHandler);
        container.removeEventListener('drop', container._dropHandler);
        container.removeEventListener('dragenter', container._dragEnterHandler);
        container.removeEventListener('dragleave', container._dragLeaveHandler);
        
        // Создаем обработчики для drop target (контейнер)
        container._dragOverHandler = (e) => handleDragOver(e, exerciseId, index);
        container._dropHandler = (e) => handleDrop(e, exerciseId, index);
        container._dragEnterHandler = (e) => handleDragEnter(e, exerciseId, index);
        container._dragLeaveHandler = (e) => handleDragLeave(e, exerciseId, index);
        
        // Привязываем обработчики к контейнеру
        container.addEventListener('dragover', container._dragOverHandler);
        container.addEventListener('drop', container._dropHandler);
        container.addEventListener('dragenter', container._dragEnterHandler);
        container.addEventListener('dragleave', container._dragLeaveHandler);
        
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
    bindDragDropEvents,
    enableScrollDuringDrag,
    disableScrollDuringDrag
};

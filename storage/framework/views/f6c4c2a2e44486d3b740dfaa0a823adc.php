<?php $__env->startSection("title", __('common.workout_templates')); ?>
<?php $__env->startSection("page-title", __('common.workout_templates')); ?>

<style>
[x-cloak] { display: none !important; }

.template-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 1024px) {
    .template-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<script>
// SPA функциональность для шаблонов тренировок
function templatesApp() {
    return {
        currentView: 'list', // list, create, edit, view
        templates: <?php echo json_encode(\App\Models\Trainer\WorkoutTemplate::active()->with('creator')->get()->map(function($template) {
            $template->valid_exercises = $template->valid_exercises;
            return $template;
        }), 15, 512) ?>,
        currentTemplate: null,
        search: '',
        currentPage: 1,
        itemsPerPage: 4,
        
        // Поля формы
        formName: '',
        formExercises: [],
        
        // Работа с упражнениями
        availableExercises: [],
        exerciseSearch: '',
        exerciseCategory: '',
        exerciseEquipment: '',
        exerciseTypeFilter: '',
        exerciseCategoryOptions: [],
        exerciseEquipmentOptions: [],
        selectedExercises: [],
        showExerciseModal: false,
        exerciseFiltersApplied: false,
        exerciseEquipmentOptions: [],
        exerciseCategoryOptions: [],
        
        // Инициализация
        init() {
            // Инициализация компонента
            
            // Сбрасываем пагинацию при изменении фильтров
            this.$watch('search', () => {
                this.currentPage = 1;
            });
        },
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentTemplate = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentTemplate = null;
            this.formName = '';
            this.formExercises = [];
            this.selectedExercises = [];
            this.exerciseSearch = '';
            this.exerciseCategory = '';
            this.exerciseEquipment = '';
            this.exerciseTypeFilter = '';
            this.exerciseCategoryOptions = [];
            this.exerciseEquipmentOptions = [];
        },
        
        showEdit(templateId) {
            this.currentView = 'edit';
            this.currentTemplate = this.templates.find(t => t.id === templateId);
            this.formName = this.currentTemplate.name;
            this.formExercises = this.currentTemplate ? (this.currentTemplate.valid_exercises || this.currentTemplate.exercises || []) : [];
            this.selectedExercises = this.currentTemplate ? (this.currentTemplate.valid_exercises || this.currentTemplate.exercises || []) : [];
            this.exerciseSearch = '';
            this.exerciseCategory = '';
            this.exerciseEquipment = '';
            this.exerciseTypeFilter = '';
            this.exerciseCategoryOptions = [];
            this.exerciseEquipmentOptions = [];
            
            // Обновляем отображение выбранных упражнений
            this.updateSelectedExercisesDisplay(this.selectedExercises);
        },
        
        showView(templateId) {
            this.currentView = 'view';
            this.currentTemplate = this.templates.find(t => t.id === templateId);
            // Добавляем валидные упражнения для отображения
            if (this.currentTemplate) {
                this.currentTemplate.valid_exercises = this.currentTemplate.valid_exercises || this.currentTemplate.exercises || [];
            }
        },
        
        // Фильтрация
        get filteredTemplates() {
            let filtered = this.templates;
            
            if (this.search) {
                filtered = filtered.filter(t => 
                    t.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            const total = Math.ceil(this.filteredTemplates.length / this.itemsPerPage);
            return total > 0 ? total : 1;
        },
        
        get visiblePages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 5) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
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
        
        get paginatedTemplates() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredTemplates.slice(start, end);
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
        
        // Сохранение
        async saveTemplate() {
            try {
                const templateData = {
                    name: this.formName,
                    exercises: this.selectedExercises
                };
                
                const url = this.currentTemplate && this.currentTemplate.id ? 
                    `/workout-templates/${this.currentTemplate.id}` : '/workout-templates';
                const method = this.currentTemplate && this.currentTemplate.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(templateData)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    
                    // Показываем уведомление об успехе
                    this.showSuccessMessage('<?php echo e(__('common.template_saved')); ?>');
                    
                    if (method === 'POST' && result.template) {
                        // Если это создание нового шаблона, добавляем его в список
                        this.templates.unshift(result.template);
                    } else if (method === 'PUT' && result.template) {
                        // Если это редактирование, обновляем существующий шаблон в списке
                        const index = this.templates.findIndex(t => t.id === result.template.id);
                        if (index !== -1) {
                            this.templates[index] = result.template;
                        }
                    }
                    
                    // Переключаемся на список шаблонов
                    this.showList();
                    
                    // Очищаем форму
                    this.formName = '';
                    this.selectedExercises = [];
                    
                } else {
                    const error = await response.json();
                    this.showErrorMessage('<?php echo e(__('common.template_saving_error')); ?>');
                }
            } catch (error) {
                this.showErrorMessage('<?php echo e(__('common.template_saving_general_error')); ?>');
            }
        },
        
        // Удаление
        deleteTemplate(id) {
            const template = this.templates.find(t => t.id === id);
            const templateName = template ? template.name : '<?php echo e(__('common.this_template')); ?>';
            
            // Показываем модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete_template_title')); ?>',
                    message: `<?php echo e(__('common.are_you_sure_delete_template')); ?> "${templateName}"? <?php echo e(__('common.this_action_cannot_be_undone')); ?>`,
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
                    onConfirm: () => this.performDelete(id),
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/workout-templates/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    // Удаляем шаблон из списка
                    this.templates = this.templates.filter(t => t.id !== id);
                    
                    // Показываем уведомление об успехе
                    this.showSuccessMessage('<?php echo e(__('common.template_deleted')); ?>');
                    
                    // Если удалили все шаблоны на текущей странице, переходим на предыдущую
                    if (this.paginatedTemplates.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    const error = await response.json();
                    this.showErrorMessage('<?php echo e(__('common.template_deleting_error')); ?>');
                }
            } catch (error) {
                this.showErrorMessage('<?php echo e(__('common.template_deleting_general_error')); ?>');
            }
        },
        
        // Вспомогательные методы
        getCategoryLabel(category) {
            const categories = {
                'strength': '<?php echo e(__('common.strength')); ?>',
                'cardio': '<?php echo e(__('common.cardio')); ?>',
                'flexibility': '<?php echo e(__('common.flexibility')); ?>',
                'mixed': '<?php echo e(__('common.mixed')); ?>'
            };
            return categories[category] || category;
        },
        
        getDifficultyLabel(difficulty) {
            const difficulties = {
                'beginner': '<?php echo e(__('common.beginner')); ?>',
                'intermediate': '<?php echo e(__('common.intermediate')); ?>',
                'advanced': '<?php echo e(__('common.advanced')); ?>'
            };
            return difficulties[difficulty] || difficulty;
        },
        
        // Методы для работы с упражнениями
        get filteredExercises() {
            let filtered = this.availableExercises;
            
            if (this.exerciseSearch) {
                filtered = filtered.filter(ex => 
                    ex.name.toLowerCase().includes(this.exerciseSearch.toLowerCase())
                );
            }
            
            if (this.exerciseCategory) {
                filtered = filtered.filter(ex => ex.category === this.exerciseCategory);
            }
            
            if (this.exerciseEquipment) {
                filtered = filtered.filter(ex => ex.equipment === this.exerciseEquipment);
            }

            if (this.exerciseTypeFilter) {
                if (this.exerciseTypeFilter === 'favorite') {
                    filtered = filtered.filter(ex => ex.is_favorite);
                } else {
                    filtered = filtered.filter(ex => ex.exercise_type === this.exerciseTypeFilter);
                }
            }

            if (!this.exerciseFiltersApplied && filtered.length > 0) {
                const categorySet = new Set();
                const equipmentSet = new Set();
                filtered.forEach(ex => {
                    if (ex.category && ex.category !== 'null') {
                        categorySet.add(ex.category);
                    }
                    if (ex.equipment && ex.equipment !== 'null' && ex.equipment !== null) {
                        equipmentSet.add(ex.equipment);
                    }
                });
                this.exerciseCategoryOptions = Array.from(categorySet).sort((a, b) => a.localeCompare(b, 'ru'));
                this.exerciseEquipmentOptions = Array.from(equipmentSet).sort((a, b) => a.localeCompare(b, 'ru'));
                this.exerciseFiltersApplied = true;
            }
            
            return filtered;
        },
        
        // Работа с модальным окном упражнений
        async openExerciseModal() {
            try {
                const response = await fetch('/exercises/api');
                const data = await response.json();
                this.availableExercises = data.exercises || [];
                this.exerciseFiltersApplied = false;
                this.exerciseTypeFilter = '';
                this.exerciseCategory = '';
                this.exerciseEquipment = '';

                const categorySet = new Set();
                const equipmentSet = new Set();

                this.availableExercises.forEach(ex => {
                    if (ex.category && ex.category !== 'null') {
                        categorySet.add(ex.category);
                    }
                    if (ex.equipment && ex.equipment !== 'null') {
                        equipmentSet.add(ex.equipment);
                    }
                });

                this.exerciseCategoryOptions = Array.from(categorySet).sort((a, b) => a.localeCompare(b, 'ru'));
                this.exerciseEquipmentOptions = Array.from(equipmentSet).sort((a, b) => a.localeCompare(b, 'ru'));
            } catch (error) {
                this.availableExercises = [];
                this.exerciseCategoryOptions = [];
                this.exerciseEquipmentOptions = [];
            }
            this.showExerciseModal = true;
            
            // Принудительно показываем модальное окно
            setTimeout(() => {
                const modal = document.querySelector('[x-show="showExerciseModal"]');
                if (modal) {
                modal.style.display = 'flex';
                } else {
                    // Создаем модальное окно через JavaScript
                    this.createModalWithJS();
                }
            }, 100);
        },
        
        closeExerciseModal() {
            this.showExerciseModal = false;
            
            // Принудительно скрываем модальное окно
            const modal = document.querySelector('[x-show="showExerciseModal"]');
            if (modal) {
                modal.style.display = 'none';
            }
            
            // Также скрываем JavaScript модальное окно, если есть
            const jsModal = document.getElementById('js-exercise-modal');
            if (jsModal) {
            jsModal.remove();
            }
        },
        
        createModalWithJS() {
            
            // Удаляем существующее модальное окно, если есть
            const existingModal = document.getElementById('js-exercise-modal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Создаем модальное окно
            const modal = document.createElement('div');
            modal.id = 'js-exercise-modal';
            modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;';
            
            modal.innerHTML = `
                <div style="background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 18px; font-weight: 600; color: #111827;"><?php echo e(__('common.select_exercises')); ?></h3>
                        <button onclick="document.getElementById('js-exercise-modal').remove()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
                    </div>
                    <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                            ${this.availableExercises.map(exercise => `
                                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s;" 
                                     onclick="this.style.background = this.style.background === 'rgb(239, 246, 255)' ? 'white' : 'rgb(239, 246, 255)'; this.style.borderColor = this.style.borderColor === 'rgb(147, 197, 253)' ? '#e5e7eb' : 'rgb(147, 197, 253)';">
                                    <h4 style="font-weight: 500; color: #111827; margin-bottom: 8px;">${exercise.name}</h4>
                                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">${exercise.category}</p>
                                    <p style="font-size: 14px; color: #9ca3af;">${exercise.equipment}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
                        <button onclick="document.getElementById('js-exercise-modal').remove()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.cancel')); ?></button>
                        <button onclick="document.getElementById('js-exercise-modal').remove()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;"><?php echo e(__('common.done')); ?></button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        },
        
        toggleExercise(exercise) {
            const index = this.selectedExercises.findIndex(ex => ex.id === exercise.id);
            if (index > -1) {
                // Удаляем упражнение
                this.selectedExercises.splice(index, 1);
            } else {
                // Добавляем упражнение
                    this.selectedExercises.push({
                        id: exercise.id,
                        name: exercise.name,
                        category: exercise.category,
                        equipment: exercise.equipment,
                        muscle_groups: exercise.muscle_groups
                    });
            }
        },
        
        removeExerciseFromTemplate(exerciseId) {
            this.selectedExercises = this.selectedExercises.filter(ex => ex.id !== exerciseId);
        },
        
        moveExerciseUp(index) {
            if (index > 0) {
                const exercise = this.selectedExercises.splice(index, 1)[0];
                this.selectedExercises.splice(index - 1, 0, exercise);
            }
        },
        
        moveExerciseDown(index) {
            if (index < this.selectedExercises.length - 1) {
                const exercise = this.selectedExercises.splice(index, 1)[0];
                this.selectedExercises.splice(index + 1, 0, exercise);
            }
        },
        
        // Уведомления
        showSuccessMessage(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'success',
                    title: '<?php echo e(__('common.success')); ?>',
                    message: message
                }
            }));
        },
        
        showErrorMessage(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'error',
                    title: 'Ошибка',
                    message: message
                }
            }));
        },
        
        showInfoMessage(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'info',
                    title: '<?php echo e(__('common.information')); ?>',
                    message: message
                }
            }));
        },
        
        showWarningMessage(message) {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'warning',
                    title: '<?php echo e(__('common.warning')); ?>',
                    message: message
                }
            }));
        },
        
        // Обновление отображения выбранных упражнений
        updateSelectedExercisesDisplay(exercises) {
            const container = document.querySelector('[x-show="selectedExercises.length > 0"]');
            if (!container) return;
            
            const parent = container.parentElement;
            const emptyState = parent.querySelector('[x-show="selectedExercises.length === 0"]');
            
            if (exercises.length > 0) {
                // Скрываем пустое состояние
                if (emptyState) emptyState.style.display = 'none';
                
                // Показываем контейнер с упражнениями
                container.style.display = 'block';
                
                // Обновляем содержимое
                container.innerHTML = exercises.map((exercise, index) => `
                    <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                            <span class="font-medium text-gray-900">${exercise.name}</span>
                            <span class="text-sm text-gray-600">(${exercise.category} • ${exercise.equipment})</span>
                        </div>
                        <button type="button" onclick="removeExerciseFromAlpine(${exercise.id})" class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `).join('');
            } else {
                // Показываем пустое состояние
                if (emptyState) emptyState.style.display = 'block';
                container.style.display = 'none';
            }
        }
        
    }
}

// Простая функция для открытия модального окна
        async function openSimpleModal() {
            let exercises = [];
            try {
                const response = await fetch('/exercises/api', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                const rawExercises = data.exercises || [];
                exercises = rawExercises.map(exercise => {
                    const exerciseType = exercise.exercise_type || (exercise.is_system ? 'system' : 'custom');
                    return {
                        ...exercise,
                        exercise_type: exerciseType,
                        is_favorite: !!exercise.is_favorite
                    };
                });
            } catch (error) {
                exercises = [];
            }
    
    window.simpleModalExercises = exercises;
    
    // Создаем модальное окно
    const modal = document.createElement('div');
    modal.id = 'simple-exercise-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    modal.innerHTML = `
        <div style="
            background: white;
            border-radius: 8px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 80%;
            max-height: 80%;
            width: 100%;
            overflow: hidden;
        ">
            <div style="
                padding: 20px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            ">
                <h3 style="font-size: 18px; font-weight: 600; color: #111827;">Выбор упражнений</h3>
                <button onclick="closeSimpleModal()" style="
                    color: #6b7280;
                    background: none;
                    border: none;
                    font-size: 24px;
                    cursor: pointer;
                    padding: 0;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">×</button>
            </div>
            <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
                <!-- Поиск и фильтры в одну линию -->
                <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                    <!-- Поиск -->
                    <input type="text" 
                           id="exercise-search" 
                           placeholder="<?php echo e(__('common.search_exercises')); ?>" 
                           style="
                               flex: 1;
                               min-width: 200px;
                               padding: 12px 16px;
                               border: 1px solid #d1d5db;
                               border-radius: 8px;
                               font-size: 14px;
                               outline: none;
                               transition: border-color 0.2s;
                           "
                           onkeyup="filterExercises()"
                           onfocus="this.style.borderColor = '#4f46e5'"
                           onblur="this.style.borderColor = '#d1d5db'">
                    
                    <!-- Фильтр категории -->
                    <select id="category-filter" 
                            onchange="filterExercises()"
                            style="
                                min-width: 150px;
                                padding: 12px 16px;
                                border: 1px solid #d1d5db;
                                border-radius: 8px;
                                font-size: 14px;
                                outline: none;
                                background: white;
                                transition: border-color 0.2s;
                            "
                            onfocus="this.style.borderColor = '#4f46e5'"
                            onblur="this.style.borderColor = '#d1d5db'">
                        <option value=""><?php echo e(__('common.all_categories')); ?></option>
                    </select>
                    
                    <!-- Фильтр оборудования -->
                    <select id="equipment-filter" 
                            onchange="filterExercises()"
                            style="
                                min-width: 150px;
                                padding: 12px 16px;
                                border: 1px solid #d1d5db;
                                border-radius: 8px;
                                font-size: 14px;
                                font-weight: 600;
                                outline: none;
                                background: white;
                                transition: border-color 0.2s;
                            "
                            onfocus="this.style.borderColor = '#4f46e5'"
                            onblur="this.style.borderColor = '#d1d5db'">
                        <option value=""><?php echo e(__('common.all_equipment')); ?></option>
                    </select>

                    <!-- Фильтр типа упражнений -->
                    <select id="type-filter"
                            onchange="filterExercises()"
                            style="
                                min-width: 160px;
                                padding: 12px 16px;
                                border: 1px solid #d1d5db;
                                border-radius: 8px;
                                font-size: 14px;
                                font-weight: 600;
                                outline: none;
                                background: white;
                                transition: border-color 0.2s;
                            "
                            onfocus="this.style.borderColor = '#4f46e5'"
                            onblur="this.style.borderColor = '#d1d5db'">
                        <option value=""><?php echo e(__('common.all_exercises')); ?></option>
                        <option value="system"><?php echo e(__('common.system_exercises')); ?></option>
                        <option value="custom"><?php echo e(__('common.user_exercises')); ?></option>
                        <option value="favorite"><?php echo e(__('common.favorite_exercises')); ?></option>
                    </select>
                </div>
                
                <div id="exercises-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                    ${exercises.map(exercise => {
                        // Функция для получения изображения с приоритетом
                        function getDisplayImage(exercise) {
                            const isGlutes = exercise.category === 'Ягодицы';
                            
                            if (isGlutes) {
                                // Для категории "Ягодицы" - сначала женское
                                if (exercise.image_url_female && typeof exercise.image_url_female === 'string' && exercise.image_url_female.trim() !== '' && exercise.image_url_female !== 'null') {
                                    return exercise.image_url_female;
                                }
                                // Если женского нет, проверяем мужское
                                if (exercise.image_url && typeof exercise.image_url === 'string' && exercise.image_url.trim() !== '' && exercise.image_url !== 'null') {
                                    return exercise.image_url;
                                }
                            } else {
                                // Для остальных категорий - сначала мужское
                                if (exercise.image_url && typeof exercise.image_url === 'string' && exercise.image_url.trim() !== '' && exercise.image_url !== 'null') {
                                    return exercise.image_url;
                                }
                                // Если мужского нет, проверяем женское
                                if (exercise.image_url_female && typeof exercise.image_url_female === 'string' && exercise.image_url_female.trim() !== '' && exercise.image_url_female !== 'null') {
                                    return exercise.image_url_female;
                                }
                            }
                            return null;
                        }
                        
                        const displayImage = getDisplayImage(exercise);
                        const imageUrl = displayImage ? `/storage/${displayImage}` : '';
                        
                        // Экранируем специальные символы
                        const escapeName = (exercise.name || '').replace(/'/g, "\\'");
                        const escapeCategory = (exercise.category || '').replace(/'/g, "\\'");
                        const escapeEquipment = (exercise.equipment || '').replace(/'/g, "\\'");
                        const attrName = (exercise.name || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        const attrCategory = (exercise.category || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        const attrEquipment = (exercise.equipment || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        const attrType = (exercise.exercise_type || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        const attrFavorite = exercise.is_favorite ? '1' : '0';
                        
                        return `
                            <div style="
                                border: 1px solid #e5e7eb;
                                border-radius: 10px;
                                padding: 16px;
                                cursor: pointer;
                                background: white;
                                display: flex;
                                flex-direction: row;
                                align-items: flex-start;
                                gap: 14px;
                                max-width: 100%;
                                box-sizing: border-box;
                                min-height: 160px;
                            " 
                            data-exercise-id="${exercise.id}"
                            data-exercise-name="${attrName}"
                            data-exercise-category="${attrCategory}"
                            data-exercise-equipment="${attrEquipment}"
                            data-exercise-type="${attrType}"
                            data-exercise-favorite="${attrFavorite}"
                            onclick="toggleExercise(this, ${exercise.id}, '${escapeName}', '${escapeCategory}', '${escapeEquipment}')">
                                ${imageUrl ? `
                                    <img src="${imageUrl}" 
                                         alt="${escapeName}" 
                                         style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
                                ` : ''}
                                <div style="flex: 1; min-width: 0;">
                                    <h4 style="font-weight: 600; color: #111827; margin-bottom: 5px; font-size: 15px; word-wrap: break-word; line-height: 1.3;">${exercise.name}</h4>
                                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 3px;">${exercise.category}</p>
                                    <p style="font-size: 13px; color: #9ca3af;">${exercise.equipment}</p>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
                
                <!-- Сообщение о пустых результатах -->
                <div id="no-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                    <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                    <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">Упражнения не найдены</h3>
                    <p style="font-size: 14px;">Попробуйте изменить параметры поиска</p>
                </div>
            </div>
            <div style="
                padding: 20px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            ">
                <button onclick="closeSimpleModal()" style="
                    padding: 8px 16px;
                    background: #f3f4f6;
                    color: #374151;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    cursor: pointer;
                "><?php echo e(__('common.cancel')); ?></button>
                <button onclick="saveSelectedExercises()" style="
                    padding: 8px 16px;
                    background: #4f46e5;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                "><?php echo e(__('common.done')); ?></button>
            </div>
        </div>
    `;
    
    // Добавляем обработчик клика на фон
    modal.onclick = function(e) {
        if (e.target === modal) {
            closeSimpleModal();
        }
    };
    
            document.body.appendChild(modal);
            populateSimpleModalFilters();
            filterExercises();
        }

// Функция закрытия модального окна
function closeSimpleModal() {
    const modal = document.getElementById('simple-exercise-modal');
    if (modal) {
        modal.remove();
    }
}

// Функция переключения упражнения
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

// Функция фильтрации упражнений
        function filterExercises() {
            const searchInput = document.getElementById('exercise-search');
            const categorySelect = document.getElementById('category-filter');
            const equipmentSelect = document.getElementById('equipment-filter');
            const typeSelect = document.getElementById('type-filter');
            const container = document.getElementById('exercises-container');
            const noResults = document.getElementById('no-results');

            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const category = categorySelect ? categorySelect.value : '';
            const equipment = equipmentSelect ? equipmentSelect.value : '';
            const typeFilter = typeSelect ? typeSelect.value : '';
            const exercises = window.simpleModalExercises || [];

            if (equipmentSelect) {
                const prevEquipment = equipment;
                const equipmentSet = new Set();
                exercises.forEach(ex => {
                    const exType = ex.exercise_type || (ex.is_system ? 'system' : 'custom');
                    const matchesType = !typeFilter || (typeFilter === 'favorite' ? !!ex.is_favorite : exType === typeFilter);
                    if ((!category || ex.category === category) && matchesType) {
                        if (ex.equipment && ex.equipment !== 'null' && ex.equipment !== null) {
                            equipmentSet.add(ex.equipment);
                        }
                    }
                });

                const desiredOptions = [''].concat(Array.from(equipmentSet).sort((a, b) => a.localeCompare(b, 'ru')));
                const currentOptions = Array.from(equipmentSelect.options || []).map(option => option.value);

                if (JSON.stringify(currentOptions) !== JSON.stringify(desiredOptions)) {
                    equipmentSelect.innerHTML = '';
                    const emptyOpt = document.createElement('option');
                    emptyOpt.value = '';
                    emptyOpt.textContent = '<?php echo e(__('common.all_equipment')); ?>';
                    equipmentSelect.appendChild(emptyOpt);
                    desiredOptions.slice(1).forEach(eq => {
                        const opt = document.createElement('option');
                        opt.value = eq;
                        opt.textContent = eq;
                        equipmentSelect.appendChild(opt);
                    });
                }

                if (desiredOptions.includes(prevEquipment)) {
                    equipmentSelect.value = prevEquipment;
                } else {
                    equipmentSelect.value = '';
                }
            }

            if (!container) {
                return;
            }

            let visibleCount = 0;
            const cards = container.querySelectorAll('[data-exercise-id]');
            cards.forEach(card => {
                const name = (card.dataset.exerciseName || '').toLowerCase().trim();
                const elementCategory = card.dataset.exerciseCategory || '';
                const elementEquipment = card.dataset.exerciseEquipment || '';
                const elementType = card.dataset.exerciseType || '';
                const elementFavorite = card.dataset.exerciseFavorite === '1';

                const matchesSearch = !searchTerm || name.includes(searchTerm);
                const matchesCategory = !category || elementCategory === category;
                const matchesEquipment = !equipment || elementEquipment === equipment;
                const matchesType = !typeFilter || (typeFilter === 'favorite' ? elementFavorite : elementType === typeFilter);

                if (matchesSearch && matchesCategory && matchesEquipment && matchesType) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        function populateSimpleModalFilters() {
            const exercises = window.simpleModalExercises || [];
            const categorySelect = document.getElementById('category-filter');
            const equipmentSelect = document.getElementById('equipment-filter');

            if (!categorySelect || !equipmentSelect) {
                return;
            }

            const prevCategory = categorySelect.value;
            const prevEquipment = equipmentSelect.value;

            const categorySet = new Set();
            const equipmentSet = new Set();

            exercises.forEach(ex => {
                if (ex.category && ex.category !== 'null') {
                    categorySet.add(ex.category);
                }
                if (ex.equipment && ex.equipment !== 'null' && ex.equipment !== null) {
                    equipmentSet.add(ex.equipment);
                }
            });

            const categoryOptions = Array.from(categorySet).sort((a, b) => a.localeCompare(b, 'ru'));
            const equipmentOptions = Array.from(equipmentSet).sort((a, b) => a.localeCompare(b, 'ru'));

            categorySelect.innerHTML = '';
            const defaultCategory = document.createElement('option');
            defaultCategory.value = '';
            defaultCategory.textContent = '<?php echo e(__('common.all_categories')); ?>';
            categorySelect.appendChild(defaultCategory);
            categoryOptions.forEach(category => {
                const option = document.createElement('option');
                option.value = category;
                option.textContent = category;
                categorySelect.appendChild(option);
            });

            equipmentSelect.innerHTML = '';
            const defaultEquipment = document.createElement('option');
            defaultEquipment.value = '';
            defaultEquipment.textContent = '<?php echo e(__('common.all_equipment')); ?>';
            equipmentSelect.appendChild(defaultEquipment);
            equipmentOptions.forEach(eq => {
                const option = document.createElement('option');
                option.value = eq;
                option.textContent = eq;
                equipmentSelect.appendChild(option);
            });

            if (prevCategory && categoryOptions.includes(prevCategory)) {
                categorySelect.value = prevCategory;
            }

            if (prevEquipment && equipmentOptions.includes(prevEquipment)) {
                equipmentSelect.value = prevEquipment;
            }
        }

// Функция сохранения выбранных упражнений
function saveSelectedExercises() {
    const selectedElements = document.querySelectorAll('#simple-exercise-modal [data-selected="true"]');
    const selectedExercises = Array.from(selectedElements).map(el => ({
        id: parseInt(el.dataset.exerciseId),
        name: el.dataset.exerciseName,
        category: el.dataset.exerciseCategory,
        equipment: el.dataset.exerciseEquipment
    }));
    
    // Обновляем Alpine.js данные
    const alpineData = Alpine.store ? Alpine.store('templatesApp') : null;
    if (alpineData) {
        alpineData.selectedExercises = selectedExercises;
    } else {
        // Если Alpine.js не доступен, ищем элемент с x-data
        const alpineElement = document.querySelector('[x-data*="templatesApp"]');
        if (alpineElement && alpineElement._x_dataStack) {
            const data = alpineElement._x_dataStack[0];
            data.selectedExercises = selectedExercises;
        }
    }
    
    // Обновляем отображение выбранных упражнений
    updateSelectedExercisesDisplay(selectedExercises);
    
    closeSimpleModal();
}

// Функция обновления отображения выбранных упражнений
function updateSelectedExercisesDisplay(exercises) {
    const container = document.querySelector('[x-show="selectedExercises.length > 0"]');
    if (!container) return;
    
    const parent = container.parentElement;
    const emptyState = parent.querySelector('[x-show="selectedExercises.length === 0"]');
    
    if (exercises.length > 0) {
        // Скрываем пустое состояние
        if (emptyState) emptyState.style.display = 'none';
        
        // Показываем контейнер с упражнениями
        container.style.display = 'block';
        
        // Обновляем содержимое
        container.innerHTML = exercises.map((exercise, index) => `
            <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                    <span class="font-medium text-gray-900">${exercise.name}</span>
                    <span class="text-sm text-gray-600">(${exercise.category} • ${exercise.equipment})</span>
                </div>
                <button type="button" onclick="removeExercise(${exercise.id})" class="text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');
    } else {
        // Показываем пустое состояние
        if (emptyState) emptyState.style.display = 'block';
        container.style.display = 'none';
    }
}

// Функция удаления упражнения
function removeExercise(exerciseId) {
    const alpineElement = document.querySelector('[x-data*="templatesApp"]');
    if (alpineElement && alpineElement._x_dataStack) {
        const data = alpineElement._x_dataStack[0];
        data.selectedExercises = data.selectedExercises.filter(ex => ex.id !== exerciseId);
        updateSelectedExercisesDisplay(data.selectedExercises);
    }
}

// Функция удаления упражнения из Alpine.js (для кнопок в форме)
function removeExerciseFromAlpine(exerciseId) {
    const alpineElement = document.querySelector('[x-data*="templatesApp"]');
    if (alpineElement && alpineElement._x_dataStack) {
        const data = alpineElement._x_dataStack[0];
        data.selectedExercises = data.selectedExercises.filter(ex => ex.id !== exerciseId);
        data.updateSelectedExercisesDisplay(data.selectedExercises);
    }
}

</script>

<?php $__env->startSection("header-actions"); ?>
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>
<div x-data="templatesApp()" x-cloak class="space-y-6">
    
    <!-- Фильтры и поиск -->
    <div x-show="currentView === 'list'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <style>
                .filters-row {
                    display: flex !important;
                    flex-direction: column !important;
                    gap: 1rem !important;
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
                    .filters-row .filter-container {
                        width: 200px !important;
                    }
                    .filters-row .buttons-container {
                        display: flex !important;
                        flex-direction: row !important;
                        gap: 0.75rem !important;
                        flex-shrink: 0 !important;
                    }
                }
            </style>
            <div class="filters-row">
                <!-- Поиск -->
                <div class="search-container">
                    <input type="text" 
                           x-model="search" 
                           placeholder="<?php echo e(__('common.search_templates')); ?>" 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    <?php if(auth()->user()->hasRole('trainer')): ?>
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            <?php echo e(__('common.add_template')); ?>

                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">Активные фильтры:</span>
                <span x-show="search" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <?php echo e(__('common.search')); ?>: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                </span>
            </div>
        </div>
    </div>

    <!-- Список шаблонов -->
    <div x-show="currentView === 'list'" class="space-y-6">
        <div x-show="paginatedTemplates.length > 0" class="template-grid">
            <template x-for="template in paginatedTemplates" :key="template.id">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 p-6">
                    <!-- Заголовок -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                                <span x-text="template.name"></span>
                            </h3>
                            
                            <!-- Список упражнений -->
                            <div class="space-y-2 mb-4">
                                <template x-for="(exercise, index) in ((template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises : (template.exercises || [])).slice(0, 5)" :key="exercise.id">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <span class="text-indigo-600 mr-2">•</span>
                                        <span x-text="exercise.name"></span>
                                    </div>
                                </template>
                                <div x-show="((template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises.length : (template.exercises || []).length) > 5" class="text-sm text-gray-500 italic">
                                    + <span x-text="((template.valid_exercises && template.valid_exercises.length > 0) ? template.valid_exercises.length : (template.exercises || []).length) - 5"></span> <?php echo e(__('common.more')); ?>...
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex space-x-2">
                        <button @click="showView(template.id)" class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            <?php echo e(__('common.view')); ?>

                        </button>
                        <?php if(auth()->user()->hasRole('trainer')): ?>
                            <button @click="showEdit(template.id)" class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                <?php echo e(__('common.edit')); ?>

                            </button>
                            <button @click="deleteTemplate(template.id)" class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                <?php echo e(__('common.delete')); ?>

                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Пустое состояние -->
        <div x-show="paginatedTemplates.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">📋</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo e(__('common.no_templates')); ?></h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto"><?php echo e(__('common.create_workout_template')); ?></p>
            <?php if(auth()->user()->hasRole('trainer')): ?>
                <button @click="showCreate()" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <?php echo e(__('common.create_first_template')); ?>

                </button>
            <?php endif; ?>
        </div>
        
        <!-- Пагинация -->
        <div x-show="paginatedTemplates.length > 0 && totalPages > 1" class="mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-2">
                        <button @click="previousPage()" 
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <template x-for="page in visiblePages" :key="page">
                            <button @click="goToPage(page)" 
                                    :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'"
                                    class="px-3 py-2 text-sm font-medium border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <span x-text="page"></span>
                            </button>
                        </template>
                        
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
    </div>

    <!-- Форма создания/редактирования -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900" x-text="currentView === 'create' ? '<?php echo e(__('common.create_template')); ?>' : '<?php echo e(__('common.edit_template')); ?>'"></h2>
                <p class="mt-2 text-gray-600" x-text="currentView === 'create' ? 'Добавьте новый шаблон тренировки' : 'Внесите изменения в шаблон'"></p>
            </div>
            <button type="button" @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back_to_list')); ?>

            </button>
        </div>
        
        <form @submit.prevent="saveTemplate()" class="space-y-6">
            <div class="space-y-6">
                <!-- Название -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.template_name')); ?> *</label>
                    <input type="text" 
                           x-model="formName" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Выбор упражнений -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.exercises_in_template')); ?></h3>
                        <div class="flex space-x-2">
                            <button type="button"
                                    onclick="openSimpleModal()"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <?php echo e(__('common.add_exercises')); ?>

                            </button>
                        </div>
                    </div>
                    
                    <!-- Выбранные упражнения -->
                    <div x-show="selectedExercises.length > 0" class="space-y-2">
                        <template x-for="(exercise, index) in selectedExercises" :key="exercise.id">
                            <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-indigo-600 font-medium" x-text="(index + 1) + '.'"></span>
                                    <span class="font-medium text-gray-900" x-text="exercise.name"></span>
                                    <span class="text-sm text-gray-600" x-text="'(' + exercise.category + ' • ' + exercise.equipment + ')'"></span>
                                </div>
                                <button type="button"
                                        @click="removeExerciseFromTemplate(exercise.id)"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Пустое состояние -->
                    <div x-show="selectedExercises.length === 0" class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p>Нет упражнений в шаблоне</p>
                        <p class="text-sm"><?php echo e(__('common.click_add_exercises_to_select')); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        @click="showList()" 
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    <?php echo e(__('common.cancel')); ?>

                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentView === 'create' ? '<?php echo e(__('common.create')); ?>' : '<?php echo e(__('common.save')); ?>'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Просмотр шаблона -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900" x-text="currentTemplate?.name || 'Шаблон'"></h2>
                    <p class="mt-2 text-gray-600" x-text="currentTemplate?.description || 'Без описания'"></p>
                </div>
                <button @click="showList()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    <?php echo e(__('common.back_to_list')); ?>

                </button>
            </div>
        </div>
        
        <div x-show="currentTemplate" class="space-y-6">
            <!-- Информация о шаблоне -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Категория</h3>
                    <p class="text-lg font-semibold text-gray-900" x-text="getCategoryLabel(currentTemplate?.category)"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Сложность</h3>
                    <p class="text-lg font-semibold text-gray-900" x-text="getDifficultyLabel(currentTemplate?.difficulty)"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Длительность</h3>
                    <p class="text-lg font-semibold text-gray-900" x-text="(currentTemplate?.estimated_duration || 60) + ' <?php echo e(__('common.minutes')); ?>'"></p>
                </div>
            </div>
            
            <!-- Список упражнений -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Упражнения в шаблоне</h3>
                <div x-show="((currentTemplate?.valid_exercises && currentTemplate?.valid_exercises.length > 0) ? currentTemplate?.valid_exercises : (currentTemplate?.exercises || [])).length > 0" class="space-y-3">
                    <template x-for="(exercise, index) in ((currentTemplate?.valid_exercises && currentTemplate?.valid_exercises.length > 0) ? currentTemplate?.valid_exercises : (currentTemplate?.exercises || []))" :key="index">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900" x-text="exercise.name || 'Неизвестное упражнение'"></h4>
                                    <p class="text-sm text-gray-600" x-text="exercise.category + ' • ' + exercise.equipment"></p>
                                </div>
                                <span class="text-sm text-gray-500" x-text="'#' + (index + 1)"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="((currentTemplate?.valid_exercises && currentTemplate?.valid_exercises.length > 0) ? currentTemplate?.valid_exercises : (currentTemplate?.exercises || [])).length === 0" class="text-center py-8 text-gray-500">
                    В шаблоне нет упражнений
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно выбора упражнений -->
<div x-show="showExerciseModal" 
     class="fixed inset-0 z-[9999] bg-black bg-opacity-50 flex items-center justify-center p-4"
     @click.self="closeExerciseModal()"
     style="display: none;">
    
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Заголовок -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.select_exercises')); ?></h3>
            <button @click="closeExerciseModal()" 
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <!-- Содержимое -->
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Фильтры для упражнений -->
            <div class="mb-4 space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" 
                           x-model="exerciseSearch" 
                           placeholder="<?php echo e(__('common.search_exercises')); ?>" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <select x-model="exerciseCategory" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value=""><?php echo e(__('common.all_categories')); ?></option>
                        <template x-for="category in exerciseCategoryOptions" :key="category">
                            <option :value="category" x-text="category"></option>
                        </template>
                    </select>
                    
                    <select x-model="exerciseEquipment" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value=""><?php echo e(__('common.all_equipment')); ?></option>
                        <template x-for="equipment in exerciseEquipmentOptions" :key="equipment">
                            <option :value="equipment" x-text="equipment"></option>
                        </template>
                    </select>

                    <select x-model="exerciseTypeFilter"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value=""><?php echo e(__('common.all_exercises')); ?></option>
                        <option value="system"><?php echo e(__('common.system_exercises')); ?></option>
                        <option value="custom"><?php echo e(__('common.user_exercises')); ?></option>
                        <option value="favorite"><?php echo e(__('common.favorite_exercises')); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="exercise in filteredExercises" :key="exercise.id">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                         :class="selectedExercises.find(ex => ex.id === exercise.id) ? 'bg-blue-50 border-blue-300' : ''"
                         @click="toggleExercise(exercise)">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" 
                                   :checked="selectedExercises.find(ex => ex.id === exercise.id)"
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 pointer-events-none">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900" x-text="exercise.name"></h4>
                                <p class="text-sm text-gray-600" x-text="exercise.category"></p>
                                <p class="text-sm text-gray-500" x-text="exercise.equipment"></p>
                                <div x-show="exercise.muscle_groups && exercise.muscle_groups.length > 0" class="mt-2">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="muscle in exercise.muscle_groups" :key="muscle">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800" x-text="muscle"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div x-show="filteredExercises.length === 0" class="text-center py-8 text-gray-500">
                <div x-show="availableExercises.length === 0">Упражнения не найдены</div>
                <div x-show="availableExercises.length > 0">Нет упражнений, соответствующих фильтрам</div>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <button @click="closeExerciseModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                <?php echo e(__('common.cancel')); ?>

            </button>
            <button @click="closeExerciseModal()" 
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700">
                <?php echo e(__('common.done')); ?>

            </button>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make("crm.layouts.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/trainer/workout-templates/index.blade.php ENDPATH**/ ?>
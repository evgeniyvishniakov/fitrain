@extends("crm.layouts.app")

@section("title", "Упражнения")
@section("page-title", "Упражнения")

<script>
// SPA функциональность для упражнений
function exerciseApp() {
    return {
        currentView: 'list', // list, create, edit, view
        exercises: @json(\App\Models\Exercise::active()->orderBy('name')->get()),
        currentExercise: null,
        search: '',
        category: '',
        equipment: '',
        currentPage: 1,
        itemsPerPage: 4,
        
        // Поля формы
        formName: '',
        formDescription: '',
        formCategory: '',
        formEquipment: '',
        formMuscleGroupsText: '',
        formInstructions: '',
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentExercise = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentExercise = null;
            this.formName = '';
            this.formDescription = '';
            this.formCategory = '';
            this.formEquipment = '';
            this.formMuscleGroupsText = '';
            this.formInstructions = '';
        },
        
        showEdit(exerciseId) {
            this.currentView = 'edit';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.formName = this.currentExercise.name;
            this.formDescription = this.currentExercise.description || '';
            this.formCategory = this.currentExercise.category;
            this.formEquipment = this.currentExercise.equipment;
            this.formMuscleGroupsText = Array.isArray(this.currentExercise.muscle_groups) ? this.currentExercise.muscle_groups.join(', ') : '';
            this.formInstructions = this.currentExercise.instructions || '';
        },
        
        showView(exerciseId) {
            this.currentView = 'view';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
        },
        
        // Фильтрация
        get filteredExercises() {
            let filtered = this.exercises;
            
            if (this.search) {
                filtered = filtered.filter(e => 
                    e.name.toLowerCase().includes(this.search.toLowerCase()) ||
                    (e.description && e.description.toLowerCase().includes(this.search.toLowerCase())) ||
                    e.category.toLowerCase().includes(this.search.toLowerCase()) ||
                    e.equipment.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            if (this.category) {
                filtered = filtered.filter(e => e.category === this.category);
            }
            
            if (this.equipment) {
                filtered = filtered.filter(e => e.equipment === this.equipment);
            }
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            const total = Math.ceil(this.filteredExercises.length / this.itemsPerPage);
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
        
        get paginatedExercises() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredExercises.slice(start, end);
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
        async saveExercise() {
            try {
                const muscleGroups = this.formMuscleGroupsText
                    .split(',')
                    .map(g => g.trim())
                    .filter(g => g.length > 0);
                
                const exerciseData = {
                    name: this.formName,
                    description: this.formDescription,
                    category: this.formCategory,
                    equipment: this.formEquipment,
                    muscle_groups: muscleGroups,
                    instructions: this.formInstructions
                };
                
                const url = this.currentExercise && this.currentExercise.id ? 
                    `/exercises/${this.currentExercise.id}` : '/exercises';
                const method = this.currentExercise && this.currentExercise.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(exerciseData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentExercise && this.currentExercise.id ? 'Упражнение обновлено' : 'Упражнение создано',
                            message: this.currentExercise && this.currentExercise.id ? 
                                'Упражнение успешно обновлено' : 
                                'Упражнение успешно добавлено в базу'
                        }
                    }));
                    
                    // Обновляем список упражнений
                    if (this.currentExercise && this.currentExercise.id) {
                        // Редактирование - обновляем существующее
                        const index = this.exercises.findIndex(e => e.id === this.currentExercise.id);
                        if (index !== -1) {
                            this.exercises[index] = { ...this.currentExercise, ...exerciseData };
                        }
                    } else {
                        // Создание - добавляем новое
                        this.exercises.unshift(result.exercise);
                    }
                    
                    // Переключаемся на список
                    this.showList();
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении упражнения'
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
                        message: 'Произошла ошибка при сохранении упражнения'
                    }
                }));
            }
        },
        
        // Удаление
        deleteExercise(id) {
            const exercise = this.exercises.find(e => e.id === id);
            const exerciseName = exercise ? exercise.name : 'упражнение';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: 'Удалить упражнение',
                    message: `Вы уверены, что хотите удалить упражнение "${exerciseName}"?`,
                    confirmText: 'Удалить',
                    cancelText: 'Отмена',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/exercises/${id}`, {
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
                            title: 'Упражнение удалено',
                            message: 'Упражнение успешно удалено из базы'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.exercises = this.exercises.filter(e => e.id !== id);
                    
                    // Если удалили все упражнения на текущей странице, переходим на предыдущую
                    if (this.paginatedExercises.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении упражнения'
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
                        message: 'Произошла ошибка при удалении упражнения'
                    }
                }));
            }
        }
    }
}
</script>

@section("header-actions")
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
@endsection

@section("content")
<div x-data="exerciseApp()" x-cloak class="space-y-6">
    
    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                           placeholder="Поиск упражнений..." 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр категории -->
                <div class="filter-container">
                    <select x-model="category" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все категории</option>
                        <option value="Грудь">Грудь</option>
                        <option value="Спина">Спина</option>
                        <option value="Ноги">Ноги</option>
                        <option value="Плечи">Плечи</option>
                        <option value="Руки">Руки</option>
                        <option value="Кардио">Кардио</option>
                        <option value="Гибкость">Гибкость</option>
                    </select>
                </div>
                
                <!-- Фильтр оборудования -->
                <div class="filter-container">
                    <select x-model="equipment" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все оборудование</option>
                        <option value="Штанга">Штанга</option>
                        <option value="Гантели">Гантели</option>
                        <option value="Собственный вес">Собственный вес</option>
                        <option value="Тренажеры">Тренажеры</option>
                        <option value="Скакалка">Скакалка</option>
                        <option value="Турник">Турник</option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    @if(auth()->user()->hasRole('trainer'))
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            Добавить упражнение
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search || category || equipment" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">Активные фильтры:</span>
                <span x-show="search" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Поиск: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                </span>
                <span x-show="category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Категория: <span x-text="category"></span>
                    <button @click="category = ''" class="ml-1 text-green-600 hover:text-green-800">×</button>
                </span>
                <span x-show="equipment" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Оборудование: <span x-text="equipment"></span>
                    <button @click="equipment = ''" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                </span>
            </div>
        </div>
    </div>

    <!-- Список упражнений -->
    <div x-show="currentView === 'list'" class="space-y-6">
        <div x-show="paginatedExercises.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <template x-for="exercise in paginatedExercises" :key="exercise.id">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 p-6">
                    <!-- Заголовок -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <span x-text="exercise.name"></span>
                            </h3>
                            <p class="text-gray-600 mb-4" x-text="exercise.description || 'Без описания'"></p>
                            
                            <!-- Теги -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="exercise.category"></span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="exercise.equipment"></span>
                            </div>
                            
                            <!-- Группы мышц -->
                            <div class="text-sm text-gray-500" x-show="exercise.muscle_groups && exercise.muscle_groups.length > 0">
                                <span x-text="'Группы мышц: ' + exercise.muscle_groups.join(', ')"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex space-x-2">
                        <button @click="showView(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            Просмотр
                        </button>
                        @if(auth()->user()->hasRole('trainer'))
                            <button @click="showEdit(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button @click="deleteExercise(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
                            </button>
                        @endif
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Пустое состояние -->
        <div x-show="paginatedExercises.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">💪</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет упражнений</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Добавьте упражнения в базу для создания тренировок.</p>
            @if(auth()->user()->hasRole('trainer'))
                <button @click="showCreate()" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Добавить первое упражнение
                </button>
            @endif
        </div>
        
        <!-- Пагинация -->
        <div x-show="paginatedExercises.length > 0 && totalPages > 1" class="mt-6">
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
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900" x-text="currentView === 'create' ? 'Создать упражнение' : 'Редактировать упражнение'"></h2>
            <p class="mt-2 text-gray-600" x-text="currentView === 'create' ? 'Добавьте новое упражнение в базу' : 'Внесите изменения в упражнение'"></p>
        </div>
        
        <form @submit.prevent="saveExercise()" class="space-y-6">
            <div class="space-y-6">
                <!-- Название -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название упражнения *</label>
                    <input type="text" 
                           x-model="formName" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Три поля в одну строку -->
                <div class="flex flex-col md:flex-row gap-6 flex-form-row" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <style>
                        /* Мобильные (< 640px) - в колонку */
                        @media (max-width: 639px) {
                            .flex-form-row { flex-direction: column !important; }
                        }
                        /* Планшеты (640px - 767px) - в колонку */
                        @media (min-width: 640px) and (max-width: 767px) {
                            .flex-form-row { flex-direction: column !important; }
                        }
                        /* Планшеты (768px - 1023px) - в линию */
                        @media (min-width: 768px) and (max-width: 1023px) {
                            .flex-form-row { flex-direction: row !important; }
                        }
                        /* Ноутбуки (1024px - 1279px) - в линию */
                        @media (min-width: 1024px) and (max-width: 1279px) {
                            .flex-form-row { flex-direction: row !important; }
                        }
                        /* Десктопы (1280px+) - в линию */
                        @media (min-width: 1280px) {
                            .flex-form-row { flex-direction: row !important; }
                        }
                    </style>
                    <!-- Категория -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Категория *</label>
                        <select x-model="formCategory" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Выберите категорию</option>
                            <option value="Грудь">Грудь</option>
                            <option value="Спина">Спина</option>
                            <option value="Ноги">Ноги</option>
                            <option value="Плечи">Плечи</option>
                            <option value="Руки">Руки</option>
                            <option value="Кардио">Кардио</option>
                            <option value="Гибкость">Гибкость</option>
                        </select>
                    </div>
                    
                    <!-- Оборудование -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Оборудование *</label>
                        <select x-model="formEquipment" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Выберите оборудование</option>
                            <option value="Штанга">Штанга</option>
                            <option value="Гантели">Гантели</option>
                            <option value="Собственный вес">Собственный вес</option>
                            <option value="Тренажеры">Тренажеры</option>
                            <option value="Скакалка">Скакалка</option>
                            <option value="Турник">Турник</option>
                        </select>
                    </div>
                    
                    <!-- Группы мышц -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Группы мышц (через запятую)</label>
                        <input type="text" 
                               x-model="formMuscleGroupsText" 
                               placeholder="например: грудь, плечи, трицепс"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                </div>
                
                <!-- Описание -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                    <textarea x-model="formDescription" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                </div>
                
                <!-- Инструкции -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Инструкции по выполнению</label>
                    <textarea x-model="formInstructions" 
                              rows="4"
                              placeholder="Пошаговые инструкции по выполнению упражнения..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                </div>
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        @click="showList()" 
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    Отмена
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentView === 'create' ? 'Создать' : 'Сохранить'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Просмотр упражнения -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900" x-text="currentExercise?.name || 'Упражнение'"></h2>
                    <p class="mt-2 text-gray-600" x-text="currentExercise?.description || 'Без описания'"></p>
                </div>
                <button @click="showList()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    Назад к списку
                </button>
            </div>
        </div>
        
        <div x-show="currentExercise" class="space-y-6">
            <!-- Информация об упражнении -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Категория</h3>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentExercise?.category"></p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Оборудование</h3>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentExercise?.equipment"></p>
                </div>
            </div>
            
            <!-- Группы мышц -->
            <div x-show="currentExercise?.muscle_groups && currentExercise?.muscle_groups.length > 0">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Группы мышц</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="group in currentExercise?.muscle_groups || []" :key="group">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" x-text="group"></span>
                    </template>
                </div>
            </div>
            
            <!-- Инструкции -->
            <div x-show="currentExercise?.instructions">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Инструкции по выполнению</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700 whitespace-pre-line" x-text="currentExercise?.instructions"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
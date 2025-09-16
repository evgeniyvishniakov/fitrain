@extends("crm.layouts.app")

@section("title", "Каталог упражнений")
@section("page-title", "Каталог упражнений")

<script>
// SPA функциональность для каталога упражнений
function exerciseCatalogApp() {
    return {
        currentView: 'exercises', // exercises, templates
        search: '',
        category: '',
        equipment: '',
        currentPage: 1,
        itemsPerPage: 20,
        
        currentView: 'list', // list, create, edit
        currentExercise: null,
        formName: '',
        formCategory: '',
        formEquipment: '',
        formDescription: '',
        
        exercises: @json($exercises->items()),
        
        // Фильтрация
        get filteredExercises() {
            let filtered = this.exercises;
            
            if (this.search) {
                filtered = filtered.filter(ex => 
                    ex.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            if (this.category) {
                filtered = filtered.filter(ex => ex.category === this.category);
            }
            
            if (this.equipment) {
                filtered = filtered.filter(ex => ex.equipment === this.equipment);
            }
            
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            const total = Math.ceil(this.filteredExercises.length / this.itemsPerPage);
            return total > 0 ? total : 1;
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
        
        // Вспомогательные методы
        getCategoryLabel(category) {
            const labels = {
                'chest': 'Грудь',
                'back': 'Спина',
                'legs': 'Ноги',
                'shoulders': 'Плечи',
                'arms': 'Руки',
                'cardio': 'Кардио',
                'core': 'Пресс'
            };
            return labels[category] || category;
        },
        
        getEquipmentLabel(equipment) {
            const labels = {
                'barbell': 'Штанга',
                'dumbbell': 'Гантели',
                'bodyweight': 'Собственный вес',
                'machine': 'Тренажер',
                'cable': 'Тросы',
                'kettlebell': 'Гири'
            };
            return labels[equipment] || equipment;
        },
        
        
        switchView(view) {
            if (view === 'exercises') {
                this.currentView = 'list';
            } else {
                this.currentView = view;
            }
            this.currentPage = 1;
        },
        
        // Методы для формы создания упражнения
        showCreate() {
            this.currentView = 'create';
            this.formName = '';
            this.formCategory = '';
            this.formEquipment = '';
            this.formDescription = '';
        },
        
        hideCreate() {
            this.currentView = 'list';
            this.currentExercise = null;
            this.formName = '';
            this.formCategory = '';
            this.formEquipment = '';
            this.formDescription = '';
        },
        
        showEdit(exerciseId) {
            this.currentView = 'edit';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.formName = this.currentExercise.name;
            this.formCategory = this.currentExercise.category;
            this.formEquipment = this.currentExercise.equipment;
            this.formDescription = this.currentExercise.description || '';
        },
        
        async saveExercise() {
            try {
                const exerciseData = {
                    name: this.formName,
                    category: this.formCategory,
                    equipment: this.formEquipment,
                    description: this.formDescription
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
                
                if (response.ok) {
                    const result = await response.json();
                    
                    if (this.currentExercise && this.currentExercise.id) {
                        // Обновляем существующее упражнение
                        const index = this.exercises.findIndex(ex => ex.id === this.currentExercise.id);
                        if (index !== -1) {
                            this.exercises[index] = result.exercise;
                        }
                        showSuccess('Успешно', 'Упражнение обновлено');
                    } else {
                        // Добавляем новое упражнение
                        this.exercises.unshift(result.exercise);
                        showSuccess('Успешно', 'Упражнение добавлено в каталог');
                    }
                    
                    // Возвращаемся к списку
                    this.hideCreate();
                } else {
                    showError('Ошибка', 'Не удалось сохранить упражнение');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showError('Ошибка', 'Произошла ошибка при сохранении');
            }
        },
        
        async deleteExercise(exerciseId) {
            const exercise = this.exercises.find(ex => ex.id === exerciseId);
            const exerciseName = exercise ? exercise.name : 'упражнение';
            
            confirmDelete(exerciseName, async () => {
                try {
                    const response = await fetch(`/exercises/${exerciseId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        // Удаляем упражнение из списка
                        this.exercises = this.exercises.filter(ex => ex.id !== exerciseId);
                        showSuccess('Успешно', 'Упражнение удалено из каталога');
                    } else {
                        showError('Ошибка', 'Не удалось удалить упражнение');
                    }
                } catch (error) {
                    console.error('Ошибка:', error);
                    showError('Ошибка', 'Произошла ошибка при удалении');
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
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.exercises.index") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <a href="{{ route("crm.progress.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.exercises.index") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <a href="{{ route("crm.progress.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("header-actions")
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route('crm.exercises.create') }}" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Добавить упражнение
        </a>
    @endif
@endsection

@section("content")
<div x-data="exerciseCatalogApp()" class="space-y-6">
    
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
                    .filters-row .category-container {
                        width: 180px !important;
                    }
                    .filters-row .equipment-container {
                        width: 180px !important;
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
                
                <!-- Категория -->
                <div class="category-container">
                    <select x-model="category" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все категории</option>
                        <option value="chest">Грудь</option>
                        <option value="back">Спина</option>
                        <option value="legs">Ноги</option>
                        <option value="shoulders">Плечи</option>
                        <option value="arms">Руки</option>
                        <option value="cardio">Кардио</option>
                        <option value="core">Пресс</option>
                    </select>
                </div>
                
                <!-- Оборудование -->
                <div class="equipment-container">
                    <select x-model="equipment" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все оборудование</option>
                        <option value="barbell">Штанга</option>
                        <option value="dumbbell">Гантели</option>
                        <option value="bodyweight">Собственный вес</option>
                        <option value="machine">Тренажер</option>
                        <option value="cable">Тросы</option>
                        <option value="kettlebell">Гири</option>
                    </select>
                </div>
                
                <!-- Кнопка добавления -->
                @if(auth()->user()->hasRole('trainer'))
                    <div class="buttons-container">
                        <button @click="showCreate()" class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить упражнение
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Вкладки -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl">
            <button @click="switchView('exercises')" 
                    :class="currentView === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                Упражнения
            </button>
            <button @click="switchView('templates')" 
                    :class="currentView === 'templates' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                Шаблоны тренировок
            </button>
        </div>
    </div>

    <!-- СПИСОК УПРАЖНЕНИЙ -->
    <div x-show="currentView === 'list'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <template x-for="exercise in paginatedExercises" :key="exercise.id">
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 hover:border-indigo-200 p-4">
                       <!-- Название -->
                       <div class="mb-3">
                           <h3 class="text-sm font-semibold text-gray-900 line-clamp-2" x-text="exercise.name"></h3>
                       </div>
                    
                    <!-- Категория и оборудование -->
                    <div class="space-y-1 mb-3">
                        <div class="text-xs text-gray-600" x-text="getCategoryLabel(exercise.category)"></div>
                        <div class="text-xs text-gray-500" x-text="getEquipmentLabel(exercise.equipment)"></div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="flex space-x-2">
                        <button class="flex-1 px-2 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            В шаблон
                        </button>
                        
                            @if(auth()->user()->hasRole('trainer'))
                                <button @click="showEdit(exercise.id)" class="px-2 py-1.5 text-xs font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    ✏️
                                </button>
                                <button @click="deleteExercise(exercise.id)" class="px-2 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                    🗑️
                                </button>
                            @endif
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Пагинация -->
        <div x-show="filteredExercises.length > 0 && totalPages > 1" class="mt-6">
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
                        <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
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
        <div x-show="filteredExercises.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">💪</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет упражнений</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">По вашему запросу упражнения не найдены. Попробуйте изменить фильтры или добавить новые упражнения.</p>
            @if(auth()->user()->hasRole('trainer'))
                <a href="{{ route('crm.exercises.create') }}" 
                   class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Добавить первое упражнение
                </a>
            @endif
        </div>
    </div>

    <!-- ШАБЛОНЫ ТРЕНИРОВОК -->
    <div x-show="currentView === 'templates'" class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">📋</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Шаблоны тренировок</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Раздел шаблонов тренировок будет доступен в следующем обновлении.</p>
            @if(auth()->user()->hasRole('trainer'))
                <button class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Создать шаблон
                </button>
            @endif
        </div>
    </div>

    <!-- Форма создания/редактирования упражнения -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900" x-text="currentView === 'create' ? 'Добавить упражнение' : 'Редактировать упражнение'"></h3>
            <button @click="hideCreate()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form @submit.prevent="saveExercise()" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название *</label>
                    <input type="text" 
                           x-model="formName"
                           class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                           placeholder="Жим лежа"
                           required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Категория *</label>
                    <select x-model="formCategory" 
                            class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            required>
                        <option value="">Выберите</option>
                        <option value="chest">Грудь</option>
                        <option value="back">Спина</option>
                        <option value="legs">Ноги</option>
                        <option value="shoulders">Плечи</option>
                        <option value="arms">Руки</option>
                        <option value="cardio">Кардио</option>
                        <option value="core">Пресс</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Оборудование *</label>
                    <select x-model="formEquipment" 
                            class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            required>
                        <option value="">Выберите</option>
                        <option value="barbell">Штанга</option>
                        <option value="dumbbell">Гантели</option>
                        <option value="bodyweight">Собственный вес</option>
                        <option value="machine">Тренажер</option>
                        <option value="cable">Тросы</option>
                        <option value="kettlebell">Гири</option>
                    </select>
                </div>
                
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                <textarea x-model="formDescription" 
                          rows="2"
                          class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                          placeholder="Краткое описание..."></textarea>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" 
                        @click="hideCreate()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                    Отмена
                </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                x-text="currentView === 'create' ? 'Создать упражнение' : 'Сохранить изменения'">
                        </button>
            </div>
        </form>
    </div>
</div>

@endsection

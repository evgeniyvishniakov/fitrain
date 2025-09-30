@extends("crm.layouts.app")

@section("title", "Упражнения")
@section("page-title", "Упражнения")

<script>
// SPA функциональность для упражнений
function exerciseApp() {
    return {
        currentView: 'list', // list, create, edit, view, add-video
        exercises: @json(\App\Models\Trainer\Exercise::active()->orderBy('created_at', 'desc')->get()),
        currentExercise: null,
        search: '',
        category: '',
        equipment: '',
        currentPage: 1,
        itemsPerPage: 10,
        
        // Поля для пользовательского видео
        userVideoUrl: '',
        userVideoTitle: '',
        userVideoDescription: '',
        currentUserVideo: null,
        userVideos: {}, // Кэш пользовательских видео для всех упражнений
        
        // Поля формы
        formName: '',
        formDescription: '',
        formCategory: '',
        formEquipment: '',
        formMuscleGroupsText: '',
        formInstructions: '',
        formVideoUrl: '',
        formFieldsConfig: ['sets', 'reps', 'weight', 'rest'], // По умолчанию
        
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
            this.formVideoUrl = '';
            this.formFieldsConfig = ['sets', 'reps', 'weight', 'rest'];
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
            this.formVideoUrl = this.currentExercise.video_url || '';
            this.formFieldsConfig = this.currentExercise.fields_config || ['sets', 'reps', 'weight', 'rest'];
        },
        
        showView(exerciseId) {
            this.currentView = 'view';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            
            // Загружаем пользовательское видео, если упражнение системное
            if (this.currentExercise && this.currentExercise.is_system) {
                this.loadUserVideo(exerciseId);
            } else {
                this.currentUserVideo = null;
            }
        },
        
        showAddVideo(exerciseId) {
            this.currentView = 'add-video';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.userVideoUrl = '';
            this.userVideoTitle = '';
            this.userVideoDescription = '';
            this.currentUserVideo = null;
            
            // Загружаем существующее видео, если есть
            this.loadUserVideo(exerciseId);
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
                    instructions: this.formInstructions,
                    video_url: this.formVideoUrl,
                    fields_config: this.formFieldsConfig
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
                
                if (result.success) {
                    // Показываем уведомление об успехе
                    showSuccess(result.message || 'Упражнение успешно удалено из каталога');
                    
                    // Удаляем из списка
                    this.exercises = this.exercises.filter(e => e.id !== id);
                    
                    // Если удалили все упражнения на текущей странице, переходим на предыдущую
                    if (this.paginatedExercises.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке (упражнение используется)
                    showError(result.message || 'Упражнение используется в тренировках или шаблонах');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                showError('Произошла ошибка при удалении упражнения');
            }
        },
        
        // Методы для работы с видео
        isYouTubeUrl(url) {
            if (!url) return false;
            return url.includes('youtube.com') || url.includes('youtu.be');
        },
        
        // Модальное окно для видео
        videoModal: {
            isOpen: false,
            url: '',
            title: ''
        },
        
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
        
        // Проверяем, есть ли видео у упражнения (системное или пользовательское)
        hasVideo(exercise) {
            const hasSystemVideo = !!exercise.video_url;
            const hasUserVideo = !!this.userVideos[exercise.id];
            const hasAnyVideo = hasSystemVideo || hasUserVideo;
            
            return hasAnyVideo;
        },
        
        // Получаем URL видео (приоритет пользовательскому)
        getVideoUrl(exercise) {
            return this.userVideos[exercise.id]?.video_url || exercise.video_url;
        },
        
        // Получаем название видео
        getVideoTitle(exercise) {
            return this.userVideos[exercise.id]?.title || exercise.name;
        },
        
        // Инициализация
        init() {
            // Загружаем пользовательские видео при инициализации
            this.loadAllUserVideos();
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
        
        // Методы для работы с пользовательскими видео
        async loadAllUserVideos() {
            try {
                const response = await fetch('/exercises/user-videos', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    console.error('Получен HTML вместо JSON. Возможно, требуется авторизация.');
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    return;
                }
                
                if (result.success && result.videos) {
                    this.userVideos = {};
                    result.videos.forEach(video => {
                        this.userVideos[video.exercise_id] = video;
                    });
                }
            } catch (error) {
                console.error('Ошибка загрузки пользовательских видео:', error);
            }
        },
        
        async loadUserVideo(exerciseId) {
            try {
                const response = await fetch(`/exercises/${exerciseId}/user-video`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success && result.video) {
                    this.currentUserVideo = result.video;
                    this.userVideoUrl = result.video.video_url;
                    this.userVideoTitle = result.video.title || '';
                    this.userVideoDescription = result.video.description || '';
                }
            } catch (error) {
                console.error('Ошибка загрузки видео:', error);
            }
        },
        
        async saveUserVideo() {
            try {
                const videoData = {
                    video_url: this.userVideoUrl,
                    title: this.userVideoTitle,
                    description: this.userVideoDescription
                };
                
                const response = await fetch(`/exercises/${this.currentExercise.id}/user-video`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(videoData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: 'Видео сохранено',
                            message: result.message
                        }
                    }));
                    
                    // Обновляем текущее видео
                    this.currentUserVideo = result.video;
                    
                    // Переключаемся на просмотр
                    this.showView(this.currentExercise.id);
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении видео'
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
                        message: 'Произошла ошибка при сохранении видео'
                    }
                }));
            }
        },
        
        async deleteUserVideo() {
            try {
                const response = await fetch(`/exercises/${this.currentExercise.id}/user-video`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: 'Видео удалено',
                            message: result.message
                        }
                    }));
                    
                    // Очищаем поля
                    this.currentUserVideo = null;
                    this.userVideoUrl = '';
                    this.userVideoTitle = '';
                    this.userVideoDescription = '';
                    
                    // Переключаемся на просмотр
                    this.showView(this.currentExercise.id);
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении видео'
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
                        message: 'Произошла ошибка при удалении видео'
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
<div x-data="exerciseApp()" x-init="init()" x-cloak class="space-y-6">
    
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
                        <option value="Пресс">Пресс</option>
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
                        <option value="Брусья">Брусья</option>
                        <option value="Скамейка">Скамейка</option>
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
        <div x-show="paginatedExercises.length > 0" style="display: grid; gap: 24px;" class="exercise-grid">
            <template x-for="exercise in paginatedExercises" :key="exercise.id">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 p-6">
                    <!-- Заголовок -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <span x-text="exercise.name"></span>
                                </h3>
                                <button x-show="hasVideo(exercise)" 
                                        @click="openVideoModal(getVideoUrl(exercise), getVideoTitle(exercise))"
                                        class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full transition-colors cursor-pointer">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    Видео
                                </button>
                            </div>
                            
                            <!-- Теги -->
                            <div class="flex flex-wrap gap-2 mb-4 justify-between">
                                <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="exercise.category"></span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="exercise.equipment"></span>
                                </div>
                                <span x-show="exercise.is_system" 
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-help"
                                      title="Системное упражнение нельзя редактировать или удалять">
                                    Системное
                                </span>
                            </div>
                            
                            <!-- Группы мышц -->
                            <div class="text-sm text-gray-500" x-show="exercise.muscle_groups && exercise.muscle_groups.length > 0">
                                <span x-text="'Группы мышц: '"></span><span class="text-black" x-text="exercise.muscle_groups.join(', ')"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Кнопки -->
                    <div class="flex space-x-2">
                        <button @click="showView(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            Просмотр
                        </button>
                        @if(auth()->user()->hasRole('trainer'))
                            <button x-show="!exercise.is_system" @click="showEdit(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button x-show="!exercise.is_system" @click="deleteExercise(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
                            </button>
                            <button x-show="exercise.is_system" @click="showAddVideo(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                                Добавить видео
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
                <!-- Название и ссылка на видео в одном ряду -->
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Название упражнения *</label>
                        <input type="text" 
                               x-model="formName" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ссылка на видео</label>
                        <input type="url" 
                               x-model="formVideoUrl" 
                               placeholder="https://youtube.com/watch?v=..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
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
                            <option value="Пресс">Пресс</option>
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
                            <option value="Брусья">Брусья</option>
                            <option value="Скамейка">Скамейка</option>
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
                
                <!-- Конфигурация полей -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Поля для ввода данных</h3>
                            <p class="text-sm text-gray-600">Выберите какие поля будут доступны при добавлении этого упражнения в тренировку</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
                        <!-- Подходы -->
                        <label class="field-card" :class="formFieldsConfig.includes('sets') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="sets"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('sets') ? 'bg-indigo-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('sets') ? 'text-indigo-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('sets') ? 'text-indigo-900' : 'text-gray-900'">Подходы</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('sets') ? 'text-indigo-600' : 'text-gray-500'">Количество подходов</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Повторения -->
                        <label class="field-card" :class="formFieldsConfig.includes('reps') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="reps"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('reps') ? 'bg-green-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('reps') ? 'text-green-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('reps') ? 'text-green-900' : 'text-gray-900'">Повторения</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('reps') ? 'text-green-600' : 'text-gray-500'">Количество повторений</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Вес -->
                        <label class="field-card" :class="formFieldsConfig.includes('weight') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="weight"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('weight') ? 'bg-orange-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('weight') ? 'text-orange-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('weight') ? 'text-orange-900' : 'text-gray-900'">Вес (кг)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('weight') ? 'text-orange-600' : 'text-gray-500'">Рабочий вес</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Отдых -->
                        <label class="field-card" :class="formFieldsConfig.includes('rest') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="rest"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('rest') ? 'bg-purple-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('rest') ? 'text-purple-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('rest') ? 'text-purple-900' : 'text-gray-900'">Отдых (мин)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('rest') ? 'text-purple-600' : 'text-gray-500'">Время отдыха</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Время -->
                        <label class="field-card" :class="formFieldsConfig.includes('time') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="time"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('time') ? 'bg-blue-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('time') ? 'text-blue-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('time') ? 'text-blue-900' : 'text-gray-900'">Время (сек)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('time') ? 'text-blue-600' : 'text-gray-500'">Продолжительность</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Дистанция -->
                        <label class="field-card" :class="formFieldsConfig.includes('distance') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="distance"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('distance') ? 'bg-emerald-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('distance') ? 'text-emerald-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('distance') ? 'text-emerald-900' : 'text-gray-900'">Дистанция (м)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('distance') ? 'text-emerald-600' : 'text-gray-500'">Пройденное расстояние</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Темп -->
                        <label class="field-card" :class="formFieldsConfig.includes('tempo') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   x-model="formFieldsConfig" 
                                   value="tempo"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('tempo') ? 'bg-pink-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('tempo') ? 'text-pink-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('tempo') ? 'text-pink-900' : 'text-gray-900'">Темп/Скорость</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('tempo') ? 'text-pink-600' : 'text-gray-500'">Скорость выполнения</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Быстрые шаблоны -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Быстрые шаблоны</h4>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    @click="formFieldsConfig = ['sets', 'reps', 'weight', 'rest']"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                💪 Силовое
                            </button>
                            <button type="button" 
                                    @click="formFieldsConfig = ['sets', 'reps', 'rest']"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                🏃 Собственный вес
                            </button>
                            <button type="button" 
                                    @click="formFieldsConfig = ['time', 'tempo']"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                🏃‍♂️ Кардио
                            </button>
                            <button type="button" 
                                    @click="formFieldsConfig = ['sets', 'time', 'rest']"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                ⏱️ Статическое
                            </button>
                            <button type="button" 
                                    @click="formFieldsConfig = ['distance', 'time', 'tempo']"
                                    class="px-3 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                🏃‍♀️ Бег/Ходьба
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-amber-800 font-medium">Примечания будут доступны всегда для всех упражнений</span>
                        </div>
                    </div>
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

    <!-- Форма добавления пользовательского видео -->
    <div x-show="currentView === 'add-video'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Добавить видео к упражнению</h2>
            <p class="mt-2 text-gray-600" x-text="'Добавьте своё видео для упражнения: ' + (currentExercise?.name || '')"></p>
        </div>
        
        <form @submit.prevent="saveUserVideo()" class="space-y-6">
            <div class="space-y-6">
                <!-- URL видео -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ссылка на видео *</label>
                    <input type="url" 
                           x-model="userVideoUrl" 
                           required
                           placeholder="https://youtube.com/watch?v=..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Название видео -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название видео</label>
                    <input type="text" 
                           x-model="userVideoTitle" 
                           placeholder="Например: Правильная техника выполнения"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Описание видео -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание видео</label>
                    <textarea x-model="userVideoDescription" 
                              rows="3"
                              placeholder="Дополнительные заметки о видео..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                </div>
                
                <!-- Предпросмотр видео -->
                <div x-show="userVideoUrl && isYouTubeUrl(userVideoUrl)" class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Предпросмотр видео</h3>
                    <div class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                        <iframe :src="getYouTubeEmbedUrl(userVideoUrl)" 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки -->
            <div class="flex justify-end space-x-4">
                <button type="button" 
                        @click="showView(currentExercise.id)" 
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    Отмена
                </button>
                <button x-show="currentUserVideo" 
                        type="button" 
                        @click="deleteUserVideo()" 
                        class="px-6 py-3 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                    Удалить видео
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentUserVideo ? 'Обновить видео' : 'Сохранить видео'"></span>
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
            
            <!-- Системное видео -->
            <div x-show="currentExercise?.video_url" class="mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Системное видео упражнения</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div x-show="isYouTubeUrl(currentExercise?.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                        <iframe :src="getYouTubeEmbedUrl(currentExercise?.video_url)" 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                allowfullscreen>
                        </iframe>
                    </div>
                    <div x-show="!isYouTubeUrl(currentExercise?.video_url)" class="text-center">
                        <a :href="currentExercise?.video_url" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Открыть видео
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Пользовательское видео -->
            <div x-show="currentExercise?.is_system" class="mt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Моё видео к упражнению</h3>
                    <button @click="showAddVideo(currentExercise.id)" 
                            class="px-4 py-2 text-sm font-medium text-purple-700 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <span x-text="currentUserVideo ? 'Изменить видео' : 'Добавить видео'"></span>
                    </button>
        </div>
                
                <div x-show="!currentUserVideo" class="bg-gray-50 rounded-lg p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Нет пользовательского видео</h4>
                    <p class="text-gray-600 mb-4">Добавьте своё видео с правильной техникой выполнения этого упражнения</p>
                    <button @click="showAddVideo(currentExercise.id)" 
                            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 transition-colors">
                        Добавить видео
                    </button>
                </div>
                
                <div x-show="currentUserVideo" class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold text-purple-900" x-text="currentUserVideo?.title || 'Моё видео'"></h4>
                        <p x-show="currentUserVideo?.description" class="text-purple-700 mt-1" x-text="currentUserVideo?.description"></p>
                    </div>
                    
                    <div x-show="currentUserVideo && currentUserVideo.video_url && isYouTubeUrl(currentUserVideo.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                        <iframe :src="getYouTubeEmbedUrl(currentUserVideo.video_url)" 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                allowfullscreen>
                        </iframe>
                    </div>
                    <div x-show="currentUserVideo && currentUserVideo.video_url && !isYouTubeUrl(currentUserVideo.video_url)" class="text-center">
                        <a :href="currentUserVideo.video_url" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Открыть видео
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для видео -->
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

<style>
.exercise-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}

@media (min-width: 1024px) {
    .exercise-grid {
        grid-template-columns: 1fr 1fr;
    }
}

.field-card {
    @apply p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md;
}

.field-card-selected {
    @apply border-indigo-300 bg-indigo-50 shadow-sm;
}

.field-card-unselected {
    @apply border-gray-200 bg-white hover:border-gray-300;
}

.field-card:hover {
    @apply transform scale-105;
}

.field-card-selected:hover {
    @apply border-indigo-400 bg-indigo-100;
}

.field-card-unselected:hover {
    @apply border-gray-300 bg-gray-50;
}

/* На больших экранах делаем карточки в одну линию */
@media (min-width: 1024px) {
    .field-card {
        @apply p-2;
    }
    
    .field-card .flex {
        @apply flex-col text-center space-x-0 space-y-1;
    }
    
    .field-card .w-10 {
        @apply w-6 h-6 mx-auto;
    }
    
    .field-card .w-5 {
        @apply w-3 h-3;
    }
    
    .field-card .font-medium {
        @apply text-xs;
    }
    
    .field-card .text-xs {
        @apply text-xs;
    }
}
</style>

@endsection
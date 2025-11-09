<?php $__env->startSection("title", "Упражнения"); ?>
<?php $__env->startSection("page-title", "Упражнения"); ?>

<?php $__env->startSection("sidebar"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="<?php echo e(route("crm.athlete.workouts")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="<?php echo e(route("crm.athlete.exercises")); ?>" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
    </a>
    <a href="<?php echo e(route("crm.athlete.progress")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="<?php echo e(route("crm.nutrition.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="<?php echo e(route('crm.athlete.settings')); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?>">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="<?php echo e(route("crm.athlete.workouts")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="<?php echo e(route("crm.athlete.exercises")); ?>" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
    </a>
    <a href="<?php echo e(route("crm.athlete.progress")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="<?php echo e(route("crm.nutrition.index")); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="<?php echo e(route('crm.athlete.settings')); ?>" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
<?php $__env->stopSection(); ?>

<script>
// SPA функциональность для упражнений спортсмена (только просмотр)
function exerciseApp() {
    return {
        currentView: 'list', // list, view
        exercises: [],
        currentExercise: null,
        search: '',
        category: '',
        equipment: '',
        exerciseType: '',
        currentPage: 1,
        itemsPerPage: 10,
        
        // Инициализация
        async init() {
            await this.loadExercises();
            
            // Сбрасываем пагинацию при изменении фильтров
            this.$watch('search', () => {
                this.currentPage = 1;
            });
            
            this.$watch('category', () => {
                this.currentPage = 1;
            });
            
            this.$watch('equipment', () => {
                this.currentPage = 1;
            });
            
            this.$watch('exerciseType', () => {
                this.currentPage = 1;
            });
        },
        
        // Загрузка упражнений из тренировок спортсмена
        async loadExercises() {
            try {
                const response = await fetch('/athlete/exercises/from-workouts', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.exercises = data.exercises || [];
                }
            } catch (error) {
            }
        },
        
        // Перевод оборудования на текущий язык
        getEquipmentTranslation(equipment) {
            if (!equipment) return '';
            const translations = {
                'Штанга': '<?php echo e(__('common.barbell')); ?>',
                'Гриф': '<?php echo e(__('common.barbell_bar')); ?>',
                'Трап-гриф': '<?php echo e(__('common.trap_bar')); ?>',
                'EZ-гриф': '<?php echo e(__('common.ez_bar')); ?>',
                'Отягощения': '<?php echo e(__('common.weight_plate')); ?>',
                'Гантели': '<?php echo e(__('common.dumbbells')); ?>',
                'Гири': '<?php echo e(__('common.kettlebells')); ?>',
                'Собственный вес': '<?php echo e(__('common.body_weight')); ?>',
                'Тренажер': '<?php echo e(__('common.machines')); ?>',
                'Машина Смита': '<?php echo e(__('common.smith_machine')); ?>',
                'Кроссовер / Блок': '<?php echo e(__('common.crossover_block')); ?>',
                'Скакалка': '<?php echo e(__('common.jump_rope')); ?>',
                'Турник': '<?php echo e(__('common.pull_up_bar')); ?>',
                'Брусья': '<?php echo e(__('common.parallel_bars')); ?>',
                'Скамейка': '<?php echo e(__('common.bench')); ?>',
                'Резина / Экспандер': '<?php echo e(__('common.resistance_band')); ?>'
            };
            return translations[equipment] || equipment;
        },
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentExercise = null;
        },
        
        showView(exerciseId) {
            this.currentView = 'view';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
        },
        
        // Фильтрация
        get filteredExercises() {
            let filtered = this.exercises;
            
            if (this.search) {
                const normalizedSearch = this.search.toLowerCase();
                filtered = filtered.filter(exercise =>
                    (exercise.name || '').toLowerCase().includes(normalizedSearch) ||
                    (exercise.category || '').toLowerCase().includes(normalizedSearch) ||
                    (exercise.equipment || '').toLowerCase().includes(normalizedSearch)
                );
            }
            
            if (this.category) {
                filtered = filtered.filter(exercise => (exercise.category || '') === this.category);
            }
            
            if (this.equipment) {
                filtered = filtered.filter(exercise => (exercise.equipment || '') === this.equipment);
            }
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            return Math.ceil(this.filteredExercises.length / this.itemsPerPage);
        },
        
        get visiblePages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                if (current <= 4) {
                    for (let i = 1; i <= 5; i++) pages.push(i);
                    pages.push('...');
                    pages.push(total);
                } else if (current >= total - 3) {
                    pages.push(1);
                    pages.push('...');
                    for (let i = total - 4; i <= total; i++) pages.push(i);
                } else {
                    pages.push(1);
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) pages.push(i);
                    pages.push('...');
                    pages.push(total);
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
            if (page !== '...') {
                this.currentPage = page;
            }
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
        
        // Вспомогательные функции
        getCategoryName(category) {
            const categories = {
                strength: 'Силовая',
                cardio: 'Кардио',
                flexibility: 'Гибкость',
                balance: 'Баланс',
                other: 'Другое'
            };
            return categories[category] || category;
        },
        
        getEquipmentName(equipment) {
            const equipments = {
                barbell: 'Штанга',
                dumbbell: 'Гантели',
                machine: 'Тренажер',
                bodyweight: 'Вес тела',
                resistance_band: 'Резинка',
                kettlebell: 'Гиря',
                other: 'Другое'
            };
            return equipments[equipment] || equipment;
        },
        
        // Преобразование URL YouTube в embed
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
            
            return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
        },
        
        // Проверка что это YouTube URL
        isYouTubeUrl(url) {
            if (!url) return false;
            return url.includes('youtube.com') || url.includes('youtu.be');
        },
        
        // Наличие видео у упражнения (системного или тренерского)
        hasVideo(exercise) {
            return this.getVideoUrl(exercise) !== '';
        },

        // Получение актуального URL видео с приоритетом видео тренера
        getVideoUrl(exercise) {
            if (!exercise) return '';

            const trainerVideo = exercise.trainer_video || null;
            const trainerUrl = trainerVideo ? (trainerVideo.url || trainerVideo.video_url || '') : '';
            const candidate = trainerUrl || exercise.video_url;

            if (!candidate || candidate === 'null') {
                return '';
            }

            return candidate;
        },

        // Название видео (если тренер указал своё название)
        getVideoTitle(exercise) {
            if (!exercise) return '';

            const trainerVideo = exercise.trainer_video || null;
            if (trainerVideo && trainerVideo.title) {
                return trainerVideo.title;
            }

            return exercise.name || '';
        },

        // Открытие модального окна для видео
        openVideoModal(url, title) {
            window.dispatchEvent(new CustomEvent('open-video-modal', {
                detail: { url: url, title: title }
            }));
        }
    }
}
</script>

<?php $__env->startSection("content"); ?>
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
    
    /* Мобильная версия карточки упражнения */
    .exercise-card-mobile {
        display: flex !important;
        gap: 1rem;
    }
    
    /* Десктопная версия карточки упражнения */
    .exercise-card-desktop {
        display: none !important;
    }
    
    @media (min-width: 768px) {
        .exercise-card-mobile {
            display: none !important;
        }
        
        .exercise-card-desktop {
            display: block !important;
        }
    }
    
    /* Медиа-запросы для просмотра упражнения */
    @media (min-width: 768px) {
        .exercise-view-desktop {
            display: flex !important;
        }
        .exercise-view-mobile {
            display: none !important;
        }
    }
    @media (max-width: 767px) {
        .exercise-view-desktop {
            display: none !important;
        }
        .exercise-view-mobile {
            display: block !important;
        }
            .p-6 {
            padding: 0.75rem !important;
        }
    }
</style>

<div x-data="exerciseApp()" x-init="init()" x-cloak class="space-y-6">
    
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
                @media (min-width: 768px) {
                    .filters-row {
                        display: grid !important;
                        grid-template-columns: 2fr 1fr 1fr 1fr !important;
                        gap: 1rem !important;
                        align-items: center !important;
                    }
                    .filters-row > div {
                        width: auto !important;
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
                            class="w-full px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value=""><?php echo e(__('common.all_categories')); ?></option>
                        <option value="Грудь"><?php echo e(__('common.chest')); ?></option>
                        <option value="Спина"><?php echo e(__('common.back_muscles')); ?></option>
                        <option value="Ноги(Бедра)"><?php echo e(__('common.legs_thighs')); ?></option>
                        <option value="Ноги(Икры)"><?php echo e(__('common.legs_calves')); ?></option>
                        <option value="Ягодицы"><?php echo e(__('common.glutes')); ?></option>
                        <option value="Плечи"><?php echo e(__('common.shoulders')); ?></option>
                        <option value="Руки(Бицепс)"><?php echo e(__('common.arms_biceps')); ?></option>
                        <option value="Руки(Трицепс)"><?php echo e(__('common.arms_triceps')); ?></option>
                        <option value="Руки(Предплечье)"><?php echo e(__('common.arms_forearm')); ?></option>
                        <option value="Пресс"><?php echo e(__('common.abs')); ?></option>
                        <option value="Шея"><?php echo e(__('common.neck')); ?></option>
                        <option value="Кардио"><?php echo e(__('common.cardio')); ?></option>
                        <option value="Гибкость"><?php echo e(__('common.flexibility')); ?></option>
                    </select>
                </div>
                
                <!-- Фильтр оборудования -->
                <div class="filter-container">
                    <select x-model="equipment"
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value=""><?php echo e(__('common.all_equipment')); ?></option>
                        <template x-for="eq in Array.from(new Set(exercises.filter(e => !category || e.category === category).map(e => e.equipment).filter(eq => eq && eq !== 'null'))).sort()" :key="eq">
                            <option :value="eq" x-text="getEquipmentTranslation(eq)"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Фильтр типа упражнений -->
                <div class="filter-container">
                    <select x-model="exerciseType"
                            class="w-full px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value=""><?php echo e(__('common.all_exercises')); ?></option>
                        <option value="system"><?php echo e(__('common.system_exercises')); ?></option>
                        <option value="custom"><?php echo e(__('common.user_exercises')); ?></option>
                    </select>
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

    <!-- СПИСОК УПРАЖНЕНИЙ -->
    <div x-show="currentView === 'list'" class="space-y-6">
        <div x-show="paginatedExercises.length > 0" style="display: grid; gap: 24px;" class="exercise-grid">
            <template x-for="exercise in paginatedExercises" :key="exercise.id">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 p-4 md:p-6" @click="showView(exercise.id)">
                    <!-- Мобильная версия -->
                    <div class="exercise-card-mobile">
                        <div x-show="exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null && exercise.image_url !== undefined && exercise.image_url !== 'undefined'" 
                             class="flex-shrink-0 w-24">
                            <img :src="`/storage/${exercise.image_url}`" 
                                 :alt="exercise.name"
                                 class="w-full h-32 object-contain rounded-lg">
                        </div>
                        <div class="flex items-center flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 cursor-pointer">
                                <span x-text="exercise.name"></span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Десктопная версия -->
                    <div class="exercise-card-desktop">
                        <div style="display: flex; gap: 1rem;">
                            <!-- Картинка слева -->
                            <div x-show="exercise.image_url && exercise.image_url !== 'null' && exercise.image_url !== null && exercise.image_url !== undefined && exercise.image_url !== 'undefined'" style="flex: 0 0 25%; max-width: 25%;">
                                <img :src="`/storage/${exercise.image_url}`" 
                                     :alt="exercise.name"
                                     class="w-full h-full object-cover rounded-lg"
                                     style="max-height: 200px;">
                            </div>
                            
                            <!-- Информация справа -->
                            <div style="flex: 1; display: flex; flex-direction: column;">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900 cursor-pointer hover:text-indigo-600 transition-colors" 
                                        @click.stop="showView(exercise.id)"
                                        :title="'Нажмите чтобы открыть: ' + exercise.name">
                                        <span x-text="exercise.name"></span>
                                    </h3>
                                    <button x-show="hasVideo(exercise)" 
                                            @click.stop="openVideoModal(getVideoUrl(exercise), getVideoTitle(exercise))"
                                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full transition-colors cursor-pointer ml-4">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Видео
                                    </button>
                                </div>
                                
                                <!-- Теги -->
                                <div class="flex flex-wrap gap-2 mb-4 justify-between">
                                    <div class="flex flex-wrap gap-2">
                                        <span x-show="exercise.category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800" x-text="exercise.category"></span>
                                        <span x-show="exercise.equipment" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800" x-text="exercise.equipment"></span>
                                    </div>
                                </div>
                                    
                                <!-- Группы мышц -->
                                <div class="text-sm text-gray-500 mb-4" x-show="exercise.muscle_groups && Array.isArray(exercise.muscle_groups) && exercise.muscle_groups.length > 0">
                                    <span x-text="'Группы мышц: '"></span><span class="text-black" x-text="Array.isArray(exercise.muscle_groups) ? exercise.muscle_groups.join(', ') : ''"></span>
                                </div>
                                
                                <!-- Кнопка просмотра внизу справа -->
                                <div class="flex space-x-2 mt-4" style="margin-top: auto; padding-top: 1rem;" @click.stop="">
                                    <button @click="showView(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                        Просмотр
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Пустое состояние -->
        <div x-show="paginatedExercises.length === 0" class="text-center py-12 bg-white rounded-2xl shadow-sm">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Упражнения не найдены</h3>
            <p class="text-gray-500">Попробуйте изменить фильтры или выполните несколько тренировок</p>
        </div>
        
        <!-- Пагинация -->
        <div x-show="totalPages > 1" class="flex justify-center items-center space-x-2 mt-6">
            <button @click="previousPage()" 
                    :disabled="currentPage === 1"
                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white transition-colors">
                Назад
            </button>
            
            <template x-for="page in visiblePages" :key="page">
                <button @click="goToPage(page)" 
                        :class="page === currentPage ? 'bg-indigo-600 text-white border-indigo-600' : page === '...' ? 'cursor-default' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'"
                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors"
                        :disabled="page === '...'"
                        x-text="page"></button>
            </template>
            
            <button @click="nextPage()" 
                    :disabled="currentPage === totalPages"
                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white transition-colors">
                Вперед
            </button>
        </div>
    </div>

    <!-- ПРОСМОТР УПРАЖНЕНИЯ -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <!-- Кнопки сверху -->
        <div class="flex items-center justify-between mb-6">
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                Назад к списку
            </button>
        </div>
        
        <div x-show="currentExercise" class="space-y-6">
            <!-- Название (только на мобилке) -->
            <h2 class="exercise-view-mobile text-3xl font-bold text-gray-900 text-center" x-text="currentExercise?.name || 'Упражнение'"></h2>
            
            <!-- Десктоп версия: картинки слева, информация справа -->
            <div class="exercise-view-desktop" style="display: flex; gap: 2rem;">
                <!-- Левая колонка: картинки и видео -->
                <div class="flex-shrink-0" style="width: 35%; max-width: 500px;">
                    <div class="space-y-4">
                        <!-- Главное изображение (скрывается если второе изображение - GIF) -->
                        <template x-if="currentExercise?.image_url && currentExercise.image_url !== 'null' && currentExercise.image_url !== null && currentExercise.image_url !== undefined && currentExercise.image_url !== 'undefined' && !(currentExercise?.image_url_2 && currentExercise.image_url_2.toLowerCase().endsWith('.gif'))">
                            <div>
                                <img :src="`/storage/${currentExercise.image_url}`" 
                                     :alt="currentExercise.name"
                                     class="w-full rounded-lg shadow-md"
                                     style="object-fit: contain;">
                            </div>
                        </template>
                        
                        <!-- Второе изображение -->
                        <template x-if="currentExercise?.image_url_2 && currentExercise.image_url_2 !== 'null' && currentExercise.image_url_2 !== null && currentExercise.image_url_2 !== undefined && currentExercise.image_url_2 !== 'undefined'">
                            <div>
                                <img :src="`/storage/${currentExercise.image_url_2}`" 
                                     :alt="currentExercise.name"
                                     class="w-full rounded-lg shadow-md"
                                     style="object-fit: contain;">
                            </div>
                        </template>
                        
                        <!-- Системное видео -->
                        <div x-show="hasVideo(currentExercise)">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Системное видео</p>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div x-show="isYouTubeUrl(getVideoUrl(currentExercise))" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                    <iframe :src="getYouTubeEmbedUrl(getVideoUrl(currentExercise))" 
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                            allowfullscreen>
                                    </iframe>
                                </div>
                                <div x-show="!isYouTubeUrl(getVideoUrl(currentExercise))" class="text-center py-4">
                                    <a :href="getVideoUrl(currentExercise)" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        Видео
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Правая колонка: информация -->
                <div class="flex-1 space-y-4">
                    <!-- Название для десктопа -->
                    <h2 class="text-3xl font-bold text-gray-900" x-text="currentExercise?.name || 'Упражнение'"></h2>
                    
                    <p class="text-gray-600" x-text="currentExercise?.description || 'Без описания'"></p>
                    
                    <!-- Информация об упражнении -->
                    <div class="grid grid-cols-2 gap-4">
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Группы мышц</h3>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="group in currentExercise?.muscle_groups || []" :key="group">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" x-text="group"></span>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Инструкции -->
                    <div x-show="currentExercise?.instructions">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Инструкции по выполнению</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700 whitespace-pre-line" x-text="currentExercise?.instructions"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Мобильная версия: всё по центру -->
            <div class="exercise-view-mobile space-y-6">
                <!-- Картинки по центру -->
                <div class="flex flex-col items-center gap-4">
                    <!-- Главное изображение (скрывается если второе изображение - GIF) -->
                    <template x-if="currentExercise?.image_url && currentExercise.image_url !== 'null' && currentExercise.image_url !== null && currentExercise.image_url !== undefined && currentExercise.image_url !== 'undefined' && !(currentExercise?.image_url_2 && currentExercise.image_url_2.toLowerCase().endsWith('.gif'))">
                        <div class="w-full">
                            <img :src="`/storage/${currentExercise.image_url}`" 
                                 :alt="currentExercise.name"
                                 class="w-full rounded-lg shadow-md mx-auto"
                                 style="object-fit: contain; max-height: 400px;">
                        </div>
                    </template>
                    
                    <!-- Второе изображение -->
                    <template x-if="currentExercise?.image_url_2 && currentExercise.image_url_2 !== 'null' && currentExercise.image_url_2 !== null && currentExercise.image_url_2 !== undefined && currentExercise.image_url_2 !== 'undefined'">
                        <div class="w-full">
                            <img :src="`/storage/${currentExercise.image_url_2}`" 
                                 :alt="currentExercise.name"
                                 class="w-full rounded-lg shadow-md mx-auto"
                                 style="object-fit: contain; max-height: 400px;">
                        </div>
                    </template>
                </div>
                
                <!-- Описание -->
                <div x-show="currentExercise?.description">
                    <p class="text-gray-600 text-center" x-text="currentExercise?.description"></p>
                </div>
                
                <!-- Информация об упражнении -->
                <div class="grid grid-cols-2 gap-4">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">Группы мышц</h3>
                    <div class="flex flex-wrap gap-2 justify-center">
                        <template x-for="group in currentExercise?.muscle_groups || []" :key="group">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" x-text="group"></span>
                        </template>
                    </div>
                </div>
                
                <!-- Инструкции -->
                <div x-show="currentExercise?.instructions">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">Инструкции по выполнению</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line" x-text="currentExercise?.instructions"></p>
                    </div>
                </div>
                
                <!-- Системное видео -->
                <div x-show="hasVideo(currentExercise)">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">Видео</h3>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div x-show="isYouTubeUrl(getVideoUrl(currentExercise))" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe :src="getYouTubeEmbedUrl(getVideoUrl(currentExercise))" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        <div x-show="!isYouTubeUrl(getVideoUrl(currentExercise))" class="text-center py-4">
                            <a :href="getVideoUrl(currentExercise)" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                Видео
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для видео -->
<div x-data="{ 
        videoModalOpen: false, 
        videoUrl: '', 
        videoTitle: '',
        getEmbedUrl(url) {
            if (!url) return '';
            let videoId = '';
            if (url.includes('youtube.com/watch?v=')) {
                videoId = url.split('v=')[1].split('&')[0];
            } else if (url.includes('youtu.be/')) {
                videoId = url.split('youtu.be/')[1].split('?')[0];
            } else if (url.includes('youtube.com/embed/')) {
                videoId = url.split('embed/')[1].split('?')[0];
            }
            return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
        }
     }" 
     x-show="videoModalOpen" 
     @open-video-modal.window="videoModalOpen = true; videoUrl = $event.detail.url; videoTitle = $event.detail.title"
     @close-video-modal.window="videoModalOpen = false; videoUrl = ''; videoTitle = ''"
     x-transition
     class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" 
     style="display: none;">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" x-text="videoTitle"></h3>
            <button @click="videoModalOpen = false; videoUrl = ''; videoTitle = ''" 
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="aspect-video bg-black">
            <iframe :src="getEmbedUrl(videoUrl)" 
                    class="w-full h-full" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
            </iframe>
        </div>
    </div>
</div>

<script>
function openVideoModal(url, title) {
    window.dispatchEvent(new CustomEvent('open-video-modal', {
        detail: { url: url, title: title }
    }));
}
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make("crm.layouts.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/athlete/exercises.blade.php ENDPATH**/ ?>
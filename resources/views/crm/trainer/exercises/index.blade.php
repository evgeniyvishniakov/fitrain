@extends("crm.layouts.app")

@section("title", __('common.exercises'))
@section("page-title", __('common.exercises'))

<script>
// SPA функциональность для упражнений
const VIDEO_EXT_REGEXP = /\.(mp4|webm|mov|m4v)$/i;

function isVideoMedia(path = '') {
    return VIDEO_EXT_REGEXP.test((path || '').toString().toLowerCase());
}

function renderMediaElement(path, altText = '', options = {}) {
    if (!path) return '';
    const { className = '', style = '', attributes = '' } = options;
    const classAttr = className ? ` class="${className}"` : '';
    const extraAttr = attributes ? ` ${attributes}` : '';
    const safeAlt = (altText || '').replace(/"/g, '&quot;');
    const baseStyle = style ? style.trim() : '';
    if (isVideoMedia(path)) {
        const combinedStyle = [baseStyle, 'pointer-events: none;'].filter(Boolean).join(' ');
        const styleAttr = combinedStyle ? ` style="${combinedStyle}"` : '';
        return `<video src="${path}"${classAttr}${styleAttr}${extraAttr} autoplay loop muted playsinline controlslist="nodownload noremoteplayback nofullscreen" disablePictureInPicture></video>`;
    }
    const styleAttr = baseStyle ? ` style="${baseStyle}"` : '';
    return `<img src="${path}" alt="${safeAlt}"${classAttr}${styleAttr}${extraAttr}>`;
}

// SPA функциональность для упражнений
function exerciseApp() {
    return {
        currentView: 'list', // list, create, edit, view, add-video
        touchStartX: null,
        touchStartY: null,
        touchHandlersSetup: false,
        touchStartTime: null,
        maxVerticalDeviation: 80,
        swipeHandled: false,
        swipeActivationThreshold: 120,
        swipeVisualLimit: 140,
        swipeTargetElement: null,
        swipeAnimationTimeout: null,
        boundTouchStart: null,
        boundTouchMove: null,
        boundTouchEnd: null,
        menuGesture: null,
        menuGestureHandled: false,
        menuSwipeThreshold: 60,
        menuIsOpen: false,
        menuObserver: null,
        menuCloseEdgeGuard: 60,
        popStateLocked: false,
        exercises: @json($allExercises),
        currentExercise: null,
        search: '',
        category: '',
        equipment: '',
        exerciseType: '',
        favoriteIds: @json($favoriteIds ?? []),
        currentPage: 1,
        itemsPerPage: 10,
        
        isVideoFile(path) {
            if (!path || path === 'null' || path === null) {
                return false;
            }
            return isVideoMedia(path);
        },

        hasUserVideo(exercise) {
            if (!exercise || !exercise.id) return false;
            return !!(this.userVideos && this.userVideos[exercise.id] && this.userVideos[exercise.id].video_url);
        },

        hasExerciseVideo(exercise) {
            if (!exercise) return false;
            const primary = this.getDisplayImage(exercise);
            const secondary = this.getDisplayImage2(exercise);
            if (this.isVideoFile(primary)) return true;
            if (this.isVideoFile(secondary)) return true;
            if (exercise.video_url) return true;
            return false;
        },

        shouldShowPrimaryImage(exercise) {
            if (!exercise) return false;
            const primary = this.getDisplayImage(exercise);
            if (!primary) return false;
            if (this.isVideoFile(primary)) return false;
            const secondary = this.getDisplayImage2(exercise);
            if (secondary && this.isVideoFile(secondary)) return false;
            if (secondary && typeof secondary === 'string' && secondary.toLowerCase().endsWith('.gif')) return false;
            if (exercise.video_url) return false;
            if (this.hasUserVideo(exercise)) return false;
            return true;
        },

        shouldShowSecondaryImage(exercise) {
            if (!exercise) return false;
            const secondary = this.getDisplayImage2(exercise);
            if (!secondary) return false;
            if (this.isVideoFile(secondary)) return false;
            if (exercise.video_url) return false;
            if (this.hasUserVideo(exercise)) return false;
            return true;
        },
        
        // Поля для пользовательского видео
        userVideoUrl: '',
        userVideoTitle: '',
        userVideoDescription: '',
        currentUserVideo: null,
        userVideos: {}, // Кэш пользовательских видео для всех упражнений
        lastScrollPositions: {
            list: 0,
            view: 0,
            addVideo: 0,
            create: 0,
            edit: 0,
        },
        lastView: 'list',
        
        // Поля формы
        formName: '',
        formDescription: '',
        formCategory: '',
        formEquipment: '',
        formMuscleGroupsText: '',
        formInstructions: '',
        formVideoUrl: '',
        formImage: null,
        formImagePreview: '',
        formImageUrl: '',
        formImage2: null,
        formImagePreview2: '',
        formImageUrl2: '',
        formFieldsConfig: ['weight', 'reps', 'sets', 'rest'], // По умолчанию
        
        // Получить изображение для отображения в списке
        // Для категории "Ягодицы" - приоритет женские, для остальных - мужские
        getDisplayImage(exercise) {
            if (!exercise) return null;
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
        },
        
        // Получить второе изображение для отображения в просмотре
        // Для категории "Ягодицы" - приоритет женские, для остальных - мужские
        getDisplayImage2(exercise) {
            if (!exercise) return null;
            const isGlutes = exercise.category === 'Ягодицы';
            
            if (isGlutes) {
                // Для категории "Ягодицы" - сначала второе женское
                if (exercise.image_url_female_2 && typeof exercise.image_url_female_2 === 'string' && exercise.image_url_female_2.trim() !== '' && exercise.image_url_female_2 !== 'null') {
                    return exercise.image_url_female_2;
                }
                // Если женского нет, проверяем мужское
                if (exercise.image_url_2 && typeof exercise.image_url_2 === 'string' && exercise.image_url_2.trim() !== '' && exercise.image_url_2 !== 'null') {
                    return exercise.image_url_2;
                }
            } else {
                // Для остальных категорий - сначала второе мужское
                if (exercise.image_url_2 && typeof exercise.image_url_2 === 'string' && exercise.image_url_2.trim() !== '' && exercise.image_url_2 !== 'null') {
                    return exercise.image_url_2;
                }
                // Если мужского нет, проверяем женское
                if (exercise.image_url_female_2 && typeof exercise.image_url_female_2 === 'string' && exercise.image_url_female_2.trim() !== '' && exercise.image_url_female_2 !== 'null') {
                    return exercise.image_url_female_2;
                }
            }
            return null;
        },
        
        // Навигация
        showList() {
            const previousView = this.currentView;
            this.clearSwipeAnimationTimeout();
            this.resetSwipeTransform(true);
            this.swipeTargetElement = null;
            this.closeMobileMenuIfOpen();
            this.currentView = 'list';
            this.currentExercise = null;
            this.$nextTick(() => {
                if ((previousView === 'view' || previousView === 'add-video' || previousView === 'create' || previousView === 'edit') && this.lastScrollPositions.list !== null) {
                    window.scrollTo({
                        top: this.lastScrollPositions.list,
                        behavior: 'auto'
                    });
                }
            });
            this.lastView = 'list';
        },
        
        showCreate() {
            if (this.currentView === 'list') {
                this.lastScrollPositions.list = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'view') {
                this.lastScrollPositions.view = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'add-video') {
                this.lastScrollPositions.addVideo = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'edit') {
                this.lastScrollPositions.edit = window.scrollY || window.pageYOffset || 0;
            }
            this.currentView = 'create';
            this.lastView = 'create';
            this.currentExercise = null;
            this.formName = '';
            this.formDescription = '';
            this.formCategory = '';
            this.formEquipment = '';
            this.formMuscleGroupsText = '';
            this.formInstructions = '';
            this.formVideoUrl = '';
            this.formImage = null;
            this.formImagePreview = '';
            this.formImageUrl = '';
            this.formImage2 = null;
            this.formImagePreview2 = '';
            this.formImageUrl2 = '';
            this.formFieldsConfig = ['weight', 'reps', 'sets', 'rest'];
            
            // Очищаем input файлов при открытии формы создания
            setTimeout(() => {
                const imageInput = document.querySelector('input[name="image"]');
                const imageInput2 = document.querySelector('input[name="image_2"]');
                if (imageInput) imageInput.value = '';
                if (imageInput2) imageInput2.value = '';
            }, 0);
            
            this.$nextTick(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        showEdit(exerciseId) {
            if (this.currentView === 'list') {
                this.lastScrollPositions.list = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'view') {
                this.lastScrollPositions.view = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'add-video') {
                this.lastScrollPositions.addVideo = window.scrollY || window.pageYOffset || 0;
            } else if (this.currentView === 'create') {
                this.lastScrollPositions.create = window.scrollY || window.pageYOffset || 0;
            }
            this.currentView = 'edit';
            this.lastView = 'edit';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.formName = this.currentExercise.name;
            this.formDescription = this.currentExercise.description || '';
            this.formCategory = this.currentExercise.category;
            this.formEquipment = this.currentExercise.equipment;
            this.formMuscleGroupsText = Array.isArray(this.currentExercise.muscle_groups) ? this.currentExercise.muscle_groups.join(', ') : '';
            this.formInstructions = this.currentExercise.instructions || '';
            this.formVideoUrl = this.currentExercise.video_url || '';
            this.formImage = null;
            // Используем приоритет: женское изображение, если есть, иначе мужское
            const displayImage = this.getDisplayImage(this.currentExercise);
            const displayImage2 = this.getDisplayImage2(this.currentExercise);
            this.formImagePreview = displayImage ? `/storage/${displayImage}` : '';
            this.formImageUrl = displayImage || '';
            this.formImage2 = null;
            this.formImagePreview2 = displayImage2 ? `/storage/${displayImage2}` : '';
            this.formImageUrl2 = displayImage2 || '';
            this.formFieldsConfig = this.currentExercise.fields_config || ['weight', 'reps', 'sets', 'rest'];
            
            // Очищаем input файлов при открытии формы редактирования
            setTimeout(() => {
                const imageInput = document.querySelector('input[name="image"]');
                const imageInput2 = document.querySelector('input[name="image_2"]');
                if (imageInput) imageInput.value = '';
                if (imageInput2) imageInput2.value = '';
            }, 0);
            
            this.$nextTick(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        showView(exerciseId) {
            if (this.currentView === 'list') {
                this.lastScrollPositions.list = window.scrollY || window.pageYOffset || 0;
            }
            this.clearSwipeAnimationTimeout();
            this.resetSwipeTransform(true);
            this.swipeTargetElement = null;
            this.currentView = 'view';
            this.lastView = 'view';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.$nextTick(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Загружаем пользовательское видео, если упражнение системное
            if (this.currentExercise && this.currentExercise.is_system) {
                this.loadUserVideo(exerciseId);
            } else {
                this.currentUserVideo = null;
            }
        },
        
        showAddVideo(exerciseId) {
            if (this.currentView === 'view') {
                this.lastScrollPositions.view = window.scrollY || window.pageYOffset || 0;
            }
            this.currentView = 'add-video';
            this.lastView = 'add-video';
            this.currentExercise = this.exercises.find(e => e.id === exerciseId);
            this.userVideoUrl = '';
            this.userVideoTitle = '';
            this.userVideoDescription = '';
            this.currentUserVideo = null;
            
            // Загружаем существующее видео, если есть
            this.loadUserVideo(exerciseId);
            
            this.$nextTick(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        // Фильтрация
        get filteredExercises() {
            let filtered = this.exercises;
            
            if (this.search) {
                const normalizedSearch = this.search.toLowerCase();
                filtered = filtered.filter(e => 
                    (e.name || '').toLowerCase().includes(normalizedSearch) ||
                    (e.description || '').toLowerCase().includes(normalizedSearch) ||
                    (e.category || '').toLowerCase().includes(normalizedSearch) ||
                    (e.equipment || '').toLowerCase().includes(normalizedSearch)
                );
            }
            
            if (this.category) {
                filtered = filtered.filter(e => (e.category || '') === this.category);
            }
            
            if (this.equipment) {
                filtered = filtered.filter(e => (e.equipment || '') === this.equipment);
            }
            
            if (this.exerciseType) {
                if (this.exerciseType === 'system') {
                    filtered = filtered.filter(e => e.is_system === true);
                } else if (this.exerciseType === 'user') {
                    filtered = filtered.filter(e => e.is_system === false);
                } else if (this.exerciseType === 'favorite') {
                    filtered = filtered.filter(e => this.favoriteIds.includes(e.id));
                }
            }
            
            return filtered;
        },
        
        // Доступные варианты оборудования по выбранной категории
        availableEquipments() {
            const equipmentsSet = new Set();
            (this.exercises || []).forEach(exercise => {
                if (!this.category || exercise.category === this.category) {
                    if (exercise.equipment) {
                        equipmentsSet.add(exercise.equipment);
                    }
                }
            });
            return Array.from(equipmentsSet).sort();
        },
        
        // Перевод оборудования на текущий язык
        getEquipmentTranslation(equipment) {
            if (!equipment) return '';
            const translations = {
                'Штанга': '{{ __('common.barbell') }}',
                'Гриф': '{{ __('common.barbell_bar') }}',
                'Трап-гриф': '{{ __('common.trap_bar') }}',
                'EZ-гриф': '{{ __('common.ez_bar') }}',
                'Отягощения': '{{ __('common.weight_plate') }}',
                'Гантели': '{{ __('common.dumbbells') }}',
                'Гири': '{{ __('common.kettlebells') }}',
                'Собственный вес': '{{ __('common.body_weight') }}',
                'Тренажер': '{{ __('common.machines') }}',
                'Машина Смита': '{{ __('common.smith_machine') }}',
                'Кроссовер / Блок': '{{ __('common.crossover_block') }}',
                'Скакалка': '{{ __('common.jump_rope') }}',
                'Турник': '{{ __('common.pull_up_bar') }}',
                'Брусья': '{{ __('common.parallel_bars') }}',
                'Скамейка': '{{ __('common.bench') }}',
                'Резина / Экспандер': '{{ __('common.resistance_band') }}'
            };
            return translations[equipment] || equipment;
        },

        setupTouchHandlers() {
            if (this.touchHandlersSetup) return;
            this.touchHandlersSetup = true;
            this.boundTouchStart = this.handleTouchStart.bind(this);
            this.boundTouchMove = this.handleTouchMove.bind(this);
            this.boundTouchEnd = this.handleTouchEnd.bind(this);
            const container = document.getElementById('trainer-exercises-root');
            if (container && window.CSS && CSS.supports('touch-action', 'pan-y')) {
                container.style.touchAction = 'pan-y';
            }
            document.addEventListener('touchstart', this.boundTouchStart, { passive: false, capture: true });
            document.addEventListener('touchmove', this.boundTouchMove, { passive: false, capture: true });
            document.addEventListener('touchend', this.boundTouchEnd, { passive: false, capture: true });
        },

        getEdgeThreshold() {
            const screenWidth = window.innerWidth || document.documentElement.clientWidth;
            if (screenWidth >= 1024) {
                // Для больших экранов делаем свайп практически по центру (до 70% ширины экрана, но не более 800px)
                return Math.min(Math.floor(screenWidth * 0.7), 800);
            } else if (screenWidth >= 768) {
                // Для средних экранов (до 60% ширины экрана, но не более 500px)
                return Math.min(Math.floor(screenWidth * 0.6), 500);
            } else {
                // Для маленьких экранов (телефоны) - до 50% ширины экрана, но не менее 150px и не более 300px
                return Math.max(150, Math.min(Math.floor(screenWidth * 0.5), 300));
            }
        },

        getSwipeTargetElement() {
            if (this.currentView === 'view') {
                return document.getElementById('trainer-exercise-view-section');
            }
            if (this.currentView === 'create' || this.currentView === 'edit') {
                return document.getElementById('trainer-exercise-form-section');
            }
            if (this.currentView === 'add-video') {
                return document.getElementById('trainer-exercise-add-video-section');
            }
            return null;
        },

        applySwipeTransform(distance) {
            const target = this.swipeTargetElement;
            if (!target) return;
            const clamped = Math.max(0, Math.min(distance, this.swipeVisualLimit));
            target.style.transform = `translateX(${clamped}px)`;
        },

        resetSwipeTransform(immediate = false, targetElement = null) {
            const target = targetElement || this.swipeTargetElement;
            if (!target) return;
            if (immediate) {
                target.style.transition = '';
                target.style.transform = '';
                return;
            }
            target.style.transition = 'transform 0.2s ease';
            requestAnimationFrame(() => {
                target.style.transform = 'translateX(0px)';
            });
            setTimeout(() => {
                target.style.transition = '';
                target.style.transform = '';
            }, 200);
        },

        clearSwipeAnimationTimeout() {
            if (this.swipeAnimationTimeout) {
                clearTimeout(this.swipeAnimationTimeout);
                this.swipeAnimationTimeout = null;
            }
        },

        syncMenuState() {
            const menu = document.getElementById('mobile-menu');
            this.menuIsOpen = !!(menu && menu.classList.contains('open'));
        },

        setupMenuObserver() {
            const menu = document.getElementById('mobile-menu');
            if (!menu || this.menuObserver) return;
            this.menuObserver = new MutationObserver(() => {
                this.syncMenuState();
            });
            this.menuObserver.observe(menu, { attributes: true, attributeFilter: ['class'] });
        },

        getMobileMenuWidth() {
            const menu = document.getElementById('mobile-menu');
            if (!menu) return 0;
            const content = menu.querySelector('.mobile-menu-content');
            return content ? content.offsetWidth || 0 : menu.offsetWidth || 0;
        },

        openMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            if (menu && !menu.classList.contains('open')) {
                menu.classList.add('open');
                this.menuIsOpen = true;
            }
        },

        closeMobileMenuIfOpen() {
            const menu = document.getElementById('mobile-menu');
            if (menu && menu.classList.contains('open')) {
                menu.classList.remove('open');
                this.menuIsOpen = false;
            }
        },

        isMobileMenuOpen() {
            return this.menuIsOpen;
        },

        handleTouchStart(event) {
            if (event.touches.length !== 1) return;
            if (this.popStateLocked) return;

            const touch = event.touches[0];
            const startX = touch.clientX;
            const startY = touch.clientY;
            const nearEdge = startX <= this.getEdgeThreshold();
            const menu = document.getElementById('mobile-menu');
            const menuContent = menu ? menu.querySelector('.mobile-menu-content') : null;
            const targetInsideMenu = menuContent ? menuContent.contains(event.target) : false;
            const isMenuToggle = event.target.closest('.mobile-menu-btn');
            const isMenuClose = event.target.closest('.mobile-menu-close');
            const menuOpen = this.isMobileMenuOpen();
            this.menuGesture = null;
            this.menuGestureHandled = false;

            if (isMenuToggle || isMenuClose) {
                return;
            }

            if (this.currentView === 'list') {
                if (menuOpen) {
                    const guard = this.menuCloseEdgeGuard;
                    const menuWidth = this.getMobileMenuWidth();
                    if (startX <= guard) {
                        event.preventDefault();
                        event.stopPropagation();
                        if (event.stopImmediatePropagation) {
                            event.stopImmediatePropagation();
                        }
                        this.menuGesture = null;
                        this.menuGestureHandled = false;
                        return;
                    }
                    if (targetInsideMenu || startX <= menuWidth + guard) {
                        this.menuGesture = null;
                        this.menuGestureHandled = false;
                        return;
                    }
                    this.menuGesture = 'close';
                } else {
                    // Блокируем системный жест "назад" с самого края (первые 60px), но разрешаем открытие меню
                    const guard = this.menuCloseEdgeGuard;
                    if (startX <= guard) {
                        // Блокируем системный жест "назад", но продолжаем обработку для открытия меню
                        event.preventDefault();
                        event.stopPropagation();
                        if (event.stopImmediatePropagation) {
                            event.stopImmediatePropagation();
                        }
                        // Не делаем return, чтобы меню могло открыться, если касание в пределах nearEdge
                    }
                    if (!nearEdge) {
                        return;
                    }
                    this.menuGesture = 'open';
                }

                this.clearSwipeAnimationTimeout();
                this.swipeHandled = false;
                this.touchStartX = startX;
                this.touchStartY = startY;
                this.touchStartTime = performance.now();
                this.swipeTargetElement = null;

                // Не блокируем события здесь, чтобы не мешать выделению текста
                // Блокировка будет только в handleTouchMove при реальном свайпе
                return;
            }

            if (!['view', 'create', 'edit', 'add-video'].includes(this.currentView)) return;
            
            // Блокируем системный жест "назад" с самого края (первые 60px), но разрешаем свайп назад
            const guard = this.menuCloseEdgeGuard;
            if (startX <= guard) {
                // Блокируем системный жест "назад", но продолжаем обработку для свайпа назад
                event.preventDefault();
                event.stopPropagation();
                if (event.stopImmediatePropagation) {
                    event.stopImmediatePropagation();
                }
                // Не делаем return, чтобы свайп назад мог работать, если касание в пределах nearEdge
            }
            
            if (!nearEdge) return;

            this.closeGlobalMobileMenu();
            this.clearSwipeAnimationTimeout();
            this.swipeHandled = false;
            this.touchStartX = startX;
            this.touchStartY = startY;
            this.touchStartTime = performance.now();
            this.swipeTargetElement = this.getSwipeTargetElement();
            if (this.swipeTargetElement) {
                this.swipeTargetElement.style.transition = 'transform 0s';
            }
            // Не блокируем события здесь, чтобы не мешать выделению текста
            // Блокировка будет только в handleTouchMove при реальном свайпе
        },

        handleTouchMove(event) {
            if (this.touchStartX === null) return;
            if (this.popStateLocked) return;
            if (this.currentView === 'list') {
                if (!this.menuGesture) return;
                if (this.menuGestureHandled) return;
                const touch = event.touches[0];
                const deltaX = touch.clientX - this.touchStartX;
                const deltaY = touch.clientY - (this.touchStartY ?? 0);
                if (Math.abs(deltaY) > this.maxVerticalDeviation) return;
                if (this.menuGesture === 'open' && deltaX > this.menuSwipeThreshold) {
                    this.openMobileMenu();
                    this.menuGestureHandled = true;
                } else if (this.menuGesture === 'close' && (this.touchStartX - touch.clientX) > this.menuSwipeThreshold) {
                    this.closeMobileMenuIfOpen();
                    this.menuGestureHandled = true;
                }
                // Блокируем события только при реальном движении (свайпе), чтобы не мешать выделению текста
                if (event && (Math.abs(deltaX) > 10 || Math.abs(deltaY) > 10)) {
                    event.preventDefault();
                    event.stopPropagation();
                    if (event.stopImmediatePropagation) {
                        event.stopImmediatePropagation();
                    }
                }
                return;
            }
            if (!['view', 'create', 'edit', 'add-video'].includes(this.currentView)) return;
            const touch = event.touches[0];
            const deltaX = Math.max(0, touch.clientX - this.touchStartX);
            const deltaY = touch.clientY - (this.touchStartY ?? 0);
            if (Math.abs(deltaY) > this.maxVerticalDeviation) return;
            if (this.swipeTargetElement) {
                this.applySwipeTransform(deltaX);
            }
            if (deltaX > this.swipeActivationThreshold && !this.swipeHandled) {
                this.handleSwipeRight(event, this.swipeTargetElement);
                return;
            }
            // Блокируем события только при реальном движении вправо (свайпе), чтобы не мешать выделению текста
            if (event && this.touchStartX <= this.getEdgeThreshold() && deltaX > 10) {
                event.preventDefault();
                event.stopPropagation();
                if (event.stopImmediatePropagation) {
                    event.stopImmediatePropagation();
                }
            }
        },

        handleTouchEnd(event) {
            // Проверка: если касание закончилось на кнопке, не обрабатываем свайп
            const isButton = event.target.closest('button') || event.target.tagName === 'BUTTON';
            if (isButton && this.touchStartX !== null) {
                this.resetSwipeTransform(true);
                this.swipeTargetElement = null;
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                this.menuGesture = null;
                this.menuGestureHandled = false;
                return;
            }

            if (this.popStateLocked) {
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                this.swipeTargetElement = null;
                this.menuGesture = null;
                this.menuGestureHandled = false;
                return;
            }
            if (this.currentView === 'list') {
                if (this.menuGesture && !this.menuGestureHandled && event.changedTouches.length === 1) {
                    const touch = event.changedTouches[0];
                    const deltaX = touch.clientX - this.touchStartX;
                    const deltaY = touch.clientY - (this.touchStartY ?? 0);
                    if (Math.abs(deltaY) <= this.maxVerticalDeviation) {
                        if (this.menuGesture === 'open' && deltaX > this.menuSwipeThreshold) {
                            this.openMobileMenu();
                        } else if (this.menuGesture === 'close' && (this.touchStartX - touch.clientX) > this.menuSwipeThreshold) {
                            this.closeMobileMenuIfOpen();
                        }
                    }
                }
                this.menuGesture = null;
                this.menuGestureHandled = false;
                this.swipeTargetElement = null;
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                return;
            }

            if (this.touchStartX === null || event.changedTouches.length !== 1) {
                this.resetSwipeTransform(true);
                this.swipeTargetElement = null;
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                return;
            }
            const targetElement = this.swipeTargetElement;
            if (this.swipeHandled) {
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                this.swipeHandled = false;
                return;
            }
            const touch = event.changedTouches[0];
            const startX = this.touchStartX;
            const startY = this.touchStartY ?? 0;
            const deltaX = touch.clientX - startX;
            const deltaY = touch.clientY - startY;
            const duration = performance.now() - (this.touchStartTime ?? performance.now());
            this.touchStartX = null;
            this.touchStartY = null;
            this.touchStartTime = null;

            if (Math.abs(deltaY) > this.maxVerticalDeviation) {
                this.resetSwipeTransform(false, targetElement);
                this.swipeTargetElement = null;
                return;
            }
            if (startX > this.getEdgeThreshold()) {
                this.resetSwipeTransform(false, targetElement);
                this.swipeTargetElement = null;
                return;
            }
            if (deltaX > this.swipeActivationThreshold && duration < 600) {
                this.handleSwipeRight(event, targetElement);
                return;
            }
            this.resetSwipeTransform(false, targetElement);
            this.swipeTargetElement = null;
        },

        handleSwipeRight(event, targetElement = null) {
            if (!['view', 'create', 'edit', 'add-video'].includes(this.currentView)) return;
            if (event) {
                event.preventDefault();
                event.stopPropagation();
                if (event.stopImmediatePropagation) {
                    event.stopImmediatePropagation();
                }
            }
            this.swipeHandled = true;
            this.closeGlobalMobileMenu();
            this.clearSwipeAnimationTimeout();
            const target = targetElement || this.swipeTargetElement || this.getSwipeTargetElement();
            if (target) {
                this.swipeTargetElement = target;
                target.style.transition = 'transform 0.18s ease';
                requestAnimationFrame(() => {
                    target.style.transform = 'translateX(100%)';
                });
                this.swipeAnimationTimeout = setTimeout(() => {
                    // Если свайп из add-video, возвращаемся к view того же упражнения
                    if (this.currentView === 'add-video' && this.currentExercise) {
                        this.showView(this.currentExercise.id);
                    } else {
                    this.showList();
                    }
                    this.resetSwipeTransform(true, target);
                    this.swipeTargetElement = null;
                    this.swipeAnimationTimeout = null;
                }, 180);
            } else {
                // Если свайп из add-video, возвращаемся к view того же упражнения
                if (this.currentView === 'add-video' && this.currentExercise) {
                    this.showView(this.currentExercise.id);
            } else {
                this.showList();
                }
            }
        },

        closeGlobalMobileMenu() {
            this.closeMobileMenuIfOpen();
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
                
                const formData = new FormData();
                formData.append('name', this.formName);
                formData.append('description', this.formDescription);
                formData.append('category', this.formCategory);
                formData.append('equipment', this.formEquipment);
                formData.append('instructions', this.formInstructions);
                formData.append('video_url', this.formVideoUrl);
                
                muscleGroups.forEach((group, index) => {
                    formData.append(`muscle_groups[${index}]`, group);
                });
                
                this.formFieldsConfig.forEach((field, index) => {
                    formData.append('fields_config[' + index + ']', field);
                });
                
                // Обработка первого изображения
                const fileInput = document.querySelector('input[name="image"]');
                const hasNewFile = fileInput && fileInput.files[0];
                
                if (hasNewFile) {
                    formData.append('image', fileInput.files[0]);
                }
                
                // Только если НЕТ нового файла И formImageUrl пустой - удаляем картинку
                if (!hasNewFile && this.currentExercise && this.currentExercise.id && !this.formImageUrl) {
                    formData.append('remove_image', '1');
                }
                
                // Обработка второго изображения
                const fileInput2 = document.querySelector('input[name="image_2"]');
                const hasNewFile2 = fileInput2 && fileInput2.files[0];
                
                
                
                if (hasNewFile2) {
                    formData.append('image_2', fileInput2.files[0]);
                }
                
                // Только если НЕТ нового файла И formImageUrl2 пустой - удаляем вторую картинку
                if (!hasNewFile2 && this.currentExercise && this.currentExercise.id && !this.formImageUrl2) {
                    formData.append('remove_image_2', '1');
                }
                
                const url = this.currentExercise && this.currentExercise.id ? 
                    `/exercises/${this.currentExercise.id}` : '/exercises';
                const method = 'POST';
                
                if (this.currentExercise && this.currentExercise.id) {
                    formData.append('_method', 'PUT');
                }
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.authorization_error') }}',
                            message: '{{ __('common.reauthorization_required') }}'
                        }
                    }));
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.server_response_error') }}',
                            message: '{{ __('common.invalid_server_response') }}'
                        }
                    }));
                    return;
                }
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentExercise && this.currentExercise.id ? '{{ __('common.exercise_updated') }}' : '{{ __('common.exercise_created') }}',
                            message: this.currentExercise && this.currentExercise.id ? 
                                '{{ __('common.exercise_successfully_updated') }}' : 
                                '{{ __('common.exercise_successfully_added') }}'
                        }
                    }));
                    
                    // Обновляем список упражнений
                    
                    if (this.currentExercise && this.currentExercise.id) {
                        // Редактирование - обновляем существующее
                        const index = this.exercises.findIndex(e => e.id === this.currentExercise.id);
                        if (index !== -1) {
                            this.exercises[index] = result.exercise;
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
                            title: '{{ __('common.saving_error') }}',
                            message: result.message || '{{ __('common.exercise_saving_error') }}'
                        }
                    }));
                }
            } catch (error) {
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '{{ __('common.error') }}',
                        message: '{{ __('common.exercise_saving_error') }}'
                    }
                }));
            }
        },
        
        // Удаление
        deleteExercise(id) {
            const exercise = this.exercises.find(e => e.id === id);
            const exerciseName = exercise ? exercise.name : '{{ __('common.exercise') }}';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '{{ __('common.delete') }} {{ __('common.exercise') }}',
                    message: `{{ __('common.are_you_sure_delete_exercise') }} "${exerciseName}"?`,
                    confirmText: '{{ __('common.delete') }}',
                    cancelText: '{{ __('common.cancel') }}',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/exercises/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.authorization_error') }}',
                            message: '{{ __('common.reauthorization_required') }}'
                        }
                    }));
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.server_response_error') }}',
                            message: '{{ __('common.invalid_server_response') }}'
                        }
                    }));
                    return;
                }
                
                if (result.success) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '{{ __('common.exercise_deleted') }}',
                            message: result.message || '{{ __('common.exercise_successfully_deleted') }}'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.exercises = this.exercises.filter(e => e.id !== id);
                    
                    // Если удалили все упражнения на текущей странице, переходим на предыдущую
                    if (this.paginatedExercises.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке (упражнение используется)
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.deleting_error') }}',
                            message: result.message || '{{ __('common.exercise_in_use') }}'
                        }
                    }));
                }
            } catch (error) {
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '{{ __('common.error') }}',
                        message: '{{ __('common.exercise_deleting_error') }}'
                    }
                }));
            }
        },
        
        // Избранное
        isFavorite(exerciseId) {
            return this.favoriteIds.includes(exerciseId);
        },
        
        async toggleFavorite(exerciseId) {
            const isFav = this.isFavorite(exerciseId);
            const url = isFav 
                ? `/exercises/${exerciseId}/favorite`
                : `/exercises/${exerciseId}/favorite`;
            const method = isFav ? 'DELETE' : 'POST';
            
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (isFav) {
                        // Удаляем из избранного
                        this.favoriteIds = this.favoriteIds.filter(id => id !== exerciseId);
                    } else {
                        // Добавляем в избранное
                        this.favoriteIds.push(exerciseId);
                    }
                    
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: isFav ? 'Удалено из избранного' : 'Добавлено в избранное',
                            message: result.message
                        }
                    }));
                }
            } catch (error) {
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: 'Ошибка',
                        message: 'Не удалось обновить избранное'
                    }
                }));
            }
        },
        
        // Простой метод для открытия модального окна (как в тренировках)
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
                document.body.removeChild(modal);
            });
            
            // Добавляем обработчик клика на фон для закрытия
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
            
            // Создаем контент видео
            const videoContent = document.createElement('div');
            
            // Проверяем, YouTube ли это
            if (url.includes('youtube.com') || url.includes('youtu.be')) {
                let videoId = '';
                if (url.includes('youtube.com/watch?v=')) {
                    videoId = url.split('v=')[1].split('&')[0];
                } else if (url.includes('youtu.be/')) {
                    videoId = url.split('youtu.be/')[1].split('?')[0];
                }
                
                if (videoId) {
                    videoContent.innerHTML = `
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe src="https://www.youtube.com/embed/${videoId}" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    `;
                } else {
                    videoContent.innerHTML = `
                        <div style="text-align: center;">
                            <a href="${url}" target="_blank" rel="noopener noreferrer" 
                               style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none;">
                                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                Открыть видео
                            </a>
                        </div>
                    `;
                }
            } else {
                videoContent.innerHTML = `
                    <div style="text-align: center;">
                        <a href="${url}" target="_blank" rel="noopener noreferrer" 
                           style="display: inline-flex; align-items: center; padding: 12px 24px; background: #dc2626; color: white; border-radius: 8px; text-decoration: none;">
                            <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            Открыть видео
                        </a>
                    </div>
                `;
            }
            
            // Собираем модальное окно
            content.appendChild(header);
            content.appendChild(videoContent);
            modal.appendChild(content);
            document.body.appendChild(modal);
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
            this.setupTouchHandlers();
            this.syncMenuState();
            this.setupMenuObserver();
            // Загружаем пользовательские видео при инициализации
            this.loadAllUserVideos();
            
            // Сбрасываем пагинацию при изменении фильтров
            this.$watch('search', () => {
                this.currentPage = 1;
            });
            
            this.$watch('category', () => {
                this.currentPage = 1;
                const available = this.availableEquipments();
                if (this.equipment && !available.includes(this.equipment)) {
                    this.equipment = '';
                }
            });
            
            this.$watch('equipment', () => {
                this.currentPage = 1;
            });
            
            this.$watch('exerciseType', () => {
                this.currentPage = 1;
            });
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
        
        // Проверка что это YouTube URL
        isYouTubeUrl(url) {
            if (!url) return false;
            return url.includes('youtube.com') || url.includes('youtu.be');
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
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    return;
                }
                
                if (result.success && result.videos) {
                    this.userVideos = {};
                    result.videos.forEach(video => {
                        this.userVideos[video.exercise_id] = video;
                    });
                }
            } catch (error) {
            }
        },
        
        async loadUserVideo(exerciseId) {
            try {
                const response = await fetch(`/exercises/${exerciseId}/user-video`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    return;
                }
                
                if (result.success && result.video) {
                    this.currentUserVideo = result.video;
                    this.userVideoUrl = result.video.video_url;
                    this.userVideoTitle = result.video.title || '';
                    this.userVideoDescription = result.video.description || '';
                }
            } catch (error) {
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
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(videoData)
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.authorization_error') }}',
                            message: '{{ __('common.reauthorization_required') }}'
                        }
                    }));
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.server_response_error') }}',
                            message: '{{ __('common.invalid_server_response') }}'
                        }
                    }));
                    return;
                }
                
                if (result.success) {
                    // Обновляем текущее видео
                    this.currentUserVideo = result.video;
                    
                    // Обновляем кэш пользовательских видео для этого упражнения
                    this.userVideos[this.currentExercise.id] = result.video;
                    
                    // Обновляем текущее упражнение, чтобы видео сразу отображалось
                    if (this.currentExercise) {
                        // Обновляем объект упражнения, чтобы видео отображалось
                        this.currentExercise = { ...this.currentExercise };
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '{{ __('common.video_saved') }}',
                            message: result.message
                        }
                    }));
                    
                    // Переключаемся на просмотр
                    this.showView(this.currentExercise.id);
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.saving_error') }}',
                            message: result.message || '{{ __('common.video_saving_error') }}'
                        }
                    }));
                }
            } catch (error) {
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '{{ __('common.error') }}',
                        message: '{{ __('common.video_saving_error') }}'
                    }
                }));
            }
        },
        
        async deleteUserVideo() {
            try {
                const response = await fetch(`/exercises/${this.currentExercise.id}/user-video`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                
                // Проверяем, не HTML ли это (например, страница входа)
                if (text.trim().startsWith('<!DOCTYPE html>') || text.trim().startsWith('<html')) {
                    return;
                }
                
                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    return;
                }
                
                if (result.success) {
                    // Удаляем из кэша пользовательских видео
                    delete this.userVideos[this.currentExercise.id];
                    
                    // Очищаем поля
                    this.currentUserVideo = null;
                    this.userVideoUrl = '';
                    this.userVideoTitle = '';
                    this.userVideoDescription = '';
                    
                    // Обновляем текущее упражнение, чтобы видео исчезло из просмотра
                    if (this.currentExercise) {
                        this.currentExercise = { ...this.currentExercise };
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '{{ __('common.video_deleted') }}',
                            message: result.message
                        }
                    }));
                    
                    // Переключаемся на просмотр
                    this.showView(this.currentExercise.id);
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '{{ __('common.deleting_error') }}',
                            message: result.message || '{{ __('common.video_deleting_error') }}'
                        }
                    }));
                }
            } catch (error) {
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '{{ __('common.error') }}',
                        message: '{{ __('common.video_deleting_error') }}'
                    }
                }));
            }
        },
        
        // Обработка выбора изображения
        handleImageSelect(event) {
            const file = event.target.files[0];
            if (file) {
                // Валидация типа файла
                if (!file.type.startsWith('image/')) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка',
                            message: 'Пожалуйста, выберите файл изображения'
                        }
                    }));
                    event.target.value = '';
                    return;
                }
                
                // Валидация размера файла (макс 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка',
                            message: 'Размер файла не должен превышать 5MB'
                        }
                    }));
                    event.target.value = '';
                    return;
                }
                
                this.formImage = file;
                
                // Создаём превью
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.formImagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        // Удаление изображения
        removeImage() {
            this.formImage = null;
            this.formImagePreview = '';
            const fileInput = document.querySelector('input[name="image"]');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        // Обработка выбора второго изображения
        handleImageSelect2(event) {
            const file = event.target.files[0];
            if (file) {
                // Валидация типа файла
                if (!file.type.startsWith('image/')) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка',
                            message: 'Пожалуйста, выберите файл изображения'
                        }
                    }));
                    event.target.value = '';
                    return;
                }
                
                // Валидация размера файла (макс 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка',
                            message: 'Размер файла не должен превышать 5MB'
                        }
                    }));
                    event.target.value = '';
                    return;
                }
                
                this.formImage2 = file;
                
                // Создаём превью
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.formImagePreview2 = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        // Удаление второго изображения
        removeImage2() {
            this.formImage2 = null;
            this.formImagePreview2 = '';
            const fileInput = document.querySelector('input[name="image_2"]');
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        // Переключение поля (добавить/удалить) с сохранением правильного порядка
        toggleField(field) {
            const index = this.formFieldsConfig.indexOf(field);
            if (index > -1) {
                // Удаляем поле из массива
                this.formFieldsConfig.splice(index, 1);
            } else {
                // Добавляем поле и сортируем согласно фиксированному порядку
                this.formFieldsConfig.push(field);
                
                // Фиксированный порядок полей
                const fieldOrder = ['weight', 'reps', 'sets', 'rest', 'time', 'distance', 'tempo'];
                this.formFieldsConfig.sort((a, b) => fieldOrder.indexOf(a) - fieldOrder.indexOf(b));
            }
        }
    }
}
</script>

@section("header-actions")
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
@endsection

@section("content")
<div id="trainer-exercises-root" x-data="exerciseApp()" x-init="init()" x-cloak class="space-y-6">
    
    <!-- Фильтры и поиск -->
    <div x-show="currentView === 'list'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3">
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
                        width: 150px !important;
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
                           placeholder="{{ __('common.search_exercises') }}" 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр категории -->
                <div class="filter-container">
                    <select x-model="category" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">{{ __('common.all_categories') }}</option>
                        <option value="Грудь">{{ __('common.chest') }}</option>
                        <option value="Спина">{{ __('common.back_muscles') }}</option>
                        <option value="Ноги(Бедра)">{{ __('common.legs_thighs') }}</option>
                        <option value="Ноги(Икры)">{{ __('common.legs_calves') }}</option>
                        <option value="Ягодицы">{{ __('common.glutes') }}</option>
                        <option value="Плечи">{{ __('common.shoulders') }}</option>
                        <option value="Руки(Бицепс)">{{ __('common.arms_biceps') }}</option>
                        <option value="Руки(Трицепс)">{{ __('common.arms_triceps') }}</option>
                        <option value="Руки(Предплечье)">{{ __('common.arms_forearm') }}</option>
                        <option value="Пресс">{{ __('common.abs') }}</option>
                        <option value="Шея">{{ __('common.neck') }}</option>
                        <option value="Кардио">{{ __('common.cardio') }}</option>
                        <option value="Гибкость">{{ __('common.flexibility') }}</option>
                    </select>
                </div>
                
                <!-- Фильтр оборудования -->
                <div class="filter-container">
                    <select x-model="equipment" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">{{ __('common.all_equipment') }}</option>
                        <template x-for="eq in availableEquipments().filter(e => e && e !== 'null')" :key="eq">
                            <option :value="eq" x-text="getEquipmentTranslation(eq)"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Фильтр типа упражнений -->
                <div class="filter-container">
                    <select x-model="exerciseType" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">{{ __('common.all_exercises') }}</option>
                        <option value="system">{{ __('common.system_exercises') }}</option>
                        <option value="user">{{ __('common.user_exercises') }}</option>
                        <option value="favorite">{{ __('common.favorite_exercises') }}</option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    @if(auth()->user()->hasRole('trainer'))
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            {{ __('common.create_exercise') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search || category || equipment" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">{{ __('common.active_filters') }}</span>
                <span x-show="search" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ __('common.search') }}: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-1 text-blue-600 hover:text-blue-800">×</button>
                </span>
                <span x-show="category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ __('common.category') }}: <span x-text="category"></span>
                    <button @click="category = ''" class="ml-1 text-green-600 hover:text-green-800">×</button>
                </span>
                <span x-show="equipment" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    {{ __('common.equipment') }}: <span x-text="equipment"></span>
                    <button @click="equipment = ''" class="ml-1 text-purple-600 hover:text-purple-800">×</button>
                </span>
            </div>
        </div>
    </div>

    <!-- Список упражнений -->
    <div x-show="currentView === 'list'" class="space-y-6">
        <div x-show="paginatedExercises.length > 0" style="display: grid; gap: 24px;" class="exercise-grid">
            <template x-for="exercise in paginatedExercises" :key="exercise.id">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 p-4 md:p-6" @click="showView(exercise.id)">
                    <!-- Мобильная версия -->
                    <div class="mobile-only" style="gap: 1rem;">
                        <div x-show="getDisplayImage(exercise)" class="exercise-card-thumbnail">
                            <template x-if="!isVideoFile(getDisplayImage(exercise))">
                                <img :src="`/storage/${getDisplayImage(exercise)}`" 
                                     :alt="exercise.name"
                                     class="exercise-card-thumbnail__image">
                            </template>
                            <template x-if="isVideoFile(getDisplayImage(exercise))">
                                <video :src="`/storage/${getDisplayImage(exercise)}`" 
                                       class="exercise-card-thumbnail__image"
                                       autoplay loop muted playsinline></video>
                            </template>
                        </div>
                        <div class="flex items-center flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 cursor-pointer">
                                <span x-text="exercise.name"></span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Десктопная версия -->
                    <div class="desktop-only">
                        <div class="exercise-card-desktop">
                            <!-- Картинка -->
                            <div x-show="getDisplayImage(exercise)" class="exercise-card-thumbnail exercise-card-thumbnail--desktop">
                                <template x-if="!isVideoFile(getDisplayImage(exercise))">
                                    <img :src="`/storage/${getDisplayImage(exercise)}`" 
                                         :alt="exercise.name"
                                         class="exercise-card-thumbnail__image">
                                </template>
                                <template x-if="isVideoFile(getDisplayImage(exercise))">
                                    <video :src="`/storage/${getDisplayImage(exercise)}`" 
                                           class="exercise-card-thumbnail__image"
                                           autoplay loop muted playsinline></video>
                                </template>
                            </div>
                            
                            <!-- Информация -->
                            <div class="exercise-card-content">
                                <!-- Заголовок -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl font-semibold text-gray-900 cursor-pointer hover:text-indigo-600 transition-colors" 
                                        @click.stop="showView(exercise.id)"
                                        :title="'Нажмите чтобы открыть: ' + exercise.name">
                                        <span x-text="exercise.name"></span>
                                    </h3>
                                    <button x-show="hasVideo(exercise)" 
                                            @click="openSimpleModal(getVideoUrl(exercise), getVideoTitle(exercise))"
                                            class="inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 text-red-700 text-xs rounded-full transition-colors cursor-pointer ml-4">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        {{ __('common.video') }}
                                    </button>
                                </div>
                            
                                        <!-- Теги -->
                                        <div class="flex flex-wrap gap-2 mb-4 justify-between exercise-badge-row">
                                            <div class="flex flex-wrap gap-2 exercise-tag-group">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800" x-text="exercise.category"></span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800" x-text="exercise.equipment"></span>
                                            </div>
                                            <span x-show="exercise.is_system" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-help exercise-system-tag"
                                                  title="Системное упражнение нельзя редактировать или удалять">
                                                Системное
                                            </span>
                                            <span x-show="!exercise.is_system" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 exercise-user-tag"
                                                  title="Пользовательское упражнение можно редактировать и удалять">
                                                Пользовательское
                                            </span>
                                        </div>
                                        
                                        <!-- Группы мышц -->
                                        <div class="text-sm text-gray-500 exercise-muscle-groups" x-show="exercise.muscle_groups && Array.isArray(exercise.muscle_groups) && exercise.muscle_groups.length > 0">
                                            <span x-text="'Группы мышц: '"></span><span class="text-black" x-text="Array.isArray(exercise.muscle_groups) ? exercise.muscle_groups.join(', ') : ''"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Кнопки -->
                                <div class="flex space-x-2 exercise-actions" @click.stop="">
                                    <button @click="showView(exercise.id)" class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                        {{ __('common.view') }}
                                    </button>
                                    @if(auth()->user()->hasRole('trainer'))
                                        <button x-show="currentExercise && !currentExercise.is_system && currentExercise.trainer_id === {{ auth()->id() }}"
                                                @click="showEdit(currentExercise.id)"
                                                class="px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                            {{ __('common.edit') }}
                                        </button>
                                        <button x-show="currentExercise && !currentExercise.is_system && currentExercise.trainer_id === {{ auth()->id() }}"
                                                @click="deleteExercise(currentExercise.id)"
                                                class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                            {{ __('common.delete') }}
                                        </button>
                                    @endif
                                    
                                    <!-- Кнопка избранного -->
                                    <button @click.stop="toggleFavorite(exercise.id)" 
                                            class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:opacity-70 rounded-lg border"
                                            :class="isFavorite(exercise.id) ? 'bg-yellow-50 border-yellow-300' : 'bg-gray-50 border-gray-300'"
                                            :title="isFavorite(exercise.id) ? 'Удалить из избранного' : 'Добавить в избранное'">
                                        <!-- Заполненная звезда (в избранном) -->
                                        <svg x-show="isFavorite(exercise.id)" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <!-- Пустая звезда (не в избранном) -->
                                        <svg x-show="!isFavorite(exercise.id)" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
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
                    {{ __('common.create') }} {{ __('common.first_exercise') }}
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
    <div id="trainer-exercise-form-section" x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3">
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900" x-text="currentView === 'create' ? '{{ __('common.create_exercise') }}' : '{{ __('common.edit_exercise') }}'"></h2>
                <p class="mt-2 text-gray-600" x-text="currentView === 'create' ? 'Добавьте новое упражнение в базу' : 'Внесите изменения в упражнение'"></p>
            </div>
            <button x-show="currentView === 'edit'" 
                    @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                {{ __('common.back_to_list') }}
            </button>
        </div>
        
        <form @submit.prevent="saveExercise()" class="space-y-6">
            <div class="space-y-6">
                <!-- Название и ссылка на видео в одном ряду -->
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.exercise_name') }} *</label>
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
                
                <!-- Загрузка главного изображения -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Главное изображение упражнения</label>
                    
                    <!-- Текущая картинка при редактировании -->
                    <div x-show="currentView === 'edit' && formImageUrl && formImageUrl !== '/storage/' && formImageUrl !== '/storage/undefined' && formImageUrl !== '/storage/null'" class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm text-gray-600">Текущее изображение:</p>
                            <button type="button"
                                    @click="formImageUrl = ''; formImagePreview = ''"
                                    class="text-xs text-red-600 hover:text-red-800 font-medium">
                                Удалить картинку
                            </button>
                        </div>
                        <template x-if="formImagePreview || formImageUrl">
                            <img :src="formImagePreview || formImageUrl" 
                                 alt="Текущая картинка"
                                 class="max-w-xs max-h-32 object-contain rounded-lg border border-gray-300">
                        </template>
                        <p class="text-xs text-gray-500 mt-2">Загрузите новый файл ниже, чтобы заменить</p>
                    </div>
                    
                    <input type="file" 
                           name="image"
                           accept="image/*"
                           @change="handleImageSelect($event)"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <p class="mt-1 text-sm text-gray-500">Максимальный размер: 5MB. Форматы: JPG, PNG, GIF</p>
                    
                    <!-- Превью нового изображения при создании -->
                    <div x-show="currentView === 'create' && formImagePreview" class="mt-4">
                        <p class="text-xs text-gray-500 mb-1">Превью:</p>
                        <img :src="formImagePreview" alt="Превью" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    </div>
                </div>
                
                <!-- Загрузка второго изображения -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Второе изображение (необязательно)</label>
                    
                    <!-- Текущая вторая картинка при редактировании -->
                    <div x-show="currentView === 'edit' && formImageUrl2 && formImageUrl2 !== '/storage/' && formImageUrl2 !== '/storage/undefined' && formImageUrl2 !== '/storage/null'" class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-start justify-between mb-2">
                            <p class="text-sm text-gray-600">Текущее второе изображение:</p>
                            <button type="button"
                                    @click="formImageUrl2 = ''; formImagePreview2 = ''"
                                    class="text-xs text-red-600 hover:text-red-800 font-medium">
                                Удалить картинку
                            </button>
                        </div>
                        <template x-if="formImagePreview2 || formImageUrl2">
                            <img :src="formImagePreview2 || formImageUrl2" 
                                 alt="Текущая вторая картинка"
                                 class="max-w-xs max-h-32 object-contain rounded-lg border border-gray-300">
                        </template>
                        <p class="text-xs text-gray-500 mt-2">Загрузите новый файл ниже, чтобы заменить</p>
                    </div>
                    
                    <input type="file" 
                           name="image_2"
                           accept="image/*"
                           @change="handleImageSelect2($event)"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <p class="mt-1 text-sm text-gray-500">Дополнительное изображение. Макс 5MB. Форматы: JPG, PNG, GIF</p>
                    
                    <!-- Превью второго изображения при создании -->
                    <div x-show="currentView === 'create' && formImagePreview2" class="mt-4">
                        <p class="text-xs text-gray-500 mb-1">Превью:</p>
                        <img :src="formImagePreview2" alt="Превью 2" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.category') }} *</label>
                        <select x-model="formCategory" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">{{ __('common.select_category') }}</option>
                            <option value="Грудь">{{ __('common.chest') }}</option>
                            <option value="Спина">{{ __('common.back_muscles') }}</option>
                            <option value="Ноги(Бедра)">{{ __('common.legs_thighs') }}</option>
                            <option value="Ноги(Икры)">{{ __('common.legs_calves') }}</option>
                            <option value="Ягодицы">{{ __('common.glutes') }}</option>
                            <option value="Плечи">{{ __('common.shoulders') }}</option>
                            <option value="Руки(Бицепс)">{{ __('common.arms_biceps') }}</option>
                            <option value="Руки(Трицепс)">{{ __('common.arms_triceps') }}</option>
                            <option value="Руки(Предплечье)">{{ __('common.arms_forearm') }}</option>
                            <option value="Пресс">{{ __('common.abs') }}</option>
                            <option value="Шея">{{ __('common.neck') }}</option>
                            <option value="Кардио">{{ __('common.cardio') }}</option>
                            <option value="Гибкость">{{ __('common.flexibility') }}</option>
                        </select>
                    </div>
                    
                    <!-- Оборудование -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.equipment') }}</label>
                        <select x-model="formEquipment" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">{{ __('common.select_equipment') }}</option>
                            <option value="Штанга">{{ __('common.barbell') }}</option>
                            <option value="Гриф">{{ __('common.barbell_bar') }}</option>
                            <option value="Трап-гриф">{{ __('common.trap_bar') }}</option>
                            <option value="EZ-гриф">{{ __('common.ez_bar') }}</option>
                            <option value="Отягощения">{{ __('common.weight_plate') }}</option>
                            <option value="Гантели">{{ __('common.dumbbells') }}</option>
                            <option value="Гири">{{ __('common.kettlebells') }}</option>
                            <option value="Собственный вес">{{ __('common.body_weight') }}</option>
                            <option value="Тренажер">{{ __('common.machines') }}</option>
                            <option value="Машина Смита">{{ __('common.smith_machine') }}</option>
                            <option value="Кроссовер / Блок">{{ __('common.crossover_block') }}</option>
                            <option value="Скакалка">{{ __('common.jump_rope') }}</option>
                            <option value="Турник">{{ __('common.pull_up_bar') }}</option>
                            <option value="Брусья">{{ __('common.parallel_bars') }}</option>
                            <option value="Скамейка">{{ __('common.bench') }}</option>
                            <option value="Резина / Экспандер">{{ __('common.resistance_band') }}</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.description') }}</label>
                    <textarea x-model="formDescription" 
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                </div>
                
                <!-- Инструкции -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.execution_instructions') }}</label>
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
                        <!-- Вес -->
                        <label class="field-card" :class="formFieldsConfig.includes('weight') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('weight')" 
                                   :checked="formFieldsConfig.includes('weight')"
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
                        
                        <!-- Повторения -->
                        <label class="field-card" :class="formFieldsConfig.includes('reps') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('reps')" 
                                   :checked="formFieldsConfig.includes('reps')"
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
                        
                        <!-- Подходы -->
                        <label class="field-card" :class="formFieldsConfig.includes('sets') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('sets')" 
                                   :checked="formFieldsConfig.includes('sets')"
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
                        
                        <!-- Отдых -->
                        <label class="field-card" :class="formFieldsConfig.includes('rest') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('rest')" 
                                   :checked="formFieldsConfig.includes('rest')"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('rest') ? 'bg-purple-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('rest') ? 'text-purple-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('rest') ? 'text-purple-900' : 'text-gray-900"">{{ __('common.rest') }} ({{ __('common.min') }})</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('rest') ? 'text-purple-600' : 'text-gray-500"">Время отдыха</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Время -->
                        <label class="field-card" :class="formFieldsConfig.includes('time') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('time')" 
                                   :checked="formFieldsConfig.includes('time')"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('time') ? 'bg-blue-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('time') ? 'text-blue-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('time') ? 'text-blue-900' : 'text-gray-900"">Время (мин)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('time') ? 'text-blue-600' : 'text-gray-500"">Продолжительность</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Дистанция -->
                        <label class="field-card" :class="formFieldsConfig.includes('distance') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('distance')" 
                                   :checked="formFieldsConfig.includes('distance')"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('distance') ? 'bg-emerald-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('distance') ? 'text-emerald-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('distance') ? 'text-emerald-900' : 'text-gray-900"">Дистанция (м)</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('distance') ? 'text-emerald-600' : 'text-gray-500"">Пройденное расстояние</div>
                                </div>
                            </div>
                        </label>
                        
                        <!-- Темп -->
                        <label class="field-card" :class="formFieldsConfig.includes('tempo') ? 'field-card-selected' : 'field-card-unselected'">
                            <input type="checkbox" 
                                   @click.prevent="toggleField('tempo')" 
                                   :checked="formFieldsConfig.includes('tempo')"
                                   class="hidden">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     :class="formFieldsConfig.includes('tempo') ? 'bg-pink-100' : 'bg-gray-100'">
                                    <svg class="w-5 h-5" :class="formFieldsConfig.includes('tempo') ? 'text-pink-600' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sm" :class="formFieldsConfig.includes('tempo') ? 'text-pink-900' : 'text-gray-900"">Темп/Скорость</div>
                                    <div class="text-xs" :class="formFieldsConfig.includes('tempo') ? 'text-pink-600' : 'text-gray-500"">Скорость выполнения</div>
                                </div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Быстрые шаблоны -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Быстрые шаблоны</h4>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    @click="formFieldsConfig = ['weight', 'reps', 'sets', 'rest']"
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
                    <span x-text="currentView === 'create' ? '{{ __('common.create') }}' : '{{ __('common.save') }}'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Форма добавления пользовательского видео -->
    <div id="trainer-exercise-add-video-section" x-show="currentView === 'add-video'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Добавить видео</h2>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.video_title') }}</label>
                    <input type="text" 
                           x-model="userVideoTitle" 
                           placeholder="Например: Правильная техника выполнения"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Описание видео -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.video_description') }}</label>
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
                        @click="showView(currentExercise?.id)" 
                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 md:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="add-video-cancel-text">Отмена</span>
                </button>
                <button x-show="currentUserVideo" 
                        type="button" 
                        @click="deleteUserVideo()" 
                        class="px-6 py-3 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                    <span class="add-video-delete-text-mobile">Удалить</span>
                    <span class="add-video-delete-text-desktop">{{ __('common.delete_video') }}</span>
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentUserVideo ? 'Обновить' : '{{ __('common.save_video') }}'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Просмотр упражнения -->
    <div id="trainer-exercise-view-section" x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3">
        <!-- Кнопки сверху -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <button x-show="currentExercise && currentExercise.is_system"
                        @click="showAddVideo(currentExercise.id)" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                    {{ __('common.add_video') }}
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <button @click.stop="toggleFavorite(currentExercise?.id)" 
                        class="px-3 py-2 text-sm font-medium transition-all duration-200 hover:opacity-70 rounded-lg border"
                        :class="isFavorite(currentExercise?.id) ? 'bg-yellow-50 border-yellow-300' : 'bg-gray-50 border-gray-300'"
                        :title="isFavorite(currentExercise?.id) ? 'Удалить из избранного' : 'Добавить в избранное'">
                    <!-- Заполненная звезда (в избранном) -->
                    <svg x-show="isFavorite(currentExercise?.id)" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <!-- Пустая звезда (не в избранном) -->
                    <svg x-show="!isFavorite(currentExercise?.id)" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </button>
                
            </div>
        </div>

        <div x-show="currentExercise" class="space-y-6">
            <!-- Название (только на мобилке) -->
            <h2 class="exercise-view-mobile text-3xl font-bold text-gray-900 text-center" x-text="currentExercise?.name || 'Упражнение'"></h2>
            
            <!-- Десктоп версия: картинки слева, информация справа -->
            <div class="exercise-view-desktop gap-8">
                <!-- Левая колонка: картинки и видео -->
                <div class="flex-shrink-0" style="width: 35%; max-width: 500px;">
                    <div class="space-y-4">
                        <!-- Второе изображение (в зависимости от пола) -->
                        <template x-if="getDisplayImage2(currentExercise) && !isVideoFile(getDisplayImage2(currentExercise))">
                            <div>
                                <img :src="`/storage/${getDisplayImage2(currentExercise)}`" 
                                     :alt="currentExercise.name"
                                     class="w-full rounded-lg shadow-md"
                                     style="object-fit: contain;">
                            </div>
                        </template>
                        <template x-if="getDisplayImage2(currentExercise) && isVideoFile(getDisplayImage2(currentExercise))">
                            <div>
                                <video :src="`/storage/${getDisplayImage2(currentExercise)}`" 
                                       class="w-full rounded-lg shadow-md"
                                       style="object-fit: contain; pointer-events: none;"
                                       autoplay loop muted playsinline controlslist="nodownload noremoteplayback nofullscreen" disablePictureInPicture></video>
                            </div>
                        </template>
                        
                        <!-- Пользовательское видео (приоритет) -->
                        <div x-show="userVideos[currentExercise?.id]?.video_url">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Ваше видео</p>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div x-show="isYouTubeUrl(userVideos[currentExercise?.id]?.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                    <iframe :src="getYouTubeEmbedUrl(userVideos[currentExercise?.id]?.video_url)" 
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                            allowfullscreen>
                                    </iframe>
                                </div>
                                <div x-show="!isYouTubeUrl(userVideos[currentExercise?.id]?.video_url)" class="text-center py-4">
                                    <a :href="userVideos[currentExercise?.id]?.video_url" 
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
                        
                        <!-- Системное видео (если нет пользовательского) -->
                        <div x-show="currentExercise?.video_url && !userVideos[currentExercise?.id]?.video_url">
                            <p class="text-xs text-gray-500 mb-1 font-medium">Системное видео</p>
                            <div class="bg-gray-50 rounded-lg p-2">
                                <div x-show="isYouTubeUrl(currentExercise?.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                    <iframe :src="getYouTubeEmbedUrl(currentExercise?.video_url)" 
                                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                            allowfullscreen>
                                    </iframe>
                                </div>
                                <div x-show="!isYouTubeUrl(currentExercise?.video_url)" class="text-center py-4">
                                    <a :href="currentExercise?.video_url" 
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
                            <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('common.category') }}</h3>
                            <p class="text-lg font-semibold text-gray-900" x-text="currentExercise?.category"></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('common.equipment') }}</h3>
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
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('common.execution_instructions') }}</h3>
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
                    <!-- Второе изображение (в зависимости от пола) -->
                    <template x-if="getDisplayImage2(currentExercise) && !isVideoFile(getDisplayImage2(currentExercise))">
                        <div class="w-full">
                            <img :src="`/storage/${getDisplayImage2(currentExercise)}`" 
                                 :alt="currentExercise.name"
                                 class="w-full rounded-lg shadow-md mx-auto"
                                 style="object-fit: contain; max-height: 400px;">
                        </div>
                    </template>
                    <template x-if="getDisplayImage2(currentExercise) && isVideoFile(getDisplayImage2(currentExercise))">
                        <div class="w-full">
                            <video :src="`/storage/${getDisplayImage2(currentExercise)}`" 
                                   class="w-full rounded-lg shadow-md mx-auto"
                                   style="object-fit: contain; max-height: 400px; pointer-events: none;"
                                   autoplay loop muted playsinline controlslist="nodownload noremoteplayback nofullscreen" disablePictureInPicture></video>
                        </div>
                    </template>
                
                    <!-- Пользовательское видео (приоритет) - сразу под изображением -->
                    <div x-show="userVideos[currentExercise?.id]?.video_url" class="w-full">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">Видео</h3>
                    <p class="text-xs text-gray-500 mb-1 text-center">Ваше видео</p>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div x-show="isYouTubeUrl(userVideos[currentExercise?.id]?.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe :src="getYouTubeEmbedUrl(userVideos[currentExercise?.id]?.video_url)" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        <div x-show="!isYouTubeUrl(userVideos[currentExercise?.id]?.video_url)" class="text-center py-4">
                            <a :href="userVideos[currentExercise?.id]?.video_url" 
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
                
                    <!-- Системное видео (если нет пользовательского) - сразу под изображением -->
                    <div x-show="currentExercise?.video_url && !userVideos[currentExercise?.id]?.video_url" class="w-full">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">Видео</h3>
                    <p class="text-xs text-gray-500 mb-1 text-center">Системное видео</p>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <div x-show="isYouTubeUrl(currentExercise?.video_url)" class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe :src="getYouTubeEmbedUrl(currentExercise?.video_url)" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                        <div x-show="!isYouTubeUrl(currentExercise?.video_url)" class="text-center py-4">
                            <a :href="currentExercise?.video_url" 
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
                
                <!-- Описание -->
                <div x-show="currentExercise?.description">
                    <p class="text-gray-600 text-center" x-text="currentExercise?.description"></p>
                </div>
                
                <!-- Информация об упражнении -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('common.category') }}</h3>
                        <p class="text-lg font-semibold text-gray-900" x-text="currentExercise?.category"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-500 mb-1">{{ __('common.equipment') }}</h3>
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 text-center">{{ __('common.execution_instructions') }}</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-line" x-text="currentExercise?.instructions"></p>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки действий внизу -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-6 border-t border-gray-200">
                <button @click="showList()" 
                        data-swipe-ignore="true"
                        class="self-start inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span class="back-button-label">{{ __('common.back_to_list') }}</span>
                </button>
                <div class="flex items-center justify-end gap-3 flex-wrap">
                    <button x-show="currentExercise && !currentExercise.is_system && currentExercise.trainer_id === {{ auth()->id() }}" 
                            @click="showEdit(currentExercise.id)" 
                            class="px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        Редактировать
                    </button>
                    <button x-show="currentExercise && !currentExercise.is_system && currentExercise.trainer_id === {{ auth()->id() }}" 
                            @click="deleteExercise(currentExercise.id)" 
                            class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
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
}

/* Медиа-запросы для мобильной и десктопной версии */
.mobile-only {
    display: flex !important;
}

.desktop-only {
    display: none !important;
}

@media (min-width: 768px) {
    .mobile-only {
        display: none !important;
    }
    
    .desktop-only {
        display: block !important;
    }
}

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

/* Превью изображений в карточках упражнений */
.exercise-card-desktop {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 1.5rem;
    width: 100%;
}

@media (orientation: landscape) and (max-width: 1180px) {
    .exercise-card-desktop {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .exercise-card-thumbnail {
        width: 100%;
        max-width: 100%;
        padding: 1.25rem 1rem;
    }

    .exercise-card-thumbnail__image {
        max-width: min(520px, 90%);
        max-height: 240px;
    }

    .exercise-card-content .exercise-actions {
        width: 100%;
        justify-content: space-between;
    }

    .exercise-card-content .exercise-actions button {
        margin-left: 0;
    }

}

@media (orientation: portrait) and (min-width: 768px) and (max-width: 1024px) {
    .exercise-card-content {
        width: 100%;
    }

    .exercise-card-content > .exercise-actions {
        margin-top: 1rem;
        width: 100%;
        justify-content: space-between;
    }

    .exercise-card-content > .exercise-actions button {
        margin-left: 0;
    }
}

.exercise-card-content {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    width: 100%;
    align-items: flex-start;
    text-align: left;
}

.exercise-card-content .exercise-actions {
    display: flex;
    gap: 0.5rem;
}

.exercise-card-thumbnail {
    flex: 0 0 auto;
    width: min(30vw, 200px);
    max-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 12px 24px -12px rgba(15, 23, 42, 0.45);
}

.exercise-card-thumbnail__image {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
}

.exercise-card-thumbnail--desktop {
    width: min(26vw, 220px);
    max-width: 220px;
}

@media (min-width: 1024px) {
    .exercise-card-thumbnail {
        width: min(22vw, 220px);
        max-width: 220px;
    }

    .exercise-card-thumbnail__image {
        max-height: 250px;
    }

    .exercise-card-thumbnail--desktop {
        width: min(20vw, 240px);
        max-width: 240px;
    }
}

.exercise-badge-row {
    width: 100%;
}

.exercise-tag-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.exercise-system-tag,
.exercise-user-tag {
    display: inline-flex;
}

.exercise-muscle-groups {
    display: none !important;
}

@media (min-width: 1366px) and (hover: hover) and (pointer: fine) {
    .exercise-muscle-groups {
        display: block !important;
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

/* Кнопка Отмена в форме добавления видео */
@media (min-width: 768px) {
    .add-video-cancel-text {
        display: inline-block !important;
    }
}

@media (max-width: 767px) {
    .add-video-cancel-text {
        display: none !important;
    }
}

/* Кнопка Удалить в форме добавления видео */
@media (min-width: 768px) {
    .add-video-delete-text-mobile {
        display: none !important;
    }
    
    .add-video-delete-text-desktop {
        display: inline-block !important;
    }
}

@media (max-width: 767px) {
    .add-video-delete-text-mobile {
        display: inline-block !important;
    }
    
    .add-video-delete-text-desktop {
        display: none !important;
    }
}
</style>

@endsection
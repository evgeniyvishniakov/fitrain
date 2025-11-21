@extends("crm.layouts.app")

@section("title", __('common.calendar'))
@section("page-title", __('common.calendar'))

<script>
// SPA функциональность для календаря
function calendarApp() {
    return {
        currentDate: '{{ $currentDate }}',
        workoutsByDay: @json($workoutsByDay),
        athletes: @json($athletes),
        selectedAthleteId: '',
        isTrainer: {{ auth()->user()->hasRole('trainer') ? 'true' : 'false' }},
        serverToday: '{{ now()->format('Y-m-d') }}',
        showWorkoutModal: false,
        currentWorkoutDetails: {},
        
        // Календарные данные
        weekDays: ['{{ __('common.monday') }}', '{{ __('common.tuesday') }}', '{{ __('common.wednesday') }}', '{{ __('common.thursday') }}', '{{ __('common.friday') }}', '{{ __('common.saturday') }}', '{{ __('common.sunday') }}'],
        
        get currentMonthYear() {
            const date = new Date(this.currentDate);
            const monthNames = {
                0: '{{ __('common.january') }}',
                1: '{{ __('common.february') }}',
                2: '{{ __('common.march') }}',
                3: '{{ __('common.april') }}',
                4: '{{ __('common.may') }}',
                5: '{{ __('common.june') }}',
                6: '{{ __('common.july') }}',
                7: '{{ __('common.august') }}',
                8: '{{ __('common.september') }}',
                9: '{{ __('common.october') }}',
                10: '{{ __('common.november') }}',
                11: '{{ __('common.december') }}'
            };
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            return `${month} ${year}`;
        },
        
        getYesterdayDate() {
            // Используем серверную дату напрямую без создания Date объекта
            return this.serverToday;
        },
        
        
        get calendarDays() {
            const date = new Date(this.currentDate);
            const year = date.getFullYear();
            const month = date.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            
            const days = [];
            
            // Добавляем пустые ячейки перед первым днем месяца
            // getDay() возвращает: 0=воскресенье, 1=понедельник, ..., 6=суббота
            let firstDayOfWeek = firstDay.getDay();
            firstDayOfWeek = firstDayOfWeek === 0 ? 7 : firstDayOfWeek; // Преобразуем воскресенье в 7
            
            // Добавляем пустые ячейки (firstDayOfWeek - 1), чтобы 1 число попало в правильную колонку
            for (let i = 1; i < firstDayOfWeek; i++) {
                days.push({
                    date: null,
                    day: null,
                    isCurrentMonth: false,
                    isToday: false,
                    workouts: [],
                    isEmpty: true
                });
            }
            
            // Показываем дни текущего месяца
            const today = new Date();
            const todayString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
            
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const dayString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const workouts = this.workoutsByDay[dayString] || [];
                
                days.push({
                    date: dayString,
                    day: day,
                    isCurrentMonth: true,
                    isToday: dayString === todayString,
                    workouts: workouts,
                    isEmpty: false
                });
            }
            
            return days;
        },
        
        // Навигация по месяцам
        previousMonth() {
            const date = new Date(this.currentDate);
            date.setMonth(date.getMonth() - 1);
            this.currentDate = date.toISOString().split('T')[0];
            this.loadWorkouts();
        },
        
        nextMonth() {
            const date = new Date(this.currentDate);
            date.setMonth(date.getMonth() + 1);
            this.currentDate = date.toISOString().split('T')[0];
            this.loadWorkouts();
        },
        
        goToToday() {
            // Используем серверную дату вместо клиентской
            this.currentDate = this.serverToday;
            this.loadWorkouts();
        },
        
        // Загрузка тренировок
        async loadWorkouts() {
            try {
                const startDate = new Date(this.currentDate);
                startDate.setDate(1);
                const endDate = new Date(this.currentDate);
                endDate.setMonth(endDate.getMonth() + 1);
                endDate.setDate(0);
                
                const params = new URLSearchParams({
                    start_date: startDate.toISOString().split('T')[0],
                    end_date: endDate.toISOString().split('T')[0]
                });
                
                if (this.selectedAthleteId) {
                    params.append('athlete_id', this.selectedAthleteId);
                }
                
                const response = await fetch(`/calendar/workouts?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    // Группируем тренировки по дням
                    const grouped = {};
                    data.workouts.forEach(workout => {
                        if (!grouped[workout.date]) {
                            grouped[workout.date] = [];
                        }
                        grouped[workout.date].push(workout);
                    });
                    this.workoutsByDay = grouped;
                }
            } catch (error) {
                console.error('Ошибка загрузки тренировок:', error);
            }
        },
        
        // Показ деталей тренировки
        showWorkoutDetails(workout) {
            this.currentWorkoutDetails = workout;
            this.showWorkoutModal = true;
        },
        
        // Показать все тренировки дня
        showAllWorkoutsForDay(day) {
            // Показываем все тренировки дня в модальном окне
            this.currentWorkoutDetails = {
                date: day.date,
                workouts: day.workouts,
                isMultiple: true
            };
            this.showWorkoutModal = true;
        },
        
        // Вспомогательные методы
        getStatusColor(status) {
            const colors = {
                'completed': 'green',
                'planned': 'blue',
                'cancelled': 'red'
            };
            return colors[status] || 'gray';
        },
        
        getStatusText(status) {
            const texts = {
                'completed': '{{ __('common.completed_status') }}',
                'planned': '{{ __('common.planned_status') }}',
                'cancelled': '{{ __('common.cancelled_status') }}'
            };
            return texts[status] || status;
        },
        
        // Форматирование даты для отображения (без учета часового пояса)
        formatDate(dateString) {
            if (!dateString) return '';
            
            // Если дата уже в формате YYYY-MM-DD, просто форматируем её
            if (typeof dateString === 'string' && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month, day] = dateString.split('-');
                return `${day}.${month}.${year}`;
            }
            
            // Если дата в формате YYYY-MM-DDTHH:mm:ss.sssZ (ISO), извлекаем только дату без времени
            if (typeof dateString === 'string' && dateString.includes('T')) {
                const datePart = dateString.split('T')[0];
                if (datePart.match(/^\d{4}-\d{2}-\d{2}$/)) {
                    const [year, month, day] = datePart.split('-');
                    return `${day}.${month}.${year}`;
                }
            }
            
            return dateString;
        }
    }
}
</script>

<script>
(function () {
    if (window.__fitrainDashboardMenuSetup) return;
    window.__fitrainDashboardMenuSetup = true;

    const getEdgeThreshold = () => {
        const screenWidth = window.innerWidth || document.documentElement.clientWidth;
        if (screenWidth >= 1024) {
            return Math.min(Math.floor(screenWidth * 0.7), 800);
        } else if (screenWidth >= 768) {
            return Math.min(Math.floor(screenWidth * 0.6), 500);
        } else {
            return Math.max(150, Math.min(Math.floor(screenWidth * 0.5), 300));
        }
    };
    const menuSwipeThreshold = 60;
    const menuCloseEdgeGuard = 60;
    const maxVerticalDeviation = 80;

    let touchStartX = null;
    let touchStartY = null;
    let menuGesture = null;
    let menuGestureHandled = false;
    let menuIsOpen = false;
    let menuObserver = null;

    const getMenu = () => document.getElementById('mobile-menu');

    const syncMenuState = () => {
        const menu = getMenu();
        menuIsOpen = !!(menu && menu.classList.contains('open'));
    };

    const setupMenuObserver = () => {
        const menu = getMenu();
        if (!menu || menuObserver) return;
        menuObserver = new MutationObserver(syncMenuState);
        menuObserver.observe(menu, { attributes: true, attributeFilter: ['class'] });
    };

    const getMobileMenuWidth = () => {
        const menu = getMenu();
        if (!menu) return 0;
        const content = menu.querySelector('.mobile-menu-content');
        return content ? content.offsetWidth || 0 : menu.offsetWidth || 0;
    };

    const openMobileMenu = () => {
        const menu = getMenu();
        if (menu && !menu.classList.contains('open')) {
            menu.classList.add('open');
            menuIsOpen = true;
        }
    };

    const closeMobileMenuIfOpen = () => {
        const menu = getMenu();
        if (menu && menu.classList.contains('open')) {
            menu.classList.remove('open');
            menuIsOpen = false;
        }
    };

    const preventEvent = (event) => {
        event.preventDefault();
        event.stopPropagation();
        if (event.stopImmediatePropagation) {
            event.stopImmediatePropagation();
        }
    };

    const resetTouchState = () => {
        touchStartX = null;
        touchStartY = null;
        menuGesture = null;
        menuGestureHandled = false;
    };

    const handleTouchStart = (event) => {
        if (event.touches.length !== 1) return;

        // Проверка: если клик по интерактивному элементу, не обрабатываем свайп (проверяем в самом начале)
        const isInteractive = event.target.closest('button') || 
                              event.target.closest('a') || 
                              event.target.closest('input') || 
                              event.target.closest('select') || 
                              event.target.closest('textarea') ||
                              event.target.tagName === 'BUTTON' ||
                              event.target.tagName === 'A' ||
                              event.target.tagName === 'INPUT' ||
                              event.target.tagName === 'SELECT' ||
                              event.target.tagName === 'TEXTAREA';
        if (isInteractive) {
            return;
        }

        // Проверка: если клик по ячейке календаря или элементам внутри нее, не обрабатываем свайп
        const isCalendarCell = event.target.closest('.calendar-day-cell') ||
                              event.target.closest('.desktop-workouts') ||
                              event.target.closest('.mobile-workouts') ||
                              event.target.closest('[class*="cursor-pointer"]') ||
                              (event.target.closest('[x-data*="calendarApp"]') && 
                               (event.target.closest('.calendar-day-cell') || 
                                event.target.closest('[class*="cursor-pointer"]')));
        if (isCalendarCell) {
            return;
        }

        syncMenuState();

        const touch = event.touches[0];
        const startX = touch.clientX;
        const startY = touch.clientY;
        const menu = getMenu();
        const menuContent = menu ? menu.querySelector('.mobile-menu-content') : null;
        const targetInsideMenu = menuContent ? menuContent.contains(event.target) : false;
        const isMenuToggle = event.target.closest('.mobile-menu-btn');
        const isMenuClose = event.target.closest('.mobile-menu-close');

        menuGesture = null;
        menuGestureHandled = false;

        if (isMenuToggle || isMenuClose) {
            resetTouchState();
            return;
        }

        if (menuIsOpen) {
            if (startX <= menuCloseEdgeGuard) {
                preventEvent(event);
                resetTouchState();
                return;
            }
            const menuWidth = getMobileMenuWidth();
            if (targetInsideMenu || startX <= menuWidth + menuCloseEdgeGuard) {
                resetTouchState();
                menuGestureHandled = true;
                return;
            }
            menuGesture = 'close';
        } else {
            // Блокируем системный жест "назад" с самого края (первые 60px), но разрешаем открытие меню
            if (startX <= menuCloseEdgeGuard) {
                // Блокируем системный жест "назад", но продолжаем обработку для открытия меню
                preventEvent(event);
                // Не делаем return, чтобы меню могло открыться, если касание в пределах nearEdge
            }
            
            // Проверяем, что касание в пределах зоны свайпа (как в тренировках)
            const nearEdge = startX <= getEdgeThreshold();
            if (!nearEdge) {
                resetTouchState();
                return;
            }
            menuGesture = 'open';
        }

        touchStartX = startX;
        touchStartY = startY;
        menuGestureHandled = false;
        // Не блокируем события здесь, чтобы не мешать выделению текста
        // Блокировка будет только в handleTouchMove при реальном свайпе
    };

    const handleTouchMove = (event) => {
        if (touchStartX === null) return;
        if (!menuGesture) return;

        // Проверка: если касание идет по интерактивному элементу, сбрасываем свайп
        const isInteractive = event.target.closest('button') || 
                              event.target.closest('a') || 
                              event.target.closest('input') || 
                              event.target.closest('select') || 
                              event.target.closest('textarea') ||
                              event.target.tagName === 'BUTTON' ||
                              event.target.tagName === 'A' ||
                              event.target.tagName === 'INPUT' ||
                              event.target.tagName === 'SELECT' ||
                              event.target.tagName === 'TEXTAREA';
        if (isInteractive) {
            resetTouchState();
            return;
        }

        // Проверка: если касание идет по ячейке календаря, сбрасываем свайп
        const isCalendarCell = event.target.closest('.calendar-day-cell') ||
                              event.target.closest('.desktop-workouts') ||
                              event.target.closest('.mobile-workouts') ||
                              event.target.closest('[class*="cursor-pointer"]');
        if (isCalendarCell) {
            resetTouchState();
            return;
        }

        const touch = event.touches[0];
        const deltaX = touch.clientX - touchStartX;
        const deltaY = touch.clientY - (touchStartY ?? 0);
        if (Math.abs(deltaY) > maxVerticalDeviation) return;

        if (!menuGestureHandled) {
            if (menuGesture === 'open' && deltaX > menuSwipeThreshold) {
                openMobileMenu();
                menuGestureHandled = true;
            } else if (menuGesture === 'close' && (touchStartX - touch.clientX) > menuSwipeThreshold) {
                closeMobileMenuIfOpen();
                menuGestureHandled = true;
            }
        }

        // Блокируем события только при реальном движении (свайпе), чтобы не мешать выделению текста
        if (!menuGestureHandled && (Math.abs(deltaX) > 10 || Math.abs(deltaY) > 10)) {
            preventEvent(event);
        }
    };

    const handleTouchEnd = (event) => {
        // Проверка: если касание закончилось на интерактивном элементе, не обрабатываем свайп
        const isInteractive = event.target.closest('button') || 
                              event.target.closest('a') || 
                              event.target.closest('input') || 
                              event.target.closest('select') || 
                              event.target.closest('textarea') ||
                              event.target.tagName === 'BUTTON' ||
                              event.target.tagName === 'A' ||
                              event.target.tagName === 'INPUT' ||
                              event.target.tagName === 'SELECT' ||
                              event.target.tagName === 'TEXTAREA';
        if (isInteractive && touchStartX !== null) {
            resetTouchState();
            return;
        }

        // Проверка: если касание закончилось на ячейке календаря, не обрабатываем свайп
        const isCalendarCell = event.target.closest('.calendar-day-cell') ||
                              event.target.closest('.desktop-workouts') ||
                              event.target.closest('.mobile-workouts') ||
                              event.target.closest('[class*="cursor-pointer"]');
        if (isCalendarCell && touchStartX !== null) {
            resetTouchState();
            return;
        }

        if (touchStartX !== null && menuGesture && !menuGestureHandled && event.changedTouches.length === 1) {
            const touch = event.changedTouches[0];
            const deltaX = touch.clientX - touchStartX;
            const deltaY = touch.clientY - (touchStartY ?? 0);
            if (Math.abs(deltaY) <= maxVerticalDeviation) {
                if (menuGesture === 'open' && deltaX > menuSwipeThreshold) {
                    openMobileMenu();
                } else if (menuGesture === 'close' && (touchStartX - touch.clientX) > menuSwipeThreshold) {
                    closeMobileMenuIfOpen();
                }
            }
        }

        resetTouchState();
    };

    document.addEventListener('touchstart', handleTouchStart, { passive: false, capture: true });
    document.addEventListener('touchmove', handleTouchMove, { passive: false, capture: true });
    document.addEventListener('touchend', handleTouchEnd, { passive: false, capture: true });

    setupMenuObserver();
    syncMenuState();

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            setupMenuObserver();
            syncMenuState();
        } else if (menuObserver) {
            menuObserver.disconnect();
            menuObserver = null;
        }
    });
})();
</script>

@section("content")
<div x-data="calendarApp()" x-cloak class="space-y-6 fade-in-up">
    
    <!-- Панель управления -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="calendar-header">
            <!-- Заголовок месяца -->
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500 rounded-2xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900" x-text="currentMonthYear"></h2>
                </div>
            </div>
            
            <!-- Фильтры и навигация -->
            <div class="calendar-controls">
                <!-- Фильтр по спортсмену -->
                <div x-show="isTrainer" class="flex items-center space-x-3">
                    <label class="text-sm font-medium text-gray-700">{{ __('common.athlete') }}:</label>
                    <select x-model="selectedAthleteId" @change="loadWorkouts()" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('common.all_athletes') }}</option>
                        <template x-for="athlete in athletes" :key="athlete.id">
                            <option :value="athlete.id" x-text="athlete.name"></option>
                        </template>
                    </select>
                </div>
                
                <!-- Навигация по месяцам -->
                <div class="flex items-center space-x-2 w-full sm:w-auto justify-center sm:justify-start">
                    <button @click="previousMonth()" 
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="goToToday()" 
                            class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                        {{ __('common.today') }}
                    </button>
                    <button @click="nextMonth()" 
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Календарная сетка -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Заголовки дней недели -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-100">
            <template x-for="day in weekDays" :key="day">
                <div class="px-4 py-4 text-center text-sm font-semibold text-gray-600" x-text="day"></div>
            </template>
        </div>
        
        <!-- Дни календаря -->
        <div class="grid grid-cols-7">
            <template x-for="day in calendarDays" :key="day.date || Math.random()">
                <div class="calendar-day-cell border-r border-b border-gray-100 last:border-r-0 transition-colors group relative"
                     :class="{
                         'bg-indigo-50 ring-2 ring-indigo-200': day.isToday,
                         'bg-gray-50': day.isEmpty,
                         'hover:bg-gray-50': !day.isEmpty
                     }">
                    <div class="w-full h-full p-3" x-show="!day.isEmpty">
                        <!-- Номер дня -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-gray-700 group-hover:text-indigo-600 transition-colors"
                                 :class="{ 'text-indigo-600 font-bold': day.isToday }"
                                 x-text="day.day"></div>
                        </div>
                        
                        <!-- Десктоп: карточки тренировок -->
                        <div class="space-y-1.5 desktop-workouts">
                            <template x-for="workout in day.workouts.slice(0, 2)" :key="workout.id">
                                <div @click="showWorkoutDetails(workout)"
                                     class="cursor-pointer rounded p-1.5 text-xs shadow-sm hover:shadow-md transition-all duration-200 border hover:scale-105"
                                     :class="{
                                         'bg-green-100 text-green-800 border-green-300 hover:bg-green-200': workout.status === 'completed',
                                         'bg-blue-100 text-blue-800 border-blue-300 hover:bg-blue-200': workout.status === 'planned',
                                         'bg-red-100 text-red-800 border-red-300 hover:bg-red-200': workout.status === 'cancelled'
                                     }">
                                    <div class="flex items-center justify-between text-xs opacity-75">
                                        <span x-text="workout.time ? workout.time.substring(0, 5) : ''"></span>
                                        <span class="truncate" x-text="workout.athlete_name"></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold truncate" x-text="workout.title"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Показать больше тренировок -->
                            <div x-show="day.workouts.length > 2" 
                                 class="text-xs text-center py-2 bg-indigo-100 text-indigo-700 rounded-lg font-medium hover:bg-indigo-200 cursor-pointer transition-colors"
                                 @click="showAllWorkoutsForDay(day)">
                                +<span x-text="day.workouts.length - 2"></span> {{ __('common.more_workouts') }}
                            </div>
                        </div>
                        
                        <!-- Мобилка: квадратик с количеством -->
                        <div class="mobile-workouts">
                            <div x-show="day.workouts.length > 0"
                                 class="w-6 h-6 text-white text-xs rounded flex items-center justify-center cursor-pointer transition-colors"
                                 :class="{
                                     'bg-green-500 hover:bg-green-600': day.workouts.some(w => w.status === 'completed'),
                                     'bg-red-500 hover:bg-red-600': day.workouts.some(w => w.status === 'cancelled') && !day.workouts.some(w => w.status === 'completed'),
                                     'bg-indigo-500 hover:bg-indigo-600': day.workouts.some(w => w.status === 'planned') || (!day.workouts.some(w => w.status === 'completed') && !day.workouts.some(w => w.status === 'cancelled'))
                                 }"
                                 @click="showAllWorkoutsForDay(day)">
                                <span x-text="day.workouts.length"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Модальное окно деталей тренировки -->
    <div x-show="showWorkoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="showWorkoutModal = false"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-gray-100"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900" x-text="currentWorkoutDetails.isMultiple ? '{{ __('common.all_workouts') }}' : currentWorkoutDetails.title"></h3>
                </div>
                <button @click="showWorkoutModal = false" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Содержимое -->
            <div class="p-5">
                <!-- Одна тренировка -->
                <div x-show="!currentWorkoutDetails.isMultiple">
                    <div class="space-y-4">
                        <!-- Статус -->
                        <div class="flex justify-center">
                            <span class="px-4 py-2 rounded-full text-sm font-bold"
                                  :class="{
                                      'bg-green-100 text-green-800 border border-green-200': currentWorkoutDetails.status === 'completed',
                                      'bg-blue-100 text-blue-800 border border-blue-200': currentWorkoutDetails.status === 'planned',
                                      'bg-red-100 text-red-800 border border-red-200': currentWorkoutDetails.status === 'cancelled'
                                  }"
                                  x-text="{
                                      'completed': '✓ {{ __('common.completed_status') }}',
                                      'planned': '⏰ {{ __('common.planned_status') }}',
                                      'cancelled': '✗ {{ __('common.cancelled_status') }}'
                                  }[currentWorkoutDetails.status] || currentWorkoutDetails.status">
                            </span>
                        </div>
                        
                        <!-- Информация -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('common.date') }}</div>
                                    <div class="font-semibold" x-text="formatDate(currentWorkoutDetails.date)"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('common.time') }}</div>
                                    <div class="font-semibold" x-text="currentWorkoutDetails.time ? currentWorkoutDetails.time.substring(0, 5) : '{{ __('common.not_specified') }}'"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('common.duration') }}</div>
                                    <div class="font-semibold" x-text="currentWorkoutDetails.duration + ' {{ __('common.minutes') }}'"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <div>
                                    <div class="text-xs text-gray-500">{{ __('common.athlete') }}</div>
                                    <div class="font-semibold" x-text="currentWorkoutDetails.athlete_name"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Множественные тренировки -->
                <div x-show="currentWorkoutDetails.isMultiple">
                    <div class="space-y-2">
                        <template x-for="workout in currentWorkoutDetails.workouts" :key="workout.id">
                            <div class="workout-row">
                                <span class="workout-title" x-text="workout.title"></span>
                                <span class="workout-athlete">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span x-text="workout.athlete_name || '{{ __('common.athlete_not_specified') }}'"></span>
                                </span>
                                <span class="workout-time">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="workout.time ? workout.time.substring(0, 5) : '{{ __('common.time_not_specified') }}'"></span>
                                </span>
                                <span class="workout-status"
                                          :class="{
                                              'bg-green-100 text-green-800 border border-green-200': workout.status === 'completed',
                                              'bg-blue-100 text-blue-800 border border-blue-200': workout.status === 'planned',
                                              'bg-red-100 text-red-800 border border-red-200': workout.status === 'cancelled'
                                          }"
                                          x-text="{
                                              'completed': '✓ {{ __('common.completed_status') }}',
                                              'planned': '⏰ {{ __('common.planned_status') }}',
                                              'cancelled': '✗ {{ __('common.cancelled_status') }}'
                                          }[workout.status] || workout.status"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
/* Стили для заголовка календаря */
.calendar-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

@media (min-width: 768px) {
    .calendar-header {
        flex-direction: row;
        align-items: center;
    }
}

/* Стили для элементов управления */
.calendar-controls {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

@media (min-width: 640px) {
    .calendar-controls {
        flex-direction: row;
        align-items: center;
    }
}

@media (min-width: 768px) {
    .calendar-controls {
        width: auto;
    }
}

/* Высота ячеек календаря */
.calendar-day-cell {
    min-height: 80px;
}

@media (min-width: 768px) {
    .calendar-day-cell {
        min-height: 160px;
    }
}

/* Медиазапросы для тренировок */
.desktop-workouts {
    display: none;
}

.mobile-workouts {
    display: block;
}

@media (min-width: 768px) {
    .desktop-workouts {
        display: block;
    }
    
    .mobile-workouts {
        display: none;
    }
}

/* Стили для карточек тренировок в модальном окне */
.workout-row {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    margin-bottom: 8px;
    gap: 16px;
}

.workout-title {
    font-weight: 600;
    color: #111827;
    min-width: 120px;
}

.workout-athlete {
    color: #6b7280;
    min-width: 150px;
    display: flex;
    align-items: center;
}

.workout-time {
    color: #9ca3af;
    min-width: 60px;
    display: flex;
    align-items: center;
}

.workout-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    margin-left: auto;
}

/* Убираем стиль --tw-space-y-reverse для space-y-6 */
.space-y-6 > :not([hidden]) ~ :not([hidden]) {
    --tw-space-y-reverse: 0 !important;
    margin-top: 0 !important;
}

/* Дополнительно убираем margin-top для всех дочерних элементов space-y-6 */
.space-y-6 > * + * {
    margin-top: 0 !important;
}

/* Адаптивные стили для модального окна на мобильных устройствах */
@media (max-width: 767px) {
    /* Модальное окно календаря - белый контейнер */
    div[x-show*="showWorkoutModal"] .bg-white.rounded-2xl.shadow-2xl,
    .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl {
        width: 95% !important;
        max-width: 95% !important;
        margin: 0 !important;
        max-height: 90vh !important;
        display: flex !important;
        flex-direction: column !important;
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
    }
    
    /* Заголовок модального окна */
    div[x-show*="showWorkoutModal"] .bg-white.rounded-2xl.shadow-2xl > div:first-child,
    .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl > div:first-child {
        padding: 1rem !important;
        flex-shrink: 0 !important;
    }
    
    div[x-show*="showWorkoutModal"] .bg-white.rounded-2xl.shadow-2xl > div:first-child h3,
    .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl > div:first-child h3 {
        font-size: 1rem !important;
    }
    
    /* Содержимое модального окна */
    div[x-show*="showWorkoutModal"] .bg-white.rounded-2xl.shadow-2xl > div:last-child,
    .fixed.inset-0 .bg-white.rounded-2xl.shadow-2xl > div:last-child {
        padding: 1rem !important;
        max-height: none !important;
        flex: 1 !important;
        overflow-y: auto !important;
        min-height: 0 !important;
    }
    
    /* Карточки тренировок на мобильных */
    .workout-row {
        display: grid !important;
        grid-template-columns: 1fr auto !important;
        gap: 0.5rem 0.75rem !important;
        padding: 0.75rem !important;
        align-items: center !important;
    }
    
    .workout-title {
        grid-column: 1 !important;
        grid-row: 1 !important;
        min-width: auto !important;
        width: auto !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
    }
    
    .workout-status {
        grid-column: 2 !important;
        grid-row: 1 !important;
        margin-left: 0 !important;
        margin-top: 0 !important;
        align-self: center !important;
    }
    
    .workout-athlete {
        grid-column: 1 !important;
        grid-row: 2 !important;
        min-width: auto !important;
        width: auto !important;
        font-size: 0.875rem !important;
    }
    
    .workout-time {
        grid-column: 2 !important;
        grid-row: 2 !important;
        min-width: auto !important;
        width: auto !important;
        font-size: 0.875rem !important;
        justify-self: end !important;
    }
}
</style>

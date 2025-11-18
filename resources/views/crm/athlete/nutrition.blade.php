@extends('crm.layouts.app')

@section('title', __('common.nutrition_diary'))
@section('page-title', __('common.nutrition_diary'))

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        {{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.athlete.exercises") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        {{ __('common.exercises') }}
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        {{ __('common.progress') }}
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        {{ __('common.nutrition_diary') }}
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ __('common.settings') }}
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        {{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.athlete.exercises") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        {{ __('common.exercises') }}
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        {{ __('common.progress') }}
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        {{ __('common.nutrition_diary') }}
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ __('common.settings') }}
    </a>
@endsection

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

@section('content')
<div class="min-h-screen ">
    <div>
        <!-- Описание -->
    
        <div x-data="nutritionApp()" x-init="loadNutritionPlans(); init();" x-cloak>
            <!-- Статистика питания -->
            <div x-show="currentView === 'list'" class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-red">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('common.calories_today') }}</div>
                        <div class="stat-value" x-text="getTodayCalories()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('common.proteins_today') }} ({{ __('common.g') }})</div>
                        <div class="stat-value" x-text="getTodayProteins()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-yellow">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('common.carbs_today') }} ({{ __('common.g') }})</div>
                        <div class="stat-value" x-text="getTodayCarbs()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">{{ __('common.fats_today') }} ({{ __('common.g') }})</div>
                        <div class="stat-value" x-text="getTodayFats()"></div>
                    </div>
                </div>
            </div>
            
            <!-- Планы питания - Список -->
            <div x-show="currentView === 'list'" class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">{{ __('common.nutrition_plans') }}</h4>
                
                <!-- Индикатор загрузки -->
                <div x-show="loadingNutritionPlans" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="mt-2 text-gray-500">{{ __('common.loading_nutrition_plans') }}...</p>
                </div>
                
                <!-- Пустое состояние -->
                <div x-show="!loadingNutritionPlans && nutritionPlans.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('common.no_nutrition_plans') }}</h3>
                    <p class="text-gray-500">{{ __('common.no_nutrition_plans_description') }}</p>
                </div>
                
                <!-- Список планов -->
                <div x-show="!loadingNutritionPlans && nutritionPlans.length > 0" class="space-y-4">
                    <template x-for="plan in nutritionPlans" :key="plan.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h5 class="text-lg font-medium text-gray-900">
                                        <span x-text="plan.title || `{{ __('common.nutrition_plan_for') }} ${new Date(0, plan.month - 1).toLocaleString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}', {month: 'long'})} ${plan.year} {{ __('common.year') }}.`"></span>
                                        <span class="text-sm text-gray-600" x-text="(() => {
                                            const locale = '{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}';
                                            const monthName = new Date(0, (parseInt(plan.month) || 1) - 1).toLocaleString(locale, { month: 'long' });
                                            const daysCount = plan.nutrition_days ? plan.nutrition_days.length : 0;
                                            return `(${daysCount} {{ __('common.days') }}, ${monthName} ${plan.year})`;
                                        })()"></span>
                                    </h5>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="showView(plan)" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            title="{{ __('common.details') }}">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ __('common.details') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Просмотр плана питания -->
            <div id="athlete-nutrition-view-section" x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <!-- Кнопка назад -->
                <div class="flex items-center justify-between mb-6">
                    <button @click="showList()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">
                        {{ __('common.back') }}
                    </button>
                </div>
                
                <div x-show="currentNutritionPlan" class="space-y-6">
                    <!-- Заголовок -->
                    <h3 class="text-xl font-semibold text-gray-900" x-text="currentNutritionPlan ? (currentNutritionPlan.title || `{{ __('common.nutrition_plan_for') }} ${new Date(0, currentNutritionPlan.month - 1).toLocaleString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}', {month: 'long'})} ${currentNutritionPlan.year} {{ __('common.year') }}.`) : ''"></h3>
                    
                    <!-- Описание плана -->
                    <div class="mb-6" x-show="currentNutritionPlan.description">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('common.description') }}</h4>
                        <p class="text-gray-600" x-text="currentNutritionPlan.description"></p>
                    </div>
                    
                    <!-- Статистика -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600" x-text="currentNutritionPlan.nutrition_days ? Math.round(currentNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.calories || 0), 0)) : 0"></div>
                            <div class="text-sm text-red-800">{{ __('common.total_calories') }}</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="currentNutritionPlan.nutrition_days ? Math.round(currentNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.proteins || 0), 0)) : 0"></div>
                            <div class="text-sm text-blue-800">{{ __('common.total_proteins') }} ({{ __('common.g') }})</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600" x-text="currentNutritionPlan.nutrition_days ? Math.round(currentNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.carbs || 0), 0)) : 0"></div>
                            <div class="text-sm text-yellow-800">{{ __('common.total_carbs') }} ({{ __('common.g') }})</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600" x-text="currentNutritionPlan.nutrition_days ? Math.round(currentNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.fats || 0), 0)) : 0"></div>
                            <div class="text-sm text-green-800">{{ __('common.total_fats') }} ({{ __('common.g') }})</div>
                        </div>
                    </div>
                    
                    <!-- Таблица дней -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">{{ __('common.day') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">{{ __('common.proteins') }} ({{ __('common.g') }})</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">{{ __('common.fats') }} ({{ __('common.g') }})</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300 carbs-header">
                                        <span class="carbs-full">{{ __('common.carbs') }} ({{ __('common.g') }})</span>
                                        <span class="carbs-short" style="display: none;">
                                            @if(app()->getLocale() === 'ua')
                                                Вугл. ({{ __('common.g') }})
                                            @else
                                                Углев. ({{ __('common.g') }})
                                            @endif
                                        </span>
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">{{ __('common.calories') }}</th>
                                    <th x-show="hasNotes(currentNutritionPlan)" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">{{ __('common.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300">
                                <template x-for="day in (currentNutritionPlan.nutrition_days || [])" :key="day.id">
                                    <tr>
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="new Date(day.date).getDate()"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="formatNumber(parseFloat(day.proteins || 0))"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="formatNumber(parseFloat(day.fats || 0))"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="formatNumber(parseFloat(day.carbs || 0))"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="formatNumber(parseFloat(day.calories || 0))"></td>
                                        <td x-show="hasNotes(currentNutritionPlan)" class="px-3 py-2 text-sm text-gray-900" x-text="day.notes || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Пустое состояние для дней -->
                    <div x-show="!currentNutritionPlan.nutrition_days || currentNutritionPlan.nutrition_days.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>{{ __('common.no_day_data') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function nutritionApp() {
    return {
        // Переменные для планов питания
        currentView: 'list', // list, view
        nutritionPlans: [],
        loadingNutritionPlans: true,
        currentNutritionPlan: null,
        touchStartX: null,
        touchStartY: null,
        touchStartTime: null,
        swipeHandled: false,
        swipeActivationThreshold: 120,
        swipeVisualLimit: 140,
        swipeTargetElement: null,
        swipeAnimationTimeout: null,
        swipeHandlersSetup: false,
        boundTouchStart: null,
        boundTouchMove: null,
        boundTouchEnd: null,
        
        // Загрузить планы питания
        async loadNutritionPlans() {
            this.loadingNutritionPlans = true;
            try {
                const response = await fetch('/athlete/nutrition-plans', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.nutritionPlans = await response.json();
                } else {
                    console.error('Ошибка загрузки планов питания');
                }
            } catch (error) {
                console.error('Ошибка:', error);
            } finally {
                this.loadingNutritionPlans = false;
            }
        },
        
        // Показать детальный просмотр плана
        showView(plan) {
            this.clearSwipeAnimationTimeout();
            this.resetSwipeTransform(true);
            this.swipeTargetElement = null;
            this.currentView = 'view';
            this.currentNutritionPlan = plan;
            this.$nextTick(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        },
        
        // Вернуться к списку
        showList() {
            this.clearSwipeAnimationTimeout();
            this.resetSwipeTransform(true);
            this.swipeTargetElement = null;
            this.currentView = 'list';
            this.currentNutritionPlan = null;
        },
        
        // Получить элемент для свайпа
        getSwipeTargetElement() {
            if (this.currentView === 'view') {
                return document.getElementById('athlete-nutrition-view-section');
            }
            return null;
        },
        
        // Применить трансформацию при свайпе
        applySwipeTransform(distance) {
            if (!this.swipeTargetElement) return;
            const clamped = Math.max(0, Math.min(distance, this.swipeVisualLimit));
            this.swipeTargetElement.style.transform = `translateX(${clamped}px)`;
        },
        
        // Сбросить трансформацию
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
        
        // Очистить таймер анимации
        clearSwipeAnimationTimeout() {
            if (this.swipeAnimationTimeout) {
                clearTimeout(this.swipeAnimationTimeout);
                this.swipeAnimationTimeout = null;
            }
        },
        
        // Обработка свайпа вправо
        handleSwipeRight(event, targetElement = null) {
            if (!targetElement && !this.swipeTargetElement) return;
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.swipeHandled = true;
            this.clearSwipeAnimationTimeout();
            const target = targetElement || this.swipeTargetElement;
            if (target) {
                this.swipeTargetElement = target;
                target.style.transition = 'transform 0.18s ease';
                requestAnimationFrame(() => {
                    target.style.transform = 'translateX(100%)';
                });
                this.swipeAnimationTimeout = setTimeout(() => {
                    this.showList();
                    this.resetSwipeTransform(true, target);
                    this.swipeTargetElement = null;
                    this.swipeAnimationTimeout = null;
                }, 180);
            } else {
                this.showList();
            }
        },
        
        // Настройка обработчиков свайпа
        setupSwipeHandlers() {
            if (this.swipeHandlersSetup) return;
            this.swipeHandlersSetup = true;
            this.boundTouchStart = this.handleTouchStart.bind(this);
            this.boundTouchMove = this.handleTouchMove.bind(this);
            this.boundTouchEnd = this.handleTouchEnd.bind(this);
            document.addEventListener('touchstart', this.boundTouchStart, { passive: false, capture: true });
            document.addEventListener('touchmove', this.boundTouchMove, { passive: false, capture: true });
            document.addEventListener('touchend', this.boundTouchEnd, { passive: false, capture: true });
        },
        
        // Обработка начала касания
        handleTouchStart(event) {
            if (event.touches.length !== 1) return;
            if (this.currentView !== 'view') return;
            
            // Проверка: если клик по кнопке, не обрабатываем свайп
            const isButton = event.target.closest('button') || event.target.tagName === 'BUTTON';
            if (isButton) {
                return;
            }
            
            const touch = event.touches[0];
            const startX = touch.clientX;
            const startY = touch.clientY;
            const nearEdge = startX <= this.getEdgeThreshold();
            
            if (!nearEdge) return;
            
            // Блокируем системный жест "назад" с самого края (первые 60px), но разрешаем свайп назад
            const menuCloseEdgeGuard = 60;
            if (startX <= menuCloseEdgeGuard) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            this.swipeHandled = false;
            this.touchStartX = startX;
            this.touchStartY = startY;
            this.touchStartTime = performance.now();
            this.swipeTargetElement = this.getSwipeTargetElement();
            if (this.swipeTargetElement) {
                this.swipeTargetElement.style.transition = 'transform 0s';
            }
        },
        
        // Обработка движения касания
        handleTouchMove(event) {
            if (this.touchStartX === null) return;
            if (this.currentView !== 'view') return;
            
            // Проверка: если касание идет по кнопке, сбрасываем свайп
            const isButton = event.target.closest('button') || event.target.tagName === 'BUTTON';
            if (isButton) {
                if (this.swipeTargetElement) {
                    this.resetSwipeTransform(true);
                    this.swipeTargetElement = null;
                }
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                return;
            }
            
            const touch = event.touches[0];
            const deltaX = Math.max(0, touch.clientX - this.touchStartX);
            const deltaY = touch.clientY - (this.touchStartY ?? 0);
            const maxVerticalDeviation = 80;
            
            if (Math.abs(deltaY) > maxVerticalDeviation) return;
            
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
            }
        },
        
        // Обработка окончания касания
        handleTouchEnd(event) {
            if (this.touchStartX === null) return;
            if (this.currentView !== 'view') return;
            
            // Проверка: если касание закончилось на кнопке, не обрабатываем свайп
            const isButton = event.target.closest('button') || event.target.tagName === 'BUTTON';
            if (isButton) {
                if (this.swipeTargetElement) {
                    this.resetSwipeTransform(true);
                    this.swipeTargetElement = null;
                }
                this.touchStartX = null;
                this.touchStartY = null;
                this.touchStartTime = null;
                return;
            }
            
            if (this.swipeTargetElement && event.changedTouches.length === 1) {
                const touch = event.changedTouches[0];
                const deltaX = Math.max(0, touch.clientX - this.touchStartX);
                const deltaY = touch.clientY - (this.touchStartY ?? 0);
                const duration = this.touchStartTime ? performance.now() - this.touchStartTime : 0;
                const maxVerticalDeviation = 80;
                
                if (Math.abs(deltaY) <= maxVerticalDeviation) {
                    if (deltaX > this.swipeActivationThreshold && duration < 600 && !this.swipeHandled) {
                        this.handleSwipeRight(event, this.swipeTargetElement);
                    } else {
                        this.resetSwipeTransform(false, this.swipeTargetElement);
                        this.swipeTargetElement = null;
                    }
                } else {
                    this.resetSwipeTransform(false, this.swipeTargetElement);
                    this.swipeTargetElement = null;
                }
            }
            
            this.touchStartX = null;
            this.touchStartY = null;
            this.touchStartTime = null;
        },
        
        // Получить порог для свайпа
        getEdgeThreshold() {
            const screenWidth = window.innerWidth || document.documentElement.clientWidth;
            if (screenWidth >= 1024) {
                return Math.min(Math.floor(screenWidth * 0.7), 800);
            } else if (screenWidth >= 768) {
                return Math.min(Math.floor(screenWidth * 0.6), 500);
            } else {
                return Math.max(150, Math.min(Math.floor(screenWidth * 0.5), 300));
            }
        },
        
        // Инициализация
        init() {
            this.setupSwipeHandlers();
        },
        
        // Проверить наличие заметок
        hasNotes(plan) {
            if (!plan || !plan.nutrition_days) return false;
            return plan.nutrition_days.some(day => day.notes && day.notes.trim() !== '');
        },
        
        // Форматировать число (убирать .0 если целое)
        formatNumber(num) {
            // Если число целое, возвращаем без точки и нуля
            if (num % 1 === 0) {
                return num.toString();
            }
            // Иначе возвращаем с одной цифрой после точки
            return num.toFixed(1);
        },
        
        // Получить сегодняшнюю дату в формате YYYY-MM-DD
        getTodayDate() {
            return new Date().toISOString().split('T')[0];
        },
        
        // Найти день питания на сегодня
        findTodayNutritionDay() {
            const today = this.getTodayDate();
            for (let plan of this.nutritionPlans) {
                if (plan.nutrition_days) {
                    const todayDay = plan.nutrition_days.find(day => day.date === today);
                    if (todayDay) {
                        return todayDay;
                    }
                }
            }
            return null;
        },
        
        // Получить калории на сегодня
        getTodayCalories() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.calories || 0).toFixed(0) : '—';
        },
        
        // Получить белки на сегодня
        getTodayProteins() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.proteins || 0).toFixed(0) : '—';
        },
        
        // Получить углеводы на сегодня
        getTodayCarbs() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.carbs || 0).toFixed(0) : '—';
        },
        
        // Получить жиры на сегодня
        getTodayFats() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.fats || 0).toFixed(0) : '—';
        }
    }
}
</script>

<style>
/* Скрыть элементы с x-cloak до инициализации Alpine.js */
[x-cloak] {
    display: none !important;
}

/* Мобильная версия для карточек статистики питания */
@media (max-width: 767px) {
    div.stats-container {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem !important;
        flex-wrap: nowrap !important;
    }
    
    div.stats-container .stat-card {
        min-width: auto !important;
        max-width: none !important;
        padding: 16px !important;
        flex-direction: column !important;
        text-align: center !important;
        gap: 12px !important;
        flex: 0 0 auto !important;
        width: 100% !important;
    }
    
    /* Сокращение "Углеводы" на мобильной версии */
    .carbs-header .carbs-full {
        display: none !important;
    }
    
    .carbs-header .carbs-short {
        display: inline !important;
    }
}
</style>
@endsection
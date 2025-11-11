@extends("crm.layouts.app")

@section("title", __('common.athlete_progress'))
@section("page-title", __('common.progress'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

<script>
(function () {
    if (window.__fitrainDashboardMenuSetup) return;
    window.__fitrainDashboardMenuSetup = true;

    const edgeThreshold = 80;
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
            if (startX > edgeThreshold) {
                resetTouchState();
                return;
            }
            menuGesture = 'open';
        }

        touchStartX = startX;
        touchStartY = startY;
        menuGestureHandled = false;
        preventEvent(event);
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

        if (!menuGestureHandled) {
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

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link {{ request()->routeIs('crm.dashboard.main') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2">
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
    <a href="{{ route("crm.workouts.index") }}" class="nav-link {{ request()->routeIs('crm.workouts.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.self-athlete.exercises") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        {{ __('common.exercises') }}
    </a>
    <a href="{{ route("crm.self-athlete.progress") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        {{ __('common.progress') }}
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link {{ request()->routeIs('crm.nutrition.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        {{ __('common.nutrition_diary') }}
    </a>
    <a href="{{ route('crm.self-athlete.settings') }}" class="nav-link {{ request()->routeIs('crm.self-athlete.settings*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ __('common.settings') }}
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link {{ request()->routeIs('crm.dashboard.main') ? 'active' : '' }}">
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
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link {{ request()->routeIs('crm.workouts.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.self-athlete.exercises") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        {{ __('common.exercises') }}
    </a>
    <a href="{{ route("crm.self-athlete.progress") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        {{ __('common.progress') }}
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link {{ request()->routeIs('crm.nutrition.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        {{ __('common.nutrition_diary') }}
    </a>
    <a href="{{ route('crm.self-athlete.settings') }}" class="mobile-nav-link {{ request()->routeIs('crm.self-athlete.settings*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ __('common.settings') }}
    </a>
@endsection

@section("content")
<div class="space-y-6 fade-in-up">
    
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('common.my_progress') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('common.track_your_achievements_and_workout_results') }}</p>
        </div>
    </div>

    <!-- Статистика прогресса -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('common.total_workouts') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentWorkouts ? $recentWorkouts->count() : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('common.progress_records') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $progressData ? $progressData->count() : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('common.measurements') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $measurements ? $measurements->count() : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">{{ __('common.last_workout') }}</p>
                    <p class="text-lg font-bold text-gray-900">
                        @if($recentWorkouts && $recentWorkouts->count() > 0)
                            {{ $recentWorkouts->first()->created_at->format('d.m.Y') }}
                        @else
                            {{ __('common.no_data') }}
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Графики прогресса -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('common.progress_charts') }}</h3>
        </div>
        <div class="p-6">
            <!-- Фильтры графиков -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">{{ __('common.period') }}:</label>
                        <select id="timeFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">{{ __('common.all_measurements') }}</option>
                            <option value="month">{{ __('common.last_month') }}</option>
                            <option value="3months">{{ __('common.last_3_months') }}</option>
                            <option value="6months">{{ __('common.last_6_months') }}</option>
                            <option value="year">{{ __('common.last_year') }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">{{ __('common.filter') }}:</label>
                        <select id="chartFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">{{ __('common.all_parameters') }}</option>
                            <option value="weight">{{ __('common.weight_and_bmi') }}</option>
                            <option value="body">{{ __('common.fat_percentage_and_muscle_mass') }}</option>
                            <option value="volumes">{{ __('common.body_volumes') }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- График веса -->
                <div id="weightChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.weight_dynamics') }} ({{ __('common.kg') }})</h4>
                    <div class="relative h-64 cursor-pointer" onclick="openChartModal('weight', '{{ __('common.weight_dynamics') }} ({{ __('common.kg') }})')">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>

                <!-- График ИМТ -->
                <div id="bmiChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.bmi_dynamics') }}</h4>
                    <div class="relative h-64 cursor-pointer" onclick="openChartModal('bmi', '{{ __('common.bmi_dynamics') }}')">
                        <canvas id="bmiChart"></canvas>
                    </div>
                </div>

                <!-- График процента жира -->
                <div id="bodyFatChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.fat_percentage') }} (%)</h4>
                    <div class="relative h-64 cursor-pointer" onclick="openChartModal('bodyFat', '{{ __('common.fat_percentage') }} (%)')">
                        <canvas id="bodyFatChart"></canvas>
                    </div>
                </div>

                <!-- График мышечной массы -->
                <div id="muscleMassChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.muscle_mass') }} ({{ __('common.kg') }})</h4>
                    <div class="relative h-64 cursor-pointer" onclick="openChartModal('muscleMass', '{{ __('common.muscle_mass') }} ({{ __('common.kg') }})')">
                        <canvas id="muscleMassChart"></canvas>
                    </div>
                </div>

                <!-- График объемов тела -->
                <div id="bodyVolumesChartContainer" class="bg-white rounded-lg p-4 border border-gray-200 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">{{ __('common.body_volumes') }} ({{ __('common.cm') }})</h4>
                        <select id="volumesFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">{{ __('common.all_volumes') }}</option>
                            <option value="chest">{{ __('common.chest') }}</option>
                            <option value="waist">{{ __('common.waist') }}</option>
                            <option value="hips">{{ __('common.hips') }}</option>
                            <option value="bicep">{{ __('common.bicep') }}</option>
                            <option value="thigh">{{ __('common.thigh') }}</option>
                            <option value="neck">{{ __('common.neck') }}</option>
                        </select>
                    </div>
                    <div class="relative h-96 cursor-pointer" onclick="openChartModal('volumes', '{{ __('common.body_volumes') }} ({{ __('common.cm') }})')">
                        <canvas id="bodyVolumesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для увеличения графиков -->
    <div id="chartModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <!-- Заголовок модального окна -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 id="modalTitle" class="text-2xl font-bold text-gray-900">{{ __('common.chart') }}</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    </button>
    </div>

                <!-- Содержимое модального окна -->
        <div class="p-6">
                    <div class="relative h-[60vh]">
                        <canvas id="modalChart"></canvas>
                        </div>
                </div>
                </div>
        </div>
    </div>

</div>

<script>
// Инициализируем графики после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    // Небольшая задержка для полной загрузки DOM
    setTimeout(function() {
        initCharts();
        setupChartFilters();
    }, 100);
    
    // Настройка модального окна
    setupModal();
});


// Настройка модального окна
function setupModal() {
    const modal = document.getElementById('chartModal');
    const closeBtn = document.getElementById('closeModal');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeChartModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeChartModal();
            }
        });
    }
    
    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeChartModal();
        }
    });
}

// Открытие модального окна с графиком
function openChartModal(chartType, title) {
    const modal = document.getElementById('chartModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalCanvas = document.getElementById('modalChart');
    
    if (!modal || !modalTitle || !modalCanvas) {
        return;
    }
    
    modalTitle.textContent = title;
    modal.classList.remove('hidden');
    createModalChart(chartType);
}

// Закрытие модального окна
function closeChartModal() {
    const modal = document.getElementById('chartModal');
    const modalCanvas = document.getElementById('modalChart');
    
    if (modal) {
        modal.classList.add('hidden');
    }
    
    // Уничтожаем график в модальном окне
    if (window.modalChart && typeof window.modalChart.destroy === 'function') {
        window.modalChart.destroy();
        window.modalChart = null;
    }
}

// Создание графика в модальном окне
function createModalChart(chartType) {
    const ctx = document.getElementById('modalChart');
    if (!ctx) return;
    
    if (window.modalChart && typeof window.modalChart.destroy === 'function') {
        window.modalChart.destroy();
    }
    
    let sourceChart = null;
    switch (chartType) {
        case 'weight':
            sourceChart = window.weightChart;
            break;
        case 'bmi':
            sourceChart = window.bmiChart;
            break;
        case 'bodyFat':
            sourceChart = window.bodyFatChart;
            break;
        case 'muscleMass':
            sourceChart = window.muscleMassChart;
            break;
        case 'volumes':
            sourceChart = window.bodyVolumesChart;
            break;
    }
    
    if (!sourceChart) return;
    
    const chartConfig = {
        type: sourceChart.config.type,
        data: sourceChart.config.data,
        options: {
            ...sourceChart.config.options,
            responsive: true,
            maintainAspectRatio: false
        }
    };
    
    window.modalChart = new Chart(ctx, chartConfig);
}

// Функция для инициализации графиков
function initCharts() {
    
    // Получаем данные измерений
    const measurements = @json($measurements ? $measurements->all() : []);
    
    
    if (measurements.length === 0) {
        return;
    }
    
    // Проверяем, что Chart.js загружен
    if (typeof Chart === 'undefined') {
        return;
    }

    // Очищаем предыдущие графики
    destroyCharts();

    // Сортируем измерения по дате (данные уже отсортированы в контроллере)
    const sortedMeasurements = [...measurements].sort((a, b) => new Date(a.measurement_date) - new Date(b.measurement_date));
    
    // Фильтруем измерения с валидными данными
    const validMeasurements = sortedMeasurements.filter(m => 
        m.measurement_date && 
        (m.weight !== null && m.weight !== undefined)
    );
    
    if (validMeasurements.length === 0) {
        return;
    }
    
    // Подготавливаем метки для графиков
    const labels = validMeasurements.map(m => {
        const date = new Date(m.measurement_date);
        return date.toLocaleDateString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}', { month: 'short', day: 'numeric' });
    });


    // Создаем графики
    createWeightChart(labels, validMeasurements);
    createBMIChart(labels, validMeasurements);
    createBodyFatChart(labels, validMeasurements);
    createMuscleMassChart(labels, validMeasurements);
    createBodyVolumesChart(labels, validMeasurements);
    
}

// Очистка графиков
function destroyCharts() {
    if (window.weightChart && typeof window.weightChart.destroy === 'function') {
        window.weightChart.destroy();
        window.weightChart = null;
    }
    if (window.bmiChart && typeof window.bmiChart.destroy === 'function') {
        window.bmiChart.destroy();
        window.bmiChart = null;
    }
    if (window.bodyFatChart && typeof window.bodyFatChart.destroy === 'function') {
        window.bodyFatChart.destroy();
        window.bodyFatChart = null;
    }
    if (window.muscleMassChart && typeof window.muscleMassChart.destroy === 'function') {
        window.muscleMassChart.destroy();
        window.muscleMassChart = null;
    }
    if (window.bodyVolumesChart && typeof window.bodyVolumesChart.destroy === 'function') {
        window.bodyVolumesChart.destroy();
        window.bodyVolumesChart = null;
    }
}

// Создание графика веса
function createWeightChart(labels, measurements) {
    const ctx = document.getElementById('weightChart');
    if (!ctx) {
        return;
    }

    const weightData = measurements.map(m => m.weight).filter(val => val !== null && val !== undefined);

    try {
        window.weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __('common.weight') }} ({{ __('common.kg') }})',
                    data: weightData,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        afterDataLimits: function(scale) {
                            const max = scale.max;
                            const min = scale.min;
                            const range = max - min;
                            scale.max = max + (range * 0.1); // Добавляем 10% отступ сверху
                            scale.min = min - (range * 0.05); // Добавляем 5% отступ снизу
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (error) {
    }
}

// Создание графика ИМТ
function createBMIChart(labels, measurements) {
    const ctx = document.getElementById('bmiChart');
    if (!ctx) return;

    const height = {{ auth()->user()->height ?? 0 }};
    const bmiData = measurements.map(m => {
        if (m.weight && height && height > 0) {
            return Math.round((m.weight / Math.pow(height/100, 2)) * 10) / 10;
        }
        return null;
    }).filter(val => val !== null && val !== undefined);

    try {
        window.bmiChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __('common.bmi') }}',
                    data: bmiData,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        afterDataLimits: function(scale) {
                            const max = scale.max;
                            const min = scale.min;
                            const range = max - min;
                            scale.max = max + (range * 0.1); // Добавляем 10% отступ сверху
                            scale.min = min - (range * 0.05); // Добавляем 5% отступ снизу
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (error) {
        console.error('Ошибка создания графика ИМТ:', error);
    }
}

// Создание графика процента жира
function createBodyFatChart(labels, measurements) {
    const ctx = document.getElementById('bodyFatChart');
    if (!ctx) return;

    const bodyFatData = measurements.map(m => m.body_fat_percentage).filter(val => val !== null && val !== undefined);

    try {
        window.bodyFatChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __('common.fat_percentage') }} (%)',
                    data: bodyFatData,
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        afterDataLimits: function(scale) {
                            const max = scale.max;
                            const min = scale.min;
                            const range = max - min;
                            scale.max = max + (range * 0.1); // Добавляем 10% отступ сверху
                            scale.min = min - (range * 0.05); // Добавляем 5% отступ снизу
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (error) {
        console.error('Ошибка создания графика процента жира:', error);
    }
}

// Создание графика мышечной массы
function createMuscleMassChart(labels, measurements) {
    const ctx = document.getElementById('muscleMassChart');
    if (!ctx) return;

    const muscleMassData = measurements.map(m => m.muscle_mass).filter(val => val !== null && val !== undefined);

    try {
        window.muscleMassChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '{{ __('common.muscle_mass') }} ({{ __('common.kg') }})',
                    data: muscleMassData,
                    borderColor: '#8B5CF6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        afterDataLimits: function(scale) {
                            const max = scale.max;
                            const min = scale.min;
                            const range = max - min;
                            scale.max = max + (range * 0.1); // Добавляем 10% отступ сверху
                            scale.min = min - (range * 0.05); // Добавляем 5% отступ снизу
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (error) {
        console.error('Ошибка создания графика мышечной массы:', error);
    }
}

// Создание графика объемов тела
function createBodyVolumesChart(labels, measurements) {
    const ctx = document.getElementById('bodyVolumesChart');
    if (!ctx) {
        return;
    }

    // Подготавливаем данные для каждого объема
    const chestData = measurements.map(m => m.chest).filter(val => val !== null && val !== undefined);
    const waistData = measurements.map(m => m.waist).filter(val => val !== null && val !== undefined);
    const hipsData = measurements.map(m => m.hips).filter(val => val !== null && val !== undefined);
    const bicepData = measurements.map(m => m.bicep).filter(val => val !== null && val !== undefined);
    const thighData = measurements.map(m => m.thigh).filter(val => val !== null && val !== undefined);
    const neckData = measurements.map(m => m.neck).filter(val => val !== null && val !== undefined);


    try {
        window.bodyVolumesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '{{ __('common.chest') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.chest),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: chestData.length === 0
                    },
                    {
                        label: '{{ __('common.waist') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.waist),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: waistData.length === 0
                    },
                    {
                        label: '{{ __('common.hips') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.hips),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: hipsData.length === 0
                    },
                    {
                        label: '{{ __('common.bicep') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.bicep),
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: bicepData.length === 0
                    },
                    {
                        label: '{{ __('common.thigh') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.thigh),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: thighData.length === 0
                    },
                    {
                        label: '{{ __('common.neck') }} ({{ __('common.cm') }})',
                        data: measurements.map(m => m.neck),
                        borderColor: '#06B6D4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: neckData.length === 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + (context.parsed.y || '—') + ' {{ __('common.cm') }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        title: {
                            display: true,
                            text: '{{ __('common.volume') }} ({{ __('common.cm') }})'
                        },
                        afterDataLimits: function(scale) {
                            const max = scale.max;
                            const min = scale.min;
                            const range = max - min;
                            scale.max = max + (range * 0.1); // Добавляем 10% отступ сверху
                            scale.min = min - (range * 0.05); // Добавляем 5% отступ снизу
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    } catch (error) {
    }
}

// Настройка фильтров графиков
function setupChartFilters() {
    const timeFilter = document.getElementById('timeFilter');
    const chartFilter = document.getElementById('chartFilter');
    const volumesFilter = document.getElementById('volumesFilter');
    
    
    if (timeFilter) {
        timeFilter.addEventListener('change', function() {
            filterChartsByTime(this.value);
        });
    } else {
    }
    
    if (chartFilter) {
        chartFilter.addEventListener('change', function() {
            filterChartsByType(this.value);
        });
    } else {
    }
    
    if (volumesFilter) {
        volumesFilter.addEventListener('change', function() {
            filterVolumesChart(this.value);
        });
    } else {
    }
}

// Фильтрация графиков по времени
function filterChartsByTime(timeFilter) {
    
    // Получаем все измерения
    const allMeasurements = @json($measurements ? $measurements->all() : []);
    let filteredMeasurements = allMeasurements;
    
    if (timeFilter !== 'all') {
        const now = new Date();
        const filterDate = new Date();
        
        switch (timeFilter) {
            case 'month':
                filterDate.setMonth(now.getMonth() - 1);
                break;
            case '3months':
                filterDate.setMonth(now.getMonth() - 3);
                break;
            case '6months':
                filterDate.setMonth(now.getMonth() - 6);
                break;
            case 'year':
                filterDate.setFullYear(now.getFullYear() - 1);
                break;
        }
        
        filteredMeasurements = allMeasurements.filter(m => {
            const measurementDate = new Date(m.measurement_date);
            return measurementDate >= filterDate;
        });
    }
    
    // Пересоздаем графики с отфильтрованными данными
    recreateCharts(filteredMeasurements);
}

// Фильтрация графиков по типу
function filterChartsByType(chartFilter) {
    
    // Скрываем/показываем контейнеры графиков
    const containers = {
        weight: document.getElementById('weightChartContainer'),
        bmi: document.getElementById('bmiChartContainer'),
        bodyFat: document.getElementById('bodyFatChartContainer'),
        muscleMass: document.getElementById('muscleMassChartContainer'),
        volumes: document.getElementById('bodyVolumesChartContainer')
    };
    
    // Проверяем, что все контейнеры существуют
    const validContainers = Object.values(containers).filter(container => container !== null);
    
    if (validContainers.length === 0) {
        return;
    }
    
    // Сначала скрываем все
    validContainers.forEach(container => {
        container.style.display = 'none';
    });
    
    // Показываем нужные в зависимости от фильтра
    switch (chartFilter) {
        case 'all':
            validContainers.forEach(container => {
                container.style.display = 'block';
            });
            break;
        case 'weight':
            if (containers.weight) containers.weight.style.display = 'block';
            if (containers.bmi) containers.bmi.style.display = 'block';
            break;
        case 'body':
            if (containers.bodyFat) containers.bodyFat.style.display = 'block';
            if (containers.muscleMass) containers.muscleMass.style.display = 'block';
            break;
        case 'volumes':
            if (containers.volumes) containers.volumes.style.display = 'block';
            break;
    }
}

// Пересоздание графиков с новыми данными
function recreateCharts(measurements) {
    
    if (measurements.length === 0) {
        return;
    }
    
    // Очищаем предыдущие графики
    destroyCharts();
    
    // Сортируем измерения по дате
    const sortedMeasurements = [...measurements].sort((a, b) => new Date(a.measurement_date) - new Date(b.measurement_date));
    
    // Фильтруем измерения с валидными данными
    const validMeasurements = sortedMeasurements.filter(m => 
        m.measurement_date && 
        (m.weight !== null && m.weight !== undefined)
    );
    
    if (validMeasurements.length === 0) {
        return;
    }
    
    // Подготавливаем метки для графиков
    const labels = validMeasurements.map(m => {
        const date = new Date(m.measurement_date);
        return date.toLocaleDateString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}', { month: 'short', day: 'numeric' });
    });
    
    // Создаем графики
    createWeightChart(labels, validMeasurements);
    createBMIChart(labels, validMeasurements);
    createBodyFatChart(labels, validMeasurements);
    createMuscleMassChart(labels, validMeasurements);
    createBodyVolumesChart(labels, validMeasurements);
    
}

// Фильтрация графика объемов тела
function filterVolumesChart(volumesFilter) {
    
    if (!window.bodyVolumesChart) {
        return;
    }
    
    const chart = window.bodyVolumesChart;
    const datasets = chart.data.datasets;
    
    // Сначала показываем все линии
    datasets.forEach(dataset => {
        dataset.hidden = false;
    });
    
    // Скрываем ненужные линии в зависимости от фильтра
    switch (volumesFilter) {
        case 'all':
            // Показываем все линии
            break;
        case 'chest':
            hideAllExcept(datasets, ['{{ __('common.chest') }} ({{ __('common.cm') }})']);
            break;
        case 'waist':
            hideAllExcept(datasets, ['{{ __('common.waist') }} ({{ __('common.cm') }})']);
            break;
        case 'hips':
            hideAllExcept(datasets, ['{{ __('common.hips') }} ({{ __('common.cm') }})']);
            break;
        case 'bicep':
            hideAllExcept(datasets, ['{{ __('common.bicep') }} ({{ __('common.cm') }})']);
            break;
        case 'thigh':
            hideAllExcept(datasets, ['{{ __('common.thigh') }} ({{ __('common.cm') }})']);
            break;
        case 'neck':
            hideAllExcept(datasets, ['{{ __('common.neck') }} ({{ __('common.cm') }})']);
            break;
    }
    
    // Обновляем график
    chart.update();
}

// Вспомогательная функция для скрытия всех линий кроме указанных
function hideAllExcept(datasets, allowedLabels) {
    datasets.forEach(dataset => {
        dataset.hidden = !allowedLabels.includes(dataset.label);
    });
}

</script>
@endsection

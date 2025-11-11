@extends("crm.layouts.app")

@section("title", __("common.measurements"))
@section("page-title", __('common.measurements'))

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
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
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
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
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

<style>
.pagination-container {
    text-align: center !important;
    width: 100% !important;
    margin-top: 2rem !important;
}

.pagination-nav {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
}

.measurements-grid {
    display: grid !important;
    grid-template-columns: repeat(1, 1fr) !important;
    gap: 1.5rem !important;
}

@media (min-width: 768px) {
    .measurements-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (min-width: 1280px) {
    .measurements-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 640px) {
    .p-6 {
        padding: 0.5rem !important;
    }
}

.stats-container {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
}

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

.pagination-wrapper {
    width: 100% !important;
    display: block !important;
    margin-top: 2rem !important;
}

.pagination-wrapper .pagination-container {
    margin: 0 auto !important;
    display: table !important;
}

</style>

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
        the startX = touch.clientX;
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

@section("content")
<div class="p-6">
    <!-- Статистические карточки -->
    <div class="stats-container mb-8">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('common.total_measurements') }}</div>
                <div class="stat-value">{{ $totalMeasurements }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('common.last_measurement') }}</div>
                <div class="stat-value">{{ $lastMeasurement ? $lastMeasurement->measurement_date->format('d.m.Y') : '—' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">{{ __('common.current_weight') }}</div>
                <div class="stat-value">{{ $lastMeasurement ? $lastMeasurement->weight . ' кг' : '—' }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-orange">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label flex items-center gap-1">
                    <span>{{ __('common.bmi') }}</span>
                    <!-- Иконка знака вопроса с подсказкой -->
                    <div class="relative group">
                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3 3 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <!-- Всплывающая подсказка -->
                        <div class="absolute top-full right-0 mt-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                            <div class="font-semibold mb-2">{{ __('common.bmi_tooltip') }}</div>
                            <div class="space-y-1">
                                <div class="flex justify-between"><span class="text-blue-300">{{ __('common.less_than_18_5') }}:</span> <span>{{ __('common.underweight') }}</span></div>
                                <div class="flex justify-between"><span class="text-green-300">{{ __('common.18_5_to_24_9') }}:</span> <span>{{ __('common.normal_weight') }}</span></div>
                                <div class="flex justify-between"><span class="text-yellow-300">{{ __('common.25_to_29_9') }}:</span> <span>{{ __('common.overweight') }}</span></div>
                                <div class="flex justify-between"><span class="text-red-300">{{ __('common.30_and_more') }}:</span> <span>{{ __('common.obesity') }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $bmi = null;
                    $bmiCategory = ['text' => '—', 'color' => 'text-gray-500'];
                    if ($lastMeasurement && $lastMeasurement->weight && auth()->user()->height) {
                        $bmi = $lastMeasurement->weight / ((auth()->user()->height/100) ** 2);
                        if ($bmi < 18.5) {
                            $bmiCategory = ['text' => __('common.underweight'), 'color' => 'text-blue-600'];
                        } elseif ($bmi < 25) {
                            $bmiCategory = ['text' => __('common.normal_weight'), 'color' => 'text-green-600'];
                        } elseif ($bmi < 30) {
                            $bmiCategory = ['text' => __('common.overweight'), 'color' => 'text-yellow-600'];
                        } else {
                            $bmiCategory = ['text' => __('common.obesity'), 'color' => 'text-red-600'];
                        }
                    }
                @endphp
                <div class="stat-value {{ $bmiCategory['color'] }}">{{ $bmi ? number_format($bmi, 1) : '—' }}</div>
            </div>
        </div>
    </div>


    <!-- Основной контент -->
    <div class="space-y-6" x-data="measurementPagination()">
        <!-- Заголовок с кнопкой добавления -->
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('common.measurement_history') }}</h3>
            <button onclick="showAddMeasurementModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('common.add_measurement') }}
            </button>
        </div>
        
        <template x-if="measurements.length > 0">
            <div>
                <!-- Карточки измерений -->
                <div class="measurements-grid">
                        <template x-for="measurement in paginatedMeasurements" :key="measurement.id">
                        <div class="card hover:shadow-lg transition-shadow duration-200">
                            <div class="card-header">
                                <div class="flex items-center justify-between">
                                    <h4 class="card-title text-lg" x-text="new Date(measurement.measurement_date).toLocaleDateString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}')"></h4>
                                    <div class="flex space-x-2">
                                        <button @click="editMeasurement(measurement.id)" class="text-indigo-600 hover:text-indigo-800" title="{{ __('common.edit_measurement') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="deleteMeasurement(measurement.id)" class="text-red-600 hover:text-red-800" title="{{ __('common.delete_measurement') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Основные параметры -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <div class="text-xl font-bold text-blue-600" x-text="measurement.weight || '—'"></div>
                                        <div class="text-xs text-blue-800">{{ __('common.weight') }} ({{ __('common.kg') }})</div>
                                    </div>
                                    <div class="text-center p-3 rounded-lg" x-show="measurement.weight && measurement.height" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).bg">
                                        <div class="text-xl font-bold" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).color" x-text="formatNumber(measurement.weight / Math.pow(measurement.height/100, 2), '')"></div>
                                        <div class="text-xs" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).color">{{ __('common.bmi') }}</div>
                                    </div>
                                </div>
                                
                                <!-- Объемы тела -->
                                <template x-if="measurement.chest || measurement.waist || measurement.hips || measurement.bicep || measurement.thigh || measurement.neck">
                                    <div class="mt-4 pt-4 pb-4 border-t border-b border-gray-200">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">{{ __('common.body_volumes') }}</h5>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <template x-if="measurement.chest">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">{{ __('common.chest') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.chest, ' см')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.waist">
                                                <div class="flex justify-between">
                                                    <span class='text-gray-500'>{{ __('common.waist') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.waist, ' см')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.hips">
                                                <div class="flex justify-between">
                                                    <span class='text-gray-500'>{{ __('common.hips') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.hips, ' см')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.bicep">
                                                <div class="flex justify-between">
                                                    <span class='text-gray-500'>{{ __('common.bicep') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.bicep, ' см')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.thigh">
                                                <div class="flex justify-between">
                                                    <span class='text-gray-500'>{{ __('common.thigh') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.thigh, ' см')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.neck">
                                                <div class="flex justify-between">
                                                    <span class='text-gray-500'>{{ __('common.neck') }}:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.neck, ' см')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Дополнительные параметры -->
                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">{{ __('common.additional_parameters') }}</h5>
                                    <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('common.fat_percentage') }}:</span>
                                        <span class="font-medium" x-text="measurement.body_fat_percentage ? formatNumber(measurement.body_fat_percentage, '%') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class='text-gray-500'>{{ __('common.muscles') }}:</span>
                                        <span class="font-medium" x-text="measurement.muscle_mass ? formatNumber(measurement.muscle_mass, ' кг') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class='text-gray-500'>{{ __('common.water') }}:</span>
                                        <span class="font-medium" x-text="measurement.water_percentage ? formatNumber(measurement.water_percentage, '%') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class='text-gray-500'>{{ __('common.pulse') }}:</span>
                                        <span class="font-medium" x-text="measurement.resting_heart_rate ? Math.round(parseFloat(measurement.resting_heart_rate)) + ' {{ __('common.bpm') }}' : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class='text-gray-500'>{{ __('common.pressure') }}:</span>
                                        <span class="font-medium" x-text="measurement.blood_pressure_systolic && measurement.blood_pressure_diastolic ? Math.round(parseFloat(measurement.blood_pressure_systolic)) + '/' + Math.round(parseFloat(measurement.blood_pressure_diastolic)) : '—'"></span>
                                    </div>
                                    </div>
                                </div>
                                
                                <!-- Комментарии -->
                                <template x-if="measurement.notes">
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class='text-sm font-medium text-gray-700 mb-1'>{{ __('common.comments') }}</h5>
                                        <p class="text-sm text-gray-600" x-text="measurement.notes"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
            
        </div>
        
        <!-- Пагинация -->
        <div class="pagination-wrapper" x-show="totalPages > 1">
            <div class="pagination-container">
                <!-- Навигация -->
                <div class="pagination-nav">
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
                            <template x-for="page in visiblePages" :key="page">
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
        </template>
        <template x-if="measurements.length === 0">
            <div>
                <!-- Пустое состояние -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('common.no_data') }}</h3>
                    <p class="text-gray-500 mb-4">{{ __('common.no_data_to_display') }}</p>
                    <button onclick="showAddMeasurementModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        {{ __('common.add_measurement') }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Модальное окно добавления/редактирования измерения -->
<div id="measurementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">{{ __('common.create_measurement_title') }}</h3>
                <button onclick="closeMeasurementModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="measurementForm" method="POST" onsubmit="submitMeasurementForm(); return false;">
                @csrf
                <div id="formMethod" style="display: none;"></div>
                
                <!-- Основные параметры -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.measurement_date') }} *</label>
                        <input type="date" name="measurement_date" id="measurement_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.weight') }} ({{ __('common.kg') }}) *</label>
                        <input type="number" name="weight" id="weight" step="0.1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.height') }} ({{ __('common.cm') }})</label>
                        <input type="number" name="height" id="height" step="0.1" readonly
                               value="{{ auth()->user()->height ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        <p class='text-xs text-gray-500 mt-1'>{{ __('common.height_from_profile') }}</p>
                    </div>
                </div>
                
                <!-- Состав тела -->
                <div class="mb-6">
                    <h4 class='text-md font-semibold text-gray-900 mb-4'>{{ __('common.body_composition') }}</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.water_percentage_pct') }}</label>
                            <input type="number" name="body_fat_percentage" id="body_fat_percentage" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.muscle_mass_kg') }}</label>
                            <input type="number" name="muscle_mass" id="muscle_mass" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.water_percentage_pct') }}</label>
                            <input type="number" name="water_percentage" id="water_percentage" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Медицинские показатели -->
                <div class="mb-6">
                    <h4 class='text-md font-semibold text-gray-900 mb-4'>{{ __('common.medical_indicators') }}</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.resting_heart_rate_bpm') }}</label>
                            <input type="number" name="resting_heart_rate" id="resting_heart_rate" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.systolic_pressure') }}</label>
                            <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.diastolic_pressure') }}</label>
                            <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Объемы тела -->
                <div class="mb-6">
                    <h4 class='text-md font-semibold text-gray-900 mb-4'>{{ __('common.body_volumes') }} ({{ __('common.cm') }})</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Грудь</label>
                            <input type="number" name="chest" id="chest" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.waist') }}</label>
                            <input type="number" name="waist" id="waist" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.hips') }}</label>
                            <input type="number" name="hips" id="hips" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.bicep') }}</label>
                            <input type="number" name="bicep" id="bicep" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.thigh') }}</label>
                            <input type="number" name="thigh" id="thigh" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.neck') }}</label>
                            <input type="number" name="neck" id="neck" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Комментарии -->
                <div class="mb-6">
                    <label class='block text-sm font-medium text-gray-700 mb-2'>{{ __('common.comments') }}</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder='{{ __('common.add_notes_placeholder') }}'></textarea>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMeasurementModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        {{ __('common.cancel') }}
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        {{ __('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="deleteConfirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class='text-lg font-medium text-gray-900 mb-2'>{{ __('common.confirm_delete') }}</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    {{ __('common.confirm_delete_measurement') }}
                </p>
            </div>
            <div class="flex justify-center space-x-3 mt-4">
                <button onclick="closeDeleteConfirmationModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    {{ __('common.cancel') }}
                </button>
                <button onclick="confirmDeleteMeasurement()" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    {{ __('common.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

    <script>
    let currentMeasurementId = null;
    
    // Ждем загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация после загрузки DOM
    });
    
    // Функция для пагинации измерений
    function measurementPagination() {
        return {
            measurements: @json($measurements->all()),
            currentPage: 1,
            itemsPerPage: 6,
            totalPages: Math.ceil(@json($measurements->all()).length / 6),
            
            get paginatedMeasurements() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.measurements.slice(start, end);
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
            
            getBMICategory(bmi) {
                if (bmi < 18.5) {
                    return { text: 'Недостаточный вес', color: 'text-blue-600', bg: 'bg-blue-50' };
                } else if (bmi < 25) {
                    return { text: 'Нормальный вес', color: 'text-green-600', bg: 'bg-green-50' };
                } else if (bmi < 30) {
                    return { text: 'Избыточный вес', color: 'text-yellow-600', bg: 'bg-yellow-50' };
                } else {
                    return { text: 'Ожирение', color: 'text-red-600', bg: 'bg-red-50' };
                }
            },
            
            formatNumber(num, unit = '') {
                if (num === null || num === undefined || num === '') return '—';
                
                // Преобразуем в число
                const number = parseFloat(num);
                if (isNaN(number)) return '—';
                
                // Проверяем, является ли число целым
                const formatted = number % 1 === 0 ? Math.round(number).toString() : number.toFixed(1).replace(/\.?0+$/, '');
                return formatted + unit;
            },
            
            editMeasurement(measurementId) {
                // Вызываем глобальную функцию
                setTimeout(() => {
                    if (window.editMeasurement) {
                        window.editMeasurement(measurementId);
                    }
                }, 100);
            },
            
            deleteMeasurement(measurementId) {
                // Вызываем глобальную функцию
                setTimeout(() => {
                    if (window.deleteMeasurement) {
                        window.deleteMeasurement(measurementId);
                    }
                }, 100);
            }
        }
    }

// Объявляем функции
function showAddMeasurementModal() {
    // Проверяем, что DOM загружен
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showAddMeasurementModal);
        return;
    }
    
    currentMeasurementId = null;
    
    const modalTitle = document.getElementById('modalTitle');
    const measurementForm = document.getElementById('measurementForm');
    const formMethod = document.getElementById('formMethod');
    const measurementDate = document.getElementById('measurement_date');
    const modal = document.getElementById('measurementModal');
    
    if (!modal) {
        return;
    }
    
    if (modalTitle) modalTitle.textContent = '{{ __('common.create_measurement_title') }}';
    if (measurementForm) measurementForm.action = '{{ route("crm.athlete.measurements.store") }}';
    if (formMethod) formMethod.innerHTML = '';
    if (measurementForm) measurementForm.reset();
    if (measurementDate) measurementDate.value = new Date().toISOString().split('T')[0];
    
    modal.classList.remove('hidden');
}

function editMeasurement(measurementId) {
    // Проверяем, что DOM загружен
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => editMeasurement(measurementId));
        return;
    }
    
    currentMeasurementId = measurementId;
    
    const modalTitle = document.getElementById('modalTitle');
    const measurementForm = document.getElementById('measurementForm');
    const formMethod = document.getElementById('formMethod');
    const modal = document.getElementById('measurementModal');
    
    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    
    if (modalTitle) modalTitle.textContent = '{{ __('common.edit_measurement_title') }}';
    if (measurementForm) measurementForm.action = `/athlete/measurements/${measurementId}`;
    if (formMethod) formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Загружаем данные измерения
    fetch(`/athlete/measurements/${measurementId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const measurement = data.measurement;
            
            // Отладочная информация
            console.log('Полученные данные измерения:', measurement);
            console.log('Дата измерения:', measurement.measurement_date);
            
            // Получаем элементы формы
            const measurementDate = document.getElementById('measurement_date');
            const weight = document.getElementById('weight');
            const height = document.getElementById('height');
            const bodyFatPercentage = document.getElementById('body_fat_percentage');
            const muscleMass = document.getElementById('muscle_mass');
            const waterPercentage = document.getElementById('water_percentage');
            const restingHeartRate = document.getElementById('resting_heart_rate');
            const systolicPressure = document.getElementById('blood_pressure_systolic');
            const diastolicPressure = document.getElementById('blood_pressure_diastolic');
            const chest = document.getElementById('chest');
            const waist = document.getElementById('waist');
            const hips = document.getElementById('hips');
            const bicep = document.getElementById('bicep');
            const thigh = document.getElementById('thigh');
            const neck = document.getElementById('neck');
            const notes = document.getElementById('notes');
            
            // Заполняем форму данными
            if (measurementDate) {
                // Правильно обрабатываем дату для input[type="date"]
                const date = new Date(measurement.measurement_date);
                // Используем локальную дату без коррекции часового пояса
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;
                
                console.log('Оригинальная дата:', measurement.measurement_date);
                console.log('Обработанная дата:', formattedDate);
                
                measurementDate.value = formattedDate;
                console.log('Значение поля даты после установки:', measurementDate.value);
            }
            if (weight) weight.value = measurement.weight || '';
            if (height) height.value = {{ auth()->user()->height ?? 'null' }};
            if (bodyFatPercentage) bodyFatPercentage.value = measurement.body_fat_percentage || '';
            if (muscleMass) muscleMass.value = measurement.muscle_mass || '';
            if (waterPercentage) waterPercentage.value = measurement.water_percentage || '';
            if (restingHeartRate) restingHeartRate.value = measurement.resting_heart_rate || '';
            if (systolicPressure) systolicPressure.value = measurement.blood_pressure_systolic || '';
            if (diastolicPressure) diastolicPressure.value = measurement.blood_pressure_diastolic || '';
            if (chest) chest.value = measurement.chest || '';
            if (waist) waist.value = measurement.waist || '';
            if (hips) hips.value = measurement.hips || '';
            if (bicep) bicep.value = measurement.bicep || '';
            if (thigh) thigh.value = measurement.thigh || '';
            if (neck) neck.value = measurement.neck || '';
            if (notes) notes.value = measurement.notes || '';
        } else {
            // Показываем уведомление об ошибке
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'error',
                    title: 'Ошибка загрузки',
                    message: data.message || 'Не удалось загрузить данные измерения'
                }
            }));
        }
    })
    .catch(error => {
        console.error('Ошибка загрузки данных:', error);
        // Показываем уведомление об ошибке
        window.dispatchEvent(new CustomEvent('show-notification', {
            detail: {
                type: 'error',
                title: 'Ошибка',
                message: 'Ошибка загрузки данных измерения'
            }
        }));
    });
    
    if (modal) modal.classList.remove('hidden');
}

async function deleteMeasurement(measurementId) {
    // Показываем модальное окно подтверждения
    showDeleteConfirmationModal(measurementId);
}

function showDeleteConfirmationModal(measurementId) {
    const modal = document.getElementById('deleteConfirmationModal');
    if (!modal) {
        console.error('Modal element not found');
        return;
    }
    
    // Сохраняем ID измерения для последующего удаления
    modal.dataset.measurementId = measurementId;
    
    // Показываем модальное окно
    modal.classList.remove('hidden');
}

async function confirmDeleteMeasurement() {
    const modal = document.getElementById('deleteConfirmationModal');
    const measurementId = modal.dataset.measurementId;
    
    console.log('Начинаем удаление измерения с ID:', measurementId);
    
    if (!measurementId) {
        console.error('Measurement ID not found');
        return;
    }
    
    try {
        const response = await fetch(`/athlete/measurements/${measurementId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            console.log('Измерение успешно удалено на сервере');
            
            // Обновляем данные в Alpine.js компоненте без перезагрузки
            updateMeasurementsInAlpine(null, false, true, measurementId);
            
            // Показываем уведомление об успехе
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'success',
                    title: 'Успех',
                    message: 'Измерение успешно удалено'
                }
            }));
            
            // Закрываем модальное окно
            closeDeleteConfirmationModal();
        } else {
            // Показываем уведомление об ошибке
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'error',
                    title: 'Ошибка удаления',
                    message: result.message || 'Произошла ошибка при удалении измерения'
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
                message: 'Произошла ошибка при удалении измерения'
            }
        }));
    }
}

function closeDeleteConfirmationModal() {
    const modal = document.getElementById('deleteConfirmationModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.dataset.measurementId = '';
    }
}

async function submitMeasurementForm() {
    try {
        // Собираем данные из формы
        const measurementData = {
            measurement_date: document.getElementById('measurement_date').value,
            weight: document.getElementById('weight').value ? parseFloat(document.getElementById('weight').value) : null,
            height: document.getElementById('height').value ? parseFloat(document.getElementById('height').value) : null,
            body_fat_percentage: document.getElementById('body_fat_percentage').value ? parseFloat(document.getElementById('body_fat_percentage').value) : null,
            muscle_mass: document.getElementById('muscle_mass').value ? parseFloat(document.getElementById('muscle_mass').value) : null,
            water_percentage: document.getElementById('water_percentage').value ? parseFloat(document.getElementById('water_percentage').value) : null,
            chest: document.getElementById('chest').value ? parseFloat(document.getElementById('chest').value) : null,
            waist: document.getElementById('waist').value ? parseFloat(document.getElementById('waist').value) : null,
            hips: document.getElementById('hips').value ? parseFloat(document.getElementById('hips').value) : null,
            bicep: document.getElementById('bicep').value ? parseFloat(document.getElementById('bicep').value) : null,
            thigh: document.getElementById('thigh').value ? parseFloat(document.getElementById('thigh').value) : null,
            neck: document.getElementById('neck').value ? parseFloat(document.getElementById('neck').value) : null,
            resting_heart_rate: document.getElementById('resting_heart_rate').value ? parseInt(document.getElementById('resting_heart_rate').value) : null,
            blood_pressure_systolic: document.getElementById('blood_pressure_systolic').value ? parseInt(document.getElementById('blood_pressure_systolic').value) : null,
            blood_pressure_diastolic: document.getElementById('blood_pressure_diastolic').value ? parseInt(document.getElementById('blood_pressure_diastolic').value) : null,
            notes: document.getElementById('notes').value || '',
        };
        
        const isEdit = currentMeasurementId !== null;
        const url = isEdit 
            ? `/athlete/measurements/${currentMeasurementId}`
            : '{{ route("crm.athlete.measurements.store") }}';
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(measurementData)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            // Обновляем данные в Alpine.js компоненте без перезагрузки
            const measurement = result.measurement;
            updateMeasurementsInAlpine(measurement, isEdit);
            
            // Показываем уведомление об успехе
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'success',
                    title: 'Успех',
                    message: isEdit ? 'Измерение успешно обновлено' : 'Измерение успешно сохранено'
                }
            }));
            
            closeMeasurementModal();
            // Больше не перезагружаем страницу - данные обновляются в реальном времени!
        } else {
            // Показываем уведомление об ошибке
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'error',
                    title: 'Ошибка сохранения',
                    message: result.message || 'Произошла ошибка при сохранении измерения'
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
                message: 'Произошла ошибка при сохранении измерения'
            }
        }));
    }
}

function closeMeasurementModal() {
    // Проверяем, что DOM загружен
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', closeMeasurementModal);
        return;
    }
    
    const modal = document.getElementById('measurementModal');
    
    if (!modal) {
        return;
    }
    
    modal.classList.add('hidden');
    currentMeasurementId = null;
}

// Функция для определения категории ИМТ
function getBMICategory(bmi) {
    if (!bmi || isNaN(bmi)) return { text: '—', color: 'text-gray-500', bg: 'bg-gray-100' };
    
    if (bmi < 18.5) {
        return { text: 'Недостаточный вес', color: 'text-blue-600', bg: 'bg-blue-100' };
    } else if (bmi < 25) {
        return { text: 'Нормальный вес', color: 'text-green-600', bg: 'bg-green-100' };
    } else if (bmi < 30) {
        return { text: 'Избыточный вес', color: 'text-yellow-600', bg: 'bg-yellow-100' };
    } else {
        return { text: 'Ожирение', color: 'text-red-600', bg: 'bg-red-100' };
    }
}

// Функция для форматирования чисел
function formatNumber(num, unit = '') {
    if (num === null || num === undefined || isNaN(num)) return '—';
    const formatted = num % 1 === 0 ? num.toString() : num.toFixed(1).replace(/\.?0+$/, '');
    return formatted + unit;
}

// Функция для обновления данных в Alpine.js компоненте
function updateMeasurementsInAlpine(measurement, isEdit = false, isDelete = false, measurementId = null) {
    // Находим Alpine.js компонент
    const alpineComponent = document.querySelector('[x-data*="measurementPagination"]');
    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
        const data = alpineComponent._x_dataStack[0];
        
        console.log('Найден Alpine.js компонент:', data);
        console.log('Текущие измерения:', data.measurements.length);
        console.log('Операция:', isDelete ? 'удаление' : isEdit ? 'редактирование' : 'добавление');
        
        if (isDelete && measurementId) {
            // Удаляем измерение из массива
            const index = data.measurements.findIndex(m => m.id == measurementId);
            console.log('Индекс для удаления:', index);
            
            if (index !== -1) {
                data.measurements.splice(index, 1);
                console.log('Измерение удалено, осталось:', data.measurements.length);
                
                // Обновляем общее количество страниц
                data.totalPages = Math.ceil(data.measurements.length / data.itemsPerPage);
                // Если текущая страница больше общего количества, переходим на последнюю
                if (data.currentPage > data.totalPages) {
                    data.currentPage = Math.max(1, data.totalPages);
                }
                
                // Принудительно обновляем отображение
                data.measurements = [...data.measurements];
            } else {
                console.error('Измерение с ID', measurementId, 'не найдено для удаления');
            }
        } else if (isEdit) {
            // Обновляем существующее измерение
            const index = data.measurements.findIndex(m => m.id == measurement.id);
            if (index !== -1) {
                data.measurements[index] = measurement;
                // Принудительно обновляем отображение
                data.measurements = [...data.measurements];
            }
        } else {
            // Добавляем новое измерение
            data.measurements.unshift(measurement);
            // Обновляем общее количество страниц
            data.totalPages = Math.ceil(data.measurements.length / data.itemsPerPage);
            // Принудительно обновляем отображение
            data.measurements = [...data.measurements];
        }
        
        // Обновляем статистические карточки
        updateStatisticsCards(data.measurements);
        
        console.log('Данные Alpine.js обновлены:', data.measurements.length, 'измерений');
    } else {
        console.error('Alpine.js компонент не найден или не инициализирован');
        // Fallback: пытаемся обновить через прямое обращение к DOM
        console.log('Fallback: обновляем через DOM');
        updateMeasurementsViaDOM(measurement, isEdit, isDelete, measurementId);
    }
}

// Fallback функция для обновления данных через DOM
function updateMeasurementsViaDOM(measurement, isEdit = false, isDelete = false, measurementId = null) {
    if (isDelete && measurementId) {
        // Находим карточку измерения по ID и удаляем её
        const measurementCards = document.querySelectorAll('[x-for*="measurement"]');
        let cardToRemove = null;
        
        measurementCards.forEach(card => {
            const cardElement = card.closest('.card');
            if (cardElement && cardElement.textContent.includes(`id="${measurementId}"`)) {
                cardToRemove = cardElement;
            }
        });
        
        if (cardToRemove) {
            cardToRemove.remove();
            console.log('Карточка измерения удалена из DOM');
        } else {
            console.log('Fallback: перезагружаем страницу');
            window.location.reload();
        }
    } else {
        // Для добавления/редактирования перезагружаем страницу
        console.log('Fallback: перезагружаем страницу');
        window.location.reload();
    }
}

// Функция для обновления статистических карточек
function updateStatisticsCards(measurements) {
    // Обновляем общее количество измерений
    const totalMeasurementsElement = document.querySelector('.stat-value');
    if (totalMeasurementsElement && measurements.length > 0) {
        // Находим элемент с общим количеством измерений (первая статистическая карточка)
        const statsContainer = document.querySelector('.stats-container');
        if (statsContainer) {
            const totalMeasurementsCard = statsContainer.children[0];
            const totalMeasurementsValue = totalMeasurementsCard.querySelector('.stat-value');
            if (totalMeasurementsValue) {
                totalMeasurementsValue.textContent = measurements.length;
            }
        }
    }
    
    // Обновляем последнее измерение
    if (measurements.length > 0) {
        const lastMeasurement = measurements[0]; // Первое в массиве - самое новое
        const lastMeasurementDate = new Date(lastMeasurement.measurement_date);
        
        const statsContainer = document.querySelector('.stats-container');
        if (statsContainer && statsContainer.children[1]) {
            const lastMeasurementCard = statsContainer.children[1];
            const lastMeasurementValue = lastMeasurementCard.querySelector('.stat-value');
            if (lastMeasurementValue) {
                lastMeasurementValue.textContent = lastMeasurementDate.toLocaleDateString('{{ app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US') }}');
            }
        }
        
        // Обновляем текущий вес
        if (statsContainer && statsContainer.children[2]) {
            const currentWeightCard = statsContainer.children[2];
            const currentWeightValue = currentWeightCard.querySelector('.stat-value');
            if (currentWeightValue && lastMeasurement.weight) {
                currentWeightValue.textContent = lastMeasurement.weight + ' {{ __('common.kg') }}';
            }
        }
        
        // Обновляем ИМТ
        if (statsContainer && statsContainer.children[3]) {
            const bmiCard = statsContainer.children[3];
            const bmiValue = bmiCard.querySelector('.stat-value');
            if (bmiValue && lastMeasurement.weight) {
                const height = {{ auth()->user()->height ?? 170 }};
                const bmi = lastMeasurement.weight / ((height/100) ** 2);
                bmiValue.textContent = bmi.toFixed(1);
                
                // Обновляем цвет ИМТ
                bmiValue.className = 'stat-value';
                if (bmi < 18.5) {
                    bmiValue.classList.add('text-blue-600');
                } else if (bmi < 25) {
                    bmiValue.classList.add('text-green-600');
                } else if (bmi < 30) {
                    bmiValue.classList.add('text-yellow-600');
                } else {
                    bmiValue.classList.add('text-red-600');
                }
            }
        }
    }
}

// Делаем функции глобальными для доступа из Alpine.js после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    window.showAddMeasurementModal = showAddMeasurementModal;
    window.editMeasurement = editMeasurement;
    window.deleteMeasurement = deleteMeasurement;
    window.submitMeasurementForm = submitMeasurementForm;
    window.closeMeasurementModal = closeMeasurementModal;
    window.updateMeasurementsInAlpine = updateMeasurementsInAlpine;
    window.updateStatisticsCards = updateStatisticsCards;
    window.showDeleteConfirmationModal = showDeleteConfirmationModal;
    window.confirmDeleteMeasurement = confirmDeleteMeasurement;
    window.closeDeleteConfirmationModal = closeDeleteConfirmationModal;
    window.updateMeasurementsViaDOM = updateMeasurementsViaDOM;
    
});


</script>
@endsection
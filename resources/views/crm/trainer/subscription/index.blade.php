@extends("crm.layouts.app")

@section("title", __('common.subscription'))
@section("page-title", __('common.subscription'))

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link {{ request()->routeIs('crm.dashboard*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        {{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        {{ __('common.athletes') }}
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        {{ __('common.subscription') }}
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ __('common.settings') }}
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link {{ request()->routeIs('crm.dashboard*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        {{ __('common.dashboard') }}
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ __('common.calendar') }}
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        {{ __('common.workouts') }}
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        {{ __('common.athletes') }}
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        {{ __('common.subscription') }}
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="mobile-nav-link">
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
            touchStartX = null;
            touchStartY = null;
            return;
        }

        if (menuIsOpen) {
            if (startX <= menuCloseEdgeGuard) {
                preventEvent(event);
                touchStartX = null;
                touchStartY = null;
                return;
            }
            const menuWidth = getMobileMenuWidth();
            if (targetInsideMenu || startX <= menuWidth + menuCloseEdgeGuard) {
                touchStartX = null;
                touchStartY = null;
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
                touchStartX = null;
                touchStartY = null;
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
        if (menuGestureHandled) return;

        const touch = event.touches[0];
        const deltaX = touch.clientX - touchStartX;
        const deltaY = touch.clientY - (touchStartY ?? 0);
        if (Math.abs(deltaY) > maxVerticalDeviation) return;

        if (menuGesture === 'open' && deltaX > menuSwipeThreshold) {
            openMobileMenu();
            menuGestureHandled = true;
        } else if (menuGesture === 'close' && (touchStartX - touch.clientX) > menuSwipeThreshold) {
            closeMobileMenuIfOpen();
            menuGestureHandled = true;
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

        touchStartX = null;
        touchStartY = null;
        menuGesture = null;
        menuGestureHandled = false;
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
<div id="trainer-subscription-root" class="space-y-6">
    @if($currentSubscription)
    <!-- Текущий план -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('common.current_plan') }}</h3>
                <span class="px-3 py-1 
                    @if($currentSubscription->status === 'trial') bg-blue-100 text-blue-800
                    @elseif($currentSubscription->status === 'active') bg-green-100 text-green-800
                    @elseif($currentSubscription->status === 'expired') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif
                    text-sm font-medium rounded-full">
                    @if($currentSubscription->status === 'trial')
                        {{ __('common.trial') }}
                    @elseif($currentSubscription->status === 'active')
                        {{ __('common.subscription_active') }}
                    @elseif($currentSubscription->status === 'expired')
                        {{ __('common.subscription_expired') }}
                    @else
                        {{ ucfirst($currentSubscription->status) }}
                    @endif
                </span>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ $currentSubscription->plan->name ?? __('common.not_specified') }}
            </div>
                    <div class="text-gray-600 mt-1">{{ __('common.subscription_plan') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">
                        @if($currentSubscription->currency)
                            {{ $currentSubscription->currency->format($currentSubscription->price) }}
                        @else
                            {{ number_format($currentSubscription->price, 2) }} {{ $currentSubscription->currency_code }}
                        @endif
                    </div>
                    <div class="text-gray-600 mt-1">{{ __('common.per_month') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $currentSubscription->expires_date->format('d.m.Y') }}
                    </div>
                    <div class="text-gray-600 mt-1">{{ __('common.valid_until') }}</div>
                </div>
            </div>
            
            @if($currentSubscription->plan && $currentSubscription->plan->description)
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <div class="text-sm text-gray-600">
                        <h4 class="font-medium text-gray-900 mb-3">{{ __('common.description') }}</h4>
                        <p>{{ $currentSubscription->plan->description }}</p>
                    </div>
                </div>
            @endif
            
            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                @if($currentSubscription->is_trial)
                    <p class="text-sm text-gray-600 mb-3">{{ __('common.trial_period') }}: {{ $currentSubscription->trial_days }} {{ __('common.days') }}</p>
                @endif
                @if($currentSubscription->status === 'trial' || $currentSubscription->status === 'active')
                    <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        {{ __('common.extend_subscription') }}
                    </button>
                @endif
            </div>
        </div>
    @else
        <!-- Нет подписки -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('common.no_subscription') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('common.no_subscription') }}</p>
        </div>
    @endif

    @if($subscriptionHistory && $subscriptionHistory->count() > 0)
        <!-- История подписок -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">{{ __('common.subscription_history') }}</h3>
            
            <div class="space-y-4">
                @foreach($subscriptionHistory as $subscription)
                    <div class="flex items-center justify-between py-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                                @if($subscription->status === 'trial') bg-blue-100
                                @elseif($subscription->status === 'active') bg-green-100
                                @elseif($subscription->status === 'expired') bg-red-100
                                @else bg-gray-100
                                @endif">
                                @if($subscription->status === 'trial')
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($subscription->status === 'active' || $subscription->status === 'expired')
                                    <svg class="w-5 h-5 {{ $subscription->status === 'active' ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @endif
                    </div>
                    <div>
                                <div class="font-medium text-gray-900">
                                    @if($subscription->is_trial)
                                        {{ __('common.trial') }} {{ __('common.subscription') }}
                                    @else
                                        {{ $subscription->plan->name ?? __('common.subscription') }}
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $subscription->start_date->format('d.m.Y') }} - {{ $subscription->expires_date->format('d.m.Y') }}
                                </div>
                    </div>
                </div>
                <div class="text-right">
                            <div class="font-medium text-gray-900">
                                @if($subscription->currency)
                                    {{ $subscription->currency->format($subscription->price) }}
                                @else
                                    {{ number_format($subscription->price, 2) }} {{ $subscription->currency_code }}
                                @endif
                            </div>
                            <div class="text-sm 
                                @if($subscription->status === 'active' || $subscription->status === 'trial') text-green-600
                                @elseif($subscription->status === 'expired') text-red-600
                                @else text-gray-600
                                @endif">
                                @if($subscription->status === 'trial')
                                    {{ __('common.trial') }}
                                @elseif($subscription->status === 'active')
                                    {{ __('common.subscription_active') }}
                                @elseif($subscription->status === 'expired')
                                    {{ __('common.subscription_expired') }}
                                @else
                                    {{ ucfirst($subscription->status) }}
                                @endif
                            </div>
                        </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

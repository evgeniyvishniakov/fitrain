@extends("crm.layouts.app")

@section("title", "Дашборд тренера")
@section("page-title", "Дашборд")

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Клиенты
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        Подписка
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
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
    <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Клиенты
    </a>
    <a href="{{ route('crm.trainer.subscription') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>
        Подписка
    </a>
    <a href="{{ route('crm.trainer.settings') }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection


@section("content")
<div class="space-y-6 fade-in-up">

    <!-- Статистика -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Спортсмены</div>
                <div class="stat-value">{{ $athletes ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Тренировки</div>
                <div class="stat-value">{{ $workouts ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Сегодня</div>
                <div class="stat-value">{{ $todayWorkouts }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Завершено</div>
                <div class="stat-value">{{ $completedThisMonth }}</div>
            </div>
        </div>
    </div>

    <!-- Календарь и ближайшие тренировки -->
    <div class="dashboard-bottom-section" x-data="trainerCalendar()">
        <!-- Календарь -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Календарь</h3>
                <div class="flex items-center space-x-1">
                    <button @click="previousMonth()" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <span class="text-xs font-medium text-gray-700" x-text="currentMonthYear"></span>
                    <button @click="nextMonth()" class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Компактный календарь -->
            <div class="calendar-grid">
                <!-- Дни недели -->
                <div class="calendar-header-cell">П</div>
                <div class="calendar-header-cell">В</div>
                <div class="calendar-header-cell">С</div>
                <div class="calendar-header-cell">Ч</div>
                <div class="calendar-header-cell">П</div>
                <div class="calendar-header-cell">С</div>
                <div class="calendar-header-cell">В</div>
                
                <!-- Дни месяца (динамические) -->
                <template x-for="day in calendarDays" :key="day.date">
                    <div class="calendar-day-cell"
                         :class="{
                             'calendar-today': day.isToday,
                             'calendar-other-month': !day.isCurrentMonth
                         }">
                        <div class="calendar-day-content">
                            <div class="calendar-day-number" x-text="day.day"></div>
                            <!-- Квадратик с количеством тренировок -->
                            <template x-if="day.workouts.length > 0">
                                <div class="calendar-workouts">
                                    <div class="calendar-workout-dot calendar-workout-planned"
                                         @click="openWorkoutModal(day)"
                                         :title="'Нажмите для просмотра тренировок'"
                                         x-text="day.workouts.length">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Ближайшие тренировки -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-semibold text-gray-900">Ближайшие тренировки</h3>
                <a href="{{ route('crm.workouts.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    Все
                </a>
            </div>
            
            @php
                $today = now()->format('Y-m-d');
                $tomorrow = now()->addDay()->format('Y-m-d');
                $todayWorkouts = isset($upcomingWorkouts) ? $upcomingWorkouts->where('date', $today) : collect();
                $tomorrowWorkouts = isset($upcomingWorkouts) ? $upcomingWorkouts->where('date', $tomorrow) : collect();
                
                // Если нет тренировок на завтра, но есть на послезавтра, показываем их как "Завтра"
                if($tomorrowWorkouts->count() == 0 && isset($upcomingWorkouts)) {
                    $nextDay = now()->addDays(2)->format('Y-m-d');
                    $tomorrowWorkouts = $upcomingWorkouts->where('date', $nextDay);
                }
            @endphp
            
            <div class="space-y-4">
                    @if($todayWorkouts->count() > 0)
                        <div class="workout-day-group">
                            <h4 class="workout-day-title">Сегодня</h4>
                            <div class="space-y-2">
                                @foreach($todayWorkouts as $workout)
                                    <div class="workout-item">
                                        <div class="workout-content">
                                            <div class="workout-details">
                                                <span class="workout-title">{{ $workout->title }} - {{ $workout->athlete ? $workout->athlete->name : 'Спортсмен не указан' }}</span>
                                                <span class="workout-date">
                                                    @if($workout->time)
                                                        {{ $workout->time ? \Carbon\Carbon::parse($workout->time)->format('H:i') : '' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    <span class="workout-status-badge
                                        {{ $workout->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($workout->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $workout->status === 'completed' ? 'Завершена' : 
                                           ($workout->status === 'planned' ? 'Запланирована' : 'Отменена') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                    @if($tomorrowWorkouts->count() > 0)
                        <div class="workout-day-group">
                            @php
                                $tomorrowDate = $tomorrowWorkouts->first()->date;
                                $isActuallyTomorrow = $tomorrowDate === $tomorrow;
                                $dayTitle = $isActuallyTomorrow ? 'Завтра' : \Carbon\Carbon::parse($tomorrowDate)->format('d.m');
                            @endphp
                            <h4 class="workout-day-title">{{ $dayTitle }}</h4>
                            <div class="space-y-2">
                                @foreach($tomorrowWorkouts as $workout)
                                    <div class="workout-item">
                                        <div class="workout-content">
                                            <div class="workout-details">
                                                <span class="workout-title">{{ $workout->title }} - {{ $workout->athlete ? $workout->athlete->name : 'Спортсмен не указан' }}</span>
                                                <span class="workout-date">
                                                    @if($workout->time)
                                                        {{ $workout->time ? \Carbon\Carbon::parse($workout->time)->format('H:i') : '' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    <span class="workout-status-badge
                                        {{ $workout->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($workout->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $workout->status === 'completed' ? 'Завершена' : 
                                           ($workout->status === 'planned' ? 'Запланирована' : 'Отменена') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($todayWorkouts->count() == 0 && $tomorrowWorkouts->count() == 0)
                    <div class="text-center py-4">
                        <div class="text-gray-400 mb-1">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-xs">Нет тренировок на сегодня и завтра</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Простое модальное окно с тренировками -->
    <div id="workoutModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Тренировки</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="workoutList">
                    <!-- Список тренировок будет добавлен через JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Закрыть
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function trainerCalendar() {
    return {
        currentDate: '{{ now()->format('Y-m-d') }}',
        monthWorkouts: @json($monthWorkouts ?? []),
        
        // Модальное окно
        showWorkoutModal: false,
        selectedDayWorkouts: [],
        selectedDate: '',
        
        // Отладочная информация
        debug: false,
        
        get currentMonthYear() {
            const date = new Date(this.currentDate);
            const monthYear = date.toLocaleDateString('ru-RU', { month: 'long', year: 'numeric' });
            return monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        },
        
        get calendarDays() {
            // Отладочная информация
            if (this.debug) {
                console.log('Month workouts:', this.monthWorkouts);
                console.log('Current date:', this.currentDate);
            }
            
            const date = new Date(this.currentDate);
            const year = date.getFullYear();
            const month = date.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startOfCalendar = new Date(firstDay);
            startOfCalendar.setDate(startOfCalendar.getDate() - firstDay.getDay() + 1); // Понедельник
            const endOfCalendar = new Date(lastDay);
            endOfCalendar.setDate(endOfCalendar.getDate() + (7 - lastDay.getDay())); // Воскресенье
            
            const days = [];
            const currentDay = new Date(startOfCalendar);
            
            while (currentDay <= endOfCalendar) {
                const year = currentDay.getFullYear();
                const monthNum = currentDay.getMonth() + 1;
                const dayNum = currentDay.getDate();
                const dayString = `${year}-${String(monthNum).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
                
                const dayWorkouts = this.monthWorkouts.filter(workout => {
                    const workoutDate = new Date(workout.date);
                    const workoutYear = workoutDate.getFullYear();
                    const workoutMonth = workoutDate.getMonth() + 1;
                    const workoutDay = workoutDate.getDate();
                    const workoutDateString = `${workoutYear}-${String(workoutMonth).padStart(2, '0')}-${String(workoutDay).padStart(2, '0')}`;
                    
                    // Отладочная информация
                    if (this.debug && workoutDateString === dayString) {
                        console.log('Found workout for day:', dayString, workout);
                    }
                    
                    return workoutDateString === dayString;
                });
                
                // Отладочная информация для дней с тренировками
                if (this.debug && dayWorkouts.length > 0) {
                    console.log('Day with workouts:', dayString, 'Workouts:', dayWorkouts);
                }
                
                days.push({
                    date: dayString,
                    day: currentDay.getDate(),
                    isCurrentMonth: currentDay.getMonth() === month,
                    isToday: dayString === '{{ now()->format('Y-m-d') }}',
                    workouts: dayWorkouts
                });
                
                currentDay.setDate(currentDay.getDate() + 1);
            }
            
            return days;
        },
        
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
            this.currentDate = '{{ now()->format('Y-m-d') }}';
            this.loadWorkouts();
        },
        
        async loadWorkouts() {
            try {
                const startDate = new Date(this.currentDate);
                startDate.setDate(1);
                const endDate = new Date(this.currentDate);
                endDate.setMonth(endDate.getMonth() + 1);
                endDate.setDate(0);
                
                const response = await fetch(`/trainer/workouts/api?start_date=${startDate.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`);
                const data = await response.json();
                
                if (data.success) {
                    this.monthWorkouts = data.workouts;
                }
            } catch (error) {
                console.error('Ошибка загрузки тренировок:', error);
            }
        },
        
        // Функции для модального окна
        openWorkoutModal(day) {
            console.log('Opening modal for day:', day);
            this.selectedDayWorkouts = day.workouts;
            this.selectedDate = this.formatDate(day.date);
            this.showWorkoutModal = true;
            console.log('Modal should be visible:', this.showWorkoutModal);
        },
        
        closeWorkoutModal() {
            this.showWorkoutModal = false;
            this.selectedDayWorkouts = [];
            this.selectedDate = '';
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU', { 
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
        },
        
        // Функция для открытия модального окна из Alpine.js
        openWorkoutModal(day) {
            // Вызываем глобальную функцию
            window.openWorkoutModal(day);
        }
    }
}

// Простые функции для модального окна
window.openWorkoutModal = function(day) {
    console.log('Opening modal for day:', day);
    
    const modal = document.getElementById('workoutModal');
    const modalTitle = document.getElementById('modalTitle');
    const workoutList = document.getElementById('workoutList');
    
    // Устанавливаем заголовок
    const date = new Date(day.date);
    const formattedDate = date.toLocaleDateString('ru-RU', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
    modalTitle.textContent = 'Тренировки на ' + formattedDate;
    
    // Очищаем список тренировок
    workoutList.innerHTML = '';
    
    // Добавляем тренировки
    if (day.workouts && day.workouts.length > 0) {
        day.workouts.forEach(workout => {
            const workoutDiv = document.createElement('div');
            workoutDiv.className = 'workout-item-modal';
            
            const statusClass = {
                'completed': 'bg-green-100 text-green-800',
                'planned': 'bg-blue-100 text-blue-800',
                'cancelled': 'bg-red-100 text-red-800'
            }[workout.status] || 'bg-gray-100 text-gray-800';
            
            const statusText = {
                'completed': 'Завершена',
                'planned': 'Запланирована',
                'cancelled': 'Отменена'
            }[workout.status] || 'Неизвестно';
            
            const time = workout.time ? workout.time.substring(0, 5) : 'Время не указано';
            
            workoutDiv.innerHTML = `
                <div class="workout-row">
                    <span class="workout-title">${workout.title}</span>
                    <span class="workout-athlete">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        ${workout.athlete_name || 'Спортсмен не указан'}
                    </span>
                    <span class="workout-time">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ${time}
                    </span>
                    <span class="workout-status ${statusClass}">${statusText}</span>
                </div>
            `;
            
            workoutList.appendChild(workoutDiv);
        });
    } else {
        workoutList.innerHTML = '<div class="text-center py-4 text-gray-500">Нет тренировок на эту дату</div>';
    }
    
    // Показываем модальное окно
    modal.style.display = 'block';
}

window.closeModal = function() {
    const modal = document.getElementById('workoutModal');
    modal.style.display = 'none';
}

// Закрытие модального окна при клике вне его
window.onclick = function(event) {
    const modal = document.getElementById('workoutModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<style>
/* Мобильная адаптация карточек в 2 ряда */
@media (max-width: 768px) {
    .stats-container {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .stats-container .stat-card {
        flex-direction: column !important;
        text-align: center !important;
        padding: 1rem !important;
    }
    
    .stats-container .stat-content {
        margin-top: 0.5rem !important;
    }
    
    .stats-container .stat-icon {
        margin: 0 auto !important;
    }
}

.dashboard-bottom-section {
    display: flex !important;
    flex-direction: column !important; /* Mobile: колонка */
    gap: 1.5rem !important;
}

@media (min-width: 1024px) {
    .dashboard-bottom-section {
        flex-direction: row !important; /* Desktop: ряд */
        align-items: stretch !important;
    }
}

.dashboard-bottom-section > div:first-child {
    flex: 0 0 40% !important; /* Календарь - 40% ширины */
}

.dashboard-bottom-section > div:last-child {
    flex: 0 0 60% !important; /* Тренировки - 60% ширины */
}

/* Стили календаря */
.calendar-grid {
    display: grid !important;
    grid-template-columns: repeat(7, 1fr) !important;
    gap: 1px !important;
    background-color: #e5e7eb !important; /* Серые линии между ячейками */
    border-radius: 0.5rem !important;
    overflow: hidden !important;
}

.calendar-header-cell {
    background-color: #f9fafb !important;
    padding: 0.5rem !important;
    text-align: center !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #6b7280 !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.calendar-day-cell {
    background-color: #ffffff !important;
    padding: 0.25rem !important;
    text-align: center !important;
    font-size: 0.75rem !important;
    color: #374151 !important;
    min-height: 4rem !important;
    display: flex !important;
    align-items: flex-start !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: background-color 0.2s !important;
}

.calendar-day-cell:hover {
    background-color: #f3f4f6 !important;
}

.calendar-today {
    background-color: #dbeafe !important;
    color: #1d4ed8 !important;
    font-weight: 600 !important;
}

.calendar-other-month {
    color: #9ca3af !important;
}

/* Содержимое ячейки календаря */
.calendar-day-content {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 0.25rem !important;
}

.calendar-day-number {
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    text-align: center !important;
}

.calendar-workouts {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
    gap: 0.125rem !important;
    flex: 1 !important;
    justify-content: center !important;
    align-items: center !important;
}

/* Стили для квадратиков тренировок */
.calendar-workout-dot {
    width: 1.25rem !important;
    height: 1.25rem !important;
    border-radius: 0.25rem !important;
    margin: 0.125rem !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

.calendar-workout-completed {
    background-color: #22c55e !important; /* Зеленый */
}

.calendar-workout-planned {
    background-color: #3b82f6 !important; /* Синий */
}

.calendar-workout-cancelled {
    background-color: #ef4444 !important; /* Красный */
}

.calendar-workout-more-dots {
    background-color: #6b7280 !important;
    color: #ffffff !important;
    padding: 0.0625rem 0.125rem !important;
    border-radius: 0.125rem !important;
    font-size: 0.375rem !important;
    font-weight: 600 !important;
    text-align: center !important;
    margin: 0.0625rem !important;
    min-width: 0.75rem !important;
    height: 0.5rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
}

/* Стили для секции ближайших тренировок */
.workout-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0.75rem !important;
    background-color: #f9fafb !important;
    border-radius: 0.5rem !important;
    transition: all 0.2s ease !important;
}

.workout-item:hover {
    background-color: #f3f4f6 !important;
    transform: translateY(-1px) !important;
}

.workout-content {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    flex: 1 !important;
    min-width: 0 !important;
}


.workout-details {
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    min-width: 0 !important;
    flex: 1 !important;
}

.workout-title {
    font-size: 1rem !important;
    font-weight: 500 !important;
    color: #111827 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    flex: 1 !important;
    min-width: 0 !important;
}

.workout-date {
    font-size: 0.875rem !important;
    color: #6b7280 !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.workout-status-badge {
    padding: 0.375rem 0.75rem !important;
    border-radius: 9999px !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    white-space: nowrap !important;
    margin-left: 0.75rem !important;
    flex-shrink: 0 !important;
}

/* Мобильная адаптация для тренировок */
@media (max-width: 768px) {
    .workout-item {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.5rem !important;
        padding: 0.75rem !important;
    }
    
    .workout-content {
        gap: 0.5rem !important;
    }
    
    .workout-details {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.25rem !important;
    }
    
    .workout-title {
        font-size: 0.875rem !important;
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
    }
    
    .workout-date {
        font-size: 0.75rem !important;
    }
    
    .workout-status-badge {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        margin-left: 0 !important;
        align-self: flex-start !important;
    }
    
}

/* Стили для групп тренировок */
.workout-day-group {
    margin-bottom: 1rem;
}

.workout-day-title {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    padding-bottom: 4px;
    border-bottom: 1px solid #e5e7eb;
}

/* Мобильная адаптация календаря */
@media (max-width: 768px) {
    .calendar-header-cell {
        padding: 0.375rem 0.25rem !important;
        font-size: 0.625rem !important;
    }
    
    .calendar-day-cell {
        min-height: 3rem !important;
        padding: 0.125rem !important;
    }
    
    .calendar-day-number {
        font-size: 0.625rem !important;
    }
    
    .calendar-workout-dot {
        width: 1rem !important;
        height: 1rem !important;
        margin: 0.0625rem !important;
        font-size: 0.625rem !important;
    }
    
    .calendar-workout-more-dots {
        font-size: 0.25rem !important;
        padding: 0.03125rem 0.0625rem !important;
        min-width: 0.5rem !important;
        height: 0.375rem !important;
        margin: 0.03125rem !important;
    }
}

/* Стили для модального окна */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: none;
    border-radius: 8px;
    width: 90%;
    max-width: 700px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
}

.close {
    color: #6b7280;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: #374151;
}

.modal-body {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    text-align: right;
}


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

/* Стили для темной темы - заголовки черного цвета */
@media (prefers-color-scheme: dark) {
    .text-base.font-semibold.text-gray-900,
    .text-lg.font-semibold.text-gray-900 {
        color: #000000 !important;
    }
}

/* Альтернативный способ для темной темы через класс */
.dark .text-base.font-semibold.text-gray-900,
.dark .text-lg.font-semibold.text-gray-900 {
    color: #000000 !important;
}
</style>
@endsection
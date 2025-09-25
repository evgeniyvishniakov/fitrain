@extends("crm.layouts.app")

@section("title", "Дашборд спортсмена")
@section("page-title", "Дашборд")

<script>
function dashboardCalendar() {
    return {
        currentDate: '{{ now()->format('Y-m-d') }}',
        monthWorkouts: @json($monthWorkouts),
        
        get currentMonthYear() {
            const date = new Date(this.currentDate);
            const monthYear = date.toLocaleDateString('ru-RU', { month: 'long', year: 'numeric' });
            return monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        },
        
        get calendarDays() {
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
                    return workoutDateString === dayString;
                });
                
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
                
                const response = await fetch(`/athlete/workouts/api?start_date=${startDate.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`);
                const data = await response.json();
                
                if (data.success) {
                    this.monthWorkouts = data.workouts;
                }
            } catch (error) {
                console.error('Ошибка загрузки тренировок:', error);
            }
        },
        
        // Функция для определения категории ИМТ
        getBMICategory(bmi) {
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
    }
}
</script>

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="{{ route('crm.calendar') }}" class="nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="{{ route('crm.calendar') }}" class="mobile-nav-link {{ request()->routeIs('crm.calendar') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.athlete.workouts") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link">
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
        <!-- Всего тренировок -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Всего тренировок</div>
                <div class="stat-value">{{ $totalWorkouts ?? 0 }}</div>
            </div>
        </div>

        <!-- Запланировано -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Запланировано</div>
                <div class="stat-value">{{ $plannedWorkouts ?? 0 }}</div>
            </div>
        </div>

        <!-- Завершено -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-green">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Завершено</div>
                <div class="stat-value">{{ $completedWorkouts ?? 0 }}</div>
            </div>
        </div>

        <!-- Последняя/Следующая тренировка -->
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">
                    @if($lastOrNextWorkout)
                        @if($lastOrNextWorkout->status === 'completed')
                            Последняя тренировка
                        @else
                            Следующая тренировка
                        @endif
                    @else
                        Тренировки
                    @endif
                </div>
                <div class="stat-value">
                    @if($lastOrNextWorkout)
                        {{ \Carbon\Carbon::parse($lastOrNextWorkout->date)->format('d.m') }}
                    @else
                        0
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Календарь и ближайшие тренировки -->
    <div class="dashboard-bottom-section" x-data="dashboardCalendar()">
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
                            <template x-if="day.workouts.length > 0">
                                <div class="calendar-workouts">
                                    <template x-for="workout in day.workouts.slice(0, 2)" :key="workout.id">
                                        <div class="calendar-workout-item"
                                             :class="{
                                                 'calendar-workout-completed': workout.status === 'completed',
                                                 'calendar-workout-planned': workout.status === 'planned',
                                                 'calendar-workout-cancelled': workout.status === 'cancelled'
                                             }">
                                            <div class="calendar-workout-time" x-text="workout.time ? workout.time.substring(0, 5) : ''"></div>
                                            <div class="calendar-workout-title" x-text="workout.title"></div>
                                        </div>
                                    </template>
                                    <template x-if="day.workouts.length > 2">
                                        <div class="calendar-workout-more" x-text="'+' + (day.workouts.length - 2)"></div>
                                    </template>
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
                <a href="{{ route('crm.athlete.workouts') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                    Все
                </a>
            </div>
            
            @if($upcomingWorkouts->count() > 0)
                <div class="space-y-2">
                    @foreach($upcomingWorkouts->take(4) as $workout)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-3 h-3 rounded-full 
                                    {{ $workout->status === 'completed' ? 'bg-green-500' : 
                                       ($workout->status === 'planned' ? 'bg-blue-500' : 'bg-red-500') }}">
                                </div>
                                <div class="flex items-center space-x-3 min-w-0 flex-1">
                                    <span class="text-base font-medium text-gray-900 truncate">{{ $workout->title }}</span>
                                    <span class="text-sm text-gray-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($workout->date)->format('d.m') }}
                                        @if($workout->time)
                                            {{ \Carbon\Carbon::parse($workout->time)->format('H:i') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 rounded-full text-sm font-semibold ml-3 whitespace-nowrap
                                {{ $workout->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($workout->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                {{ $workout->status === 'completed' ? 'Завершена' : 
                                   ($workout->status === 'planned' ? 'Запланирована' : 'Отменена') }}
                            </span>
                        </div>
                    @endforeach
                    @if($upcomingWorkouts->count() > 4)
                        <div class="text-center pt-1">
                            <span class="text-xs text-gray-500">+{{ $upcomingWorkouts->count() - 4 }} еще</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <div class="text-gray-400 mb-1">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-xs">Нет тренировок</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Карточки измерений -->
    <div class="flex gap-6" x-data="dashboardCalendar()">
        <!-- Текущий вес -->
        <div class="stat-card flex-1">
            <div class="stat-icon stat-icon-blue">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Текущий вес</div>
                <div class="stat-value">{{ $currentWeight ? $currentWeight . ' кг' : 'Не указан' }}</div>
            </div>
        </div>

        <!-- ИМТ -->
        <div class="stat-card flex-1">
            <div class="stat-icon stat-icon-{{ $bmiColor ?? 'gray' }}">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label flex items-center gap-1">
                    <span>ИМТ</span>
                    <!-- Иконка знака вопроса с подсказкой -->
                    <div class="relative group">
                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3 3 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <!-- Всплывающая подсказка -->
                        <div class="absolute top-full right-0 mt-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
                            <div class="font-semibold mb-2">Индекс массы тела (ИМТ)</div>
                            <div class="space-y-1">
                                <div class="flex justify-between"><span class="text-blue-300">Менее 18.5:</span> <span>Недостаточный вес</span></div>
                                <div class="flex justify-between"><span class="text-green-300">18.5 - 24.9:</span> <span>Нормальный вес</span></div>
                                <div class="flex justify-between"><span class="text-yellow-300">25 - 29.9:</span> <span>Избыточный вес</span></div>
                                <div class="flex justify-between"><span class="text-red-300">30 и более:</span> <span>Ожирение</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                    $bmiTextColor = 'text-gray-500';
                    if ($bmi && $bmiCategory) {
                        if ($bmiColor === 'blue') {
                            $bmiTextColor = 'text-blue-600';
                        } elseif ($bmiColor === 'green') {
                            $bmiTextColor = 'text-green-600';
                        } elseif ($bmiColor === 'yellow') {
                            $bmiTextColor = 'text-yellow-600';
                        } elseif ($bmiColor === 'red') {
                            $bmiTextColor = 'text-red-600';
                        }
                    }
                @endphp
                <div class="stat-value {{ $bmiTextColor }}" 
                     @if($bmi && $bmiColor === 'green') style="color: #059669 !important;" 
                     @elseif($bmi && $bmiColor === 'blue') style="color: #2563eb !important;" 
                     @elseif($bmi && $bmiColor === 'yellow') style="color: #d97706 !important;" 
                     @elseif($bmi && $bmiColor === 'red') style="color: #dc2626 !important;" 
                     @endif>
                    {{ $bmi ? number_format($bmi, 1) : '—' }}
                </div>
            </div>
        </div>

        <!-- Всего измерений -->
        <div class="stat-card flex-1">
            <div class="stat-icon stat-icon-purple">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Всего измерений</div>
                <div class="stat-value">{{ $totalMeasurements }}</div>
            </div>
        </div>

        <!-- Последнее измерение -->
        <div class="stat-card flex-1">
            <div class="stat-icon stat-icon-orange">
                <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Последнее измерение</div>
                <div class="stat-value">{{ $lastMeasurement ? $lastMeasurement->measurement_date->format('d.m.Y') : 'Нет данных' }}</div>
            </div>
        </div>
    </div>

    <!-- Последнее измерение и график веса -->
    @if($lastMeasurement)
    <div class="measurement-chart-row">
        <!-- Карточка последнего измерения -->
        <div class="measurement-detail-card">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 h-full">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Последнее измерение</h3>
                    <span class="text-sm text-gray-500">{{ $lastMeasurement->measurement_date->format('d.m.Y') }}</span>
                </div>
                
                <!-- Основные параметры -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-xl font-bold text-blue-600">{{ $lastMeasurement->weight ?? '—' }}</div>
                        <div class="text-xs text-blue-800">Вес (кг)</div>
                    </div>
                    <div class="text-center p-3 rounded-lg 
                        @if($bmi && $bmiColor === 'blue') bg-blue-50
                        @elseif($bmi && $bmiColor === 'green') bg-green-50
                        @elseif($bmi && $bmiColor === 'yellow') bg-yellow-50
                        @elseif($bmi && $bmiColor === 'red') bg-red-50
                        @else bg-gray-50
                        @endif">
                        <div class="text-xl font-bold 
                            @if($bmi && $bmiColor === 'blue') text-blue-600
                            @elseif($bmi && $bmiColor === 'green') text-green-600
                            @elseif($bmi && $bmiColor === 'yellow') text-yellow-600
                            @elseif($bmi && $bmiColor === 'red') text-red-600
                            @else text-gray-600
                            @endif">{{ $bmi ? number_format($bmi, 1) : '—' }}</div>
                        <div class="text-xs 
                            @if($bmi && $bmiColor === 'blue') text-blue-600
                            @elseif($bmi && $bmiColor === 'green') text-green-600
                            @elseif($bmi && $bmiColor === 'yellow') text-yellow-600
                            @elseif($bmi && $bmiColor === 'red') text-red-600
                            @else text-gray-600
                            @endif">ИМТ</div>
                    </div>
                </div>
                
                <!-- Дополнительные параметры -->
                <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">% жира:</span>
                        <span class="font-medium">{{ $lastMeasurement->body_fat_percentage ? number_format($lastMeasurement->body_fat_percentage, 1) . '%' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Мышцы:</span>
                        <span class="font-medium">{{ $lastMeasurement->muscle_mass ? number_format($lastMeasurement->muscle_mass, 1) . ' кг' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Вода:</span>
                        <span class="font-medium">{{ $lastMeasurement->water_percentage ? number_format($lastMeasurement->water_percentage, 1) . '%' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Пульс:</span>
                        <span class="font-medium">{{ $lastMeasurement->resting_heart_rate ? round($lastMeasurement->resting_heart_rate) . ' уд/мин' : '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Давление:</span>
                        <span class="font-medium">
                            @if($lastMeasurement->blood_pressure_systolic && $lastMeasurement->blood_pressure_diastolic)
                                {{ round($lastMeasurement->blood_pressure_systolic) }}/{{ round($lastMeasurement->blood_pressure_diastolic) }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- Объемы тела -->
                @if($lastMeasurement->chest || $lastMeasurement->waist || $lastMeasurement->hips || $lastMeasurement->bicep || $lastMeasurement->thigh || $lastMeasurement->neck)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Объемы тела (см)</h5>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @if($lastMeasurement->chest)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Грудь:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->chest, 1) }}</span>
                        </div>
                        @endif
                        @if($lastMeasurement->waist)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Талия:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->waist, 1) }}</span>
                        </div>
                        @endif
                        @if($lastMeasurement->hips)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Бедра:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->hips, 1) }}</span>
                        </div>
                        @endif
                        @if($lastMeasurement->bicep)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Бицепс:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->bicep, 1) }}</span>
                        </div>
                        @endif
                        @if($lastMeasurement->thigh)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Бедро:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->thigh, 1) }}</span>
                        </div>
                        @endif
                        @if($lastMeasurement->neck)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Шея:</span>
                            <span class="font-medium">{{ number_format($lastMeasurement->neck, 1) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Комментарии -->
                @if($lastMeasurement->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h5 class="text-sm font-medium text-gray-700 mb-1">Комментарии</h5>
                    <p class="text-sm text-gray-600">{{ $lastMeasurement->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- График веса -->
        <div class="chart-card">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Динамика веса</h3>
                <div class="h-80">
                    <canvas id="weightChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

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

@media (max-width: 768px) {
    .flex.gap-6 {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
    }
    
    .flex.gap-6 .stat-card {
        flex-direction: column !important;
        text-align: center !important;
        padding: 1rem !important;
    }
    
    .flex.gap-6 .stat-content {
        margin-top: 0.5rem !important;
    }
    
    .flex.gap-6 .stat-icon {
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

.dashboard-bottom-section > div {
    flex: 1 !important; /* Равная ширина блоков */
}

/* Стили для блока измерений и графика */
.measurement-chart-row {
    display: flex !important;
    gap: 1.5rem !important;
    align-items: stretch !important; /* Равная высота блоков */
}

.measurement-detail-card {
    flex: 0 0 33.333333% !important; /* 1/3 ширины */
}

.chart-card {
    flex: 0 0 66.666667% !important; /* 2/3 ширины */
}

/* Мобильная адаптация для секции измерения и графика */
@media (max-width: 768px) {
    .measurement-chart-row {
        flex-direction: column !important; /* Мобильные: колонка */
        gap: 1rem !important;
    }
    
    .measurement-detail-card,
    .chart-card {
        flex: none !important; /* Убираем фиксированную ширину на мобильных */
        width: 100% !important;
    }
    
    .p-6 {
        padding: 1rem !important;
    }
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
    flex-direction: column !important;
    gap: 0.125rem !important;
    flex: 1 !important;
}

.calendar-workout-item {
    padding: 0.125rem 0.25rem !important;
    border-radius: 0.25rem !important;
    font-size: 0.625rem !important;
    line-height: 1 !important;
    text-align: center !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
}

.calendar-workout-completed {
    background-color: #dcfce7 !important;
    color: #166534 !important;
    border: 1px solid #bbf7d0 !important;
}

.calendar-workout-planned {
    background-color: #dbeafe !important;
    color: #1e40af !important;
    border: 1px solid #93c5fd !important;
}

.calendar-workout-cancelled {
    background-color: #fecaca !important;
    color: #991b1b !important;
    border: 1px solid #fca5a5 !important;
}

.calendar-workout-time {
    font-weight: 600 !important;
    font-size: 0.5rem !important;
}

.calendar-workout-title {
    font-weight: 500 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.calendar-workout-more {
    background-color: #f3f4f6 !important;
    color: #6b7280 !important;
    padding: 0.125rem 0.25rem !important;
    border-radius: 0.25rem !important;
    font-size: 0.5rem !important;
    font-weight: 600 !important;
    text-align: center !important;
    border: 1px solid #d1d5db !important;
}

</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Создаем график веса
    const ctx = document.getElementById('weightChart');
    if (ctx) {
        // Получаем данные измерений
        const measurements = @json($athlete->measurements()->latest('measurement_date')->take(10)->get());
        
        if (measurements.length > 0) {
            // Сортируем по дате
            const sortedMeasurements = measurements.sort((a, b) => new Date(a.measurement_date) - new Date(b.measurement_date));
            
            // Подготавливаем данные
            const labels = sortedMeasurements.map(m => {
                const date = new Date(m.measurement_date);
                return date.toLocaleDateString('ru-RU', { month: 'short', day: 'numeric' });
            });
            
            const weightData = sortedMeasurements.map(m => m.weight).filter(val => val !== null && val !== undefined);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Вес (кг)',
                        data: weightData,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
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
                                color: '#f3f4f6'
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6b7280'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        } else {
            // Показываем сообщение, если нет данных
            ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-64 text-gray-500">Нет данных для отображения</div>';
        }
    }
});
</script>
@endpush
@endsection
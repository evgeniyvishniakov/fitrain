@extends("crm.layouts.app")

@section("title", "Fitrain CRM")
@section("page-title", "CRM")

@section("content")
<div x-data="crmApp()" class="space-y-6 fade-in-up">
    
    <!-- Навигация по разделам -->
    <div class="card p-6">
        <div class="flex flex-wrap gap-2">
            <button @click="currentPage = 'dashboard'" 
                    :class="currentPage === 'dashboard' ? 'btn' : 'btn-secondary'"
                    class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Дашборд
            </button>
            
            <button @click="currentPage = 'workouts'" 
                    :class="currentPage === 'workouts' ? 'btn' : 'btn-secondary'"
                    class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Тренировки
            </button>
            
            <button @click="currentPage = 'progress'" 
                    :class="currentPage === 'progress' ? 'btn' : 'btn-secondary'"
                    class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Прогресс
            </button>
            
            @if(auth()->user()->hasRole('trainer'))
                <button @click="currentPage = 'athletes'" 
                        :class="currentPage === 'athletes' ? 'btn' : 'btn-secondary'"
                        class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Клиенты
                </button>
            @else
                <button @click="currentPage = 'nutrition'" 
                        :class="currentPage === 'nutrition' ? 'btn' : 'btn-secondary'"
                        class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    Питание
                </button>
            @endif
        </div>
    </div>

    <!-- ДАШБОРД -->
    <div x-show="currentPage === 'dashboard'" x-transition>
        @include('crm.sections.dashboard')
    </div>

    <!-- ТРЕНИРОВКИ -->
    <div x-show="currentPage === 'workouts'" x-transition>
        @include('crm.sections.workouts')
    </div>

    <!-- ПРОГРЕСС -->
    <div x-show="currentPage === 'progress'" x-transition>
        @include('crm.sections.progress')
    </div>

    <!-- КЛИЕНТЫ (для тренера) -->
    @if(auth()->user()->hasRole('trainer'))
        <div x-show="currentPage === 'athletes'" x-transition>
            @include('crm.sections.athletes')
        </div>
    @endif

    <!-- ПИТАНИЕ (для спортсмена) -->
    @if(auth()->user()->hasRole('athlete'))
        <div x-show="currentPage === 'nutrition'" x-transition>
            @include('crm.sections.nutrition')
        </div>
    @endif
</div>

<script>
function crmApp() {
    return {
        currentPage: 'dashboard',
        workouts: [],
        currentWorkout: null,
        athletes: [],
        currentAthlete: null,
        progress: [],
        currentProgress: null,
        nutrition: [],
        currentNutrition: null,
        
        // Загрузка данных
        async loadData() {
            try {
                // Загружаем тренировки
                const workoutsResponse = await fetch('/api/workouts');
                this.workouts = await workoutsResponse.json();
                
                // Загружаем спортсменов (для тренера)
                if (this.isTrainer()) {
                    const athletesResponse = await fetch('/api/athletes');
                    this.athletes = await athletesResponse.json();
                }
                
                // Загружаем прогресс
                const progressResponse = await fetch('/api/progress');
                this.progress = await progressResponse.json();
                
                // Загружаем питание (для спортсмена)
                if (this.isAthlete()) {
                    const nutritionResponse = await fetch('/api/nutrition');
                    this.nutrition = await nutritionResponse.json();
                }
            } catch (error) {
                console.error('Ошибка загрузки данных:', error);
            }
        },
        
        // Проверка роли
        isTrainer() {
            return {{ auth()->user()->hasRole('trainer') ? 'true' : 'false' }};
        },
        
        isAthlete() {
            return {{ auth()->user()->hasRole('athlete') ? 'true' : 'false' }};
        },
        
        // Навигация
        showPage(page) {
            this.currentPage = page;
        },
        
        // Тренировки
        createWorkout() {
            this.currentWorkout = {
                title: '',
                description: '',
                athlete_id: '',
                date: new Date().toISOString().split('T')[0],
                duration: 60,
                status: 'planned'
            };
            this.currentPage = 'workout-create';
        },
        
        editWorkout(workout) {
            this.currentWorkout = { ...workout };
            this.currentPage = 'workout-edit';
        },
        
        async saveWorkout() {
            try {
                const url = this.currentWorkout.id ? `/api/workouts/${this.currentWorkout.id}` : '/api/workouts';
                const method = this.currentWorkout.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.currentWorkout)
                });
                
                if (response.ok) {
                    await this.loadData();
                    this.currentPage = 'workouts';
                    this.currentWorkout = null;
                }
            } catch (error) {
                console.error('Ошибка сохранения тренировки:', error);
            }
        },
        
        async deleteWorkout(workoutId) {
            if (confirm('Удалить тренировку?')) {
                try {
                    const response = await fetch(`/api/workouts/${workoutId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        await this.loadData();
                    }
                } catch (error) {
                    console.error('Ошибка удаления тренировки:', error);
                }
            }
        },
        
        // Инициализация
        init() {
            this.loadData();
        }
    }
}
</script>
@endsection


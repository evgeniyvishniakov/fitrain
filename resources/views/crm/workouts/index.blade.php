@extends("crm.layouts.app")

@section("title", "Тренировки")
@section("page-title", "Тренировки")

<script>
// SPA функциональность для тренировок
function workoutApp() {
    return {
        currentView: 'list', // list, create, edit, view
        workouts: @json($workouts->items()),
        currentWorkout: null,
        search: '',
        status: '',
        currentPage: 1,
        itemsPerPage: 2,
        
        // Поля формы
        formTitle: '',
        formAthleteId: '',
        formDate: '',
        formDuration: 60,
        formDescription: '',
        formStatus: 'planned',
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentWorkout = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentWorkout = null;
            this.formTitle = '';
            this.formDescription = '';
            this.formAthleteId = '';
            this.formDate = new Date().toISOString().split('T')[0];
            this.formDuration = 60;
            this.formStatus = 'planned';
        },
        
        showEdit(workoutId) {
            this.currentView = 'edit';
            this.currentWorkout = this.workouts.find(w => w.id === workoutId);
            this.formTitle = this.currentWorkout.title;
            this.formDescription = this.currentWorkout.description || '';
            this.formAthleteId = this.currentWorkout.athlete_id;
            this.formDate = this.currentWorkout.date;
            this.formDuration = this.currentWorkout.duration || 60;
            this.formStatus = this.currentWorkout.status;
        },
        
        showView(workoutId) {
            this.currentView = 'view';
            this.currentWorkout = this.workouts.find(w => w.id === workoutId);
        },
        
        // Фильтрация
        get filteredWorkouts() {
            let filtered = this.workouts;
            
            if (this.search) {
                filtered = filtered.filter(w => 
                    w.title.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            if (this.status) {
                filtered = filtered.filter(w => w.status === this.status);
            }
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            const total = Math.ceil(this.filteredWorkouts.length / this.itemsPerPage);
            return total > 0 ? total : 1;
        },
        
        get visiblePages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 5) {
                // Если страниц 5 или меньше, показываем все
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Показываем максимум 5 страниц
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
        
        get paginatedWorkouts() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredWorkouts.slice(start, end);
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
        
        // Вспомогательные методы
        getStatusLabel(status) {
            const labels = {
                'completed': 'Завершена',
                'cancelled': 'Отменена',
                'planned': 'Запланирована'
            };
            return labels[status] || status;
        },
        
        // Сбор данных упражнений
        collectExerciseData() {
            const exercises = [];
            const exerciseElements = document.querySelectorAll('#selectedExercisesList > div[data-exercise-id]');
            
            exerciseElements.forEach(element => {
                const exerciseId = element.dataset.exerciseId;
                const exerciseName = element.querySelector('.font-medium').textContent;
                
                const sets = parseInt(element.querySelector(`input[name="sets_${exerciseId}"]`).value) || 0;
                const reps = parseInt(element.querySelector(`input[name="reps_${exerciseId}"]`).value) || 0;
                const weight = parseFloat(element.querySelector(`input[name="weight_${exerciseId}"]`).value) || 0;
                const rest = parseFloat(element.querySelector(`input[name="rest_${exerciseId}"]`).value) || 0;
                const tempo = element.querySelector(`input[name="tempo_${exerciseId}"]`).value || '';
                const notes = element.querySelector(`input[name="notes_${exerciseId}"]`).value || '';
                
                exercises.push({
                    exercise_id: parseInt(exerciseId),
                    name: exerciseName,
                    sets: sets,
                    reps: reps,
                    weight: weight,
                    rest_minutes: rest,
                    tempo: tempo,
                    notes: notes
                });
            });
            
            return exercises;
        },
        
        // Сохранение
        async saveWorkout() {
            try {
                // Собираем данные упражнений
                const exercises = this.collectExerciseData();
                console.log('Собранные данные упражнений:', exercises);
                
                const workoutData = {
                    title: this.formTitle,
                    description: this.formDescription,
                    athlete_id: this.formAthleteId,
                    date: this.formDate,
                    duration: this.formDuration,
                    status: this.formStatus,
                    exercises: exercises
                };
                
                const url = this.currentWorkout && this.currentWorkout.id ? 
                    `/workouts/${this.currentWorkout.id}` : '/workouts';
                const method = this.currentWorkout && this.currentWorkout.id ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(workoutData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentWorkout && this.currentWorkout.id ? 'Тренировка обновлена' : 'Тренировка создана',
                            message: this.currentWorkout && this.currentWorkout.id ? 
                                'Тренировка успешно обновлена' : 
                                'Тренировка успешно добавлена в календарь'
                        }
                    }));
                    
                    // Обновляем список тренировок
                    if (this.currentWorkout && this.currentWorkout.id) {
                        // Редактирование - обновляем существующую
                        const index = this.workouts.findIndex(w => w.id === this.currentWorkout.id);
                        if (index !== -1) {
                            this.workouts[index] = { ...this.currentWorkout, ...workoutData };
                        }
                    } else {
                        // Создание - добавляем новую
                        this.workouts.unshift(result.workout);
                    }
                    
                    // Переключаемся на список
                    this.showList();
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении тренировки'
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
                        message: 'Произошла ошибка при сохранении тренировки'
                    }
                }));
            }
        },
        
        // Удаление
        deleteWorkout(id) {
            const workout = this.workouts.find(w => w.id === id);
            const workoutTitle = workout ? workout.title : 'тренировку';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: 'Удалить тренировку',
                    message: `Вы уверены, что хотите удалить тренировку "${workoutTitle}"?`,
                    confirmText: 'Удалить',
                    cancelText: 'Отмена',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/workouts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: 'Тренировка удалена',
                            message: 'Тренировка успешно удалена из календаря'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.workouts = this.workouts.filter(w => w.id !== id);
                    
                    // Если удалили все тренировки на текущей странице, переходим на предыдущую
                    if (this.paginatedWorkouts.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении тренировки'
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
                        message: 'Произошла ошибка при удалении тренировки'
                    }
                }));
            }
        }
    }
}
</script>

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.exercises.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <a href="{{ route("crm.progress.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.exercises.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Каталог упражнений
    </a>
    <a href="{{ route("crm.progress.index") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    @if(auth()->user()->hasRole('trainer'))
        <a href="{{ route("crm.trainer.athletes") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Клиенты
        </a>
    @else
        <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
            </svg>
            Дневник питания
        </a>
    @endif
    <a href="#" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("header-actions")
    <!-- Кнопка добавления перенесена в строку с фильтрами -->
@endsection

@section("content")
<div x-data="workoutApp()" x-cloak class="space-y-6">
    
    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                    .filters-row .status-container {
                        width: 200px !important;
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
                           placeholder="Поиск тренировок..." 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр статуса -->
                <div class="status-container">
                    <select x-model="status" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все статусы</option>
                        <option value="planned">Запланирована</option>
                        <option value="completed">Завершена</option>
                        <option value="cancelled">Отменена</option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    @if(auth()->user()->hasRole('trainer'))
                        <button @click="showCreate()" 
                                class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                            Добавить тренировку
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Активные фильтры -->
        <div x-show="search || status" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">Активные фильтры:</span>
                
                <span x-show="search" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Поиск: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
                
                <span x-show="status" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Статус: <span x-text="getStatusLabel(status)"></span>
                    <button @click="status = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
            </div>
        </div>
    </div>

    <!-- СПИСОК ТРЕНИРОВОК -->
    <div x-show="currentView === 'list'" class="space-y-4">
        <template x-for="workout in paginatedWorkouts" :key="workout.id">
            <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200 overflow-hidden">
                <!-- Статус индикатор -->
                <div class="absolute top-0 left-0 w-full h-1" 
                     :class="{
                         'bg-green-500': workout.status === 'completed',
                         'bg-red-500': workout.status === 'cancelled',
                         'bg-blue-500': workout.status === 'planned'
                     }">
                </div>
                
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <!-- Аватарка спортсмена -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-semibold text-lg">
                                <span x-text="(workout.athlete?.name || workout.trainer?.name || '?').charAt(0).toUpperCase()"></span>
                            </div>
                        </div>
                        
                        <!-- Статус -->
                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                              :class="{
                                  'bg-green-100 text-green-800': workout.status === 'completed',
                                  'bg-red-100 text-red-800': workout.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': workout.status === 'planned'
                              }"
                              x-text="getStatusLabel(workout.status)">
                        </span>
                    </div>
                    
                    <!-- Заголовок и описание -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors" x-text="workout.title"></h3>
                        <p class="text-gray-600 text-sm line-clamp-2" x-text="workout.description || ''"></p>
                    </div>
                    
                    <!-- Мета информация -->
                    <div class="space-y-2 mb-4">
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Дата:</span>
                            <span x-text="new Date(workout.date).toLocaleDateString('ru-RU')"></span>
                        </div>
                        
                        <div class="text-sm text-gray-500" x-show="workout.duration">
                            <span class="font-medium text-gray-700">Продолжительность:</span>
                            <span x-text="workout.duration + ' мин'"></span>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">Участник:</span>
                            <span x-text="workout.athlete?.name || workout.trainer?.name || 'Неизвестно'"></span>
                        </div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="flex space-x-2">
                        <button @click="showView(workout.id)" 
                                class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            Просмотр
                        </button>
                        @if(auth()->user()->hasRole('trainer'))
                            <button @click="showEdit(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button @click="deleteWorkout(workout.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Пагинация -->
        <div x-show="filteredWorkouts.length > 0 && totalPages > 1" class="mt-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-center">
                    <!-- Навигация -->
                    <div class="flex items-center space-x-2">
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
        </div>
        
        <!-- Пустое состояние -->
        <div x-show="filteredWorkouts.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">💪</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет тренировок</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">У вас пока нет запланированных тренировок. Создайте первую тренировку для начала работы.</p>
            @if(auth()->user()->hasRole('trainer'))
                <button @click="showCreate()" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Создать первую тренировку
                </button>
            @endif
        </div>
    </div>

    <!-- СОЗДАНИЕ/РЕДАКТИРОВАНИЕ ТРЕНИРОВКИ -->
    <div x-show="currentView === 'create' || currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
                <span x-text="currentWorkout?.id ? 'Редактировать тренировку' : 'Создать тренировку'"></span>
            </h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Назад
            </button>
        </div>
        
        <form @submit.prevent="saveWorkout()" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Основная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Название тренировки *
                        </label>
                        <input type="text" 
                               x-model="formTitle"
                               class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Например: Силовая тренировка"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Спортсмен *
                        </label>
                        <select x-model="formAthleteId" 
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                required>
                            <option value="">Выберите спортсмена</option>
                            @foreach($athletes ?? [] as $athlete)
                                <option value="{{ $athlete->id }}">{{ $athlete->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Дата тренировки *
                        </label>
                        <input type="date" 
                               x-model="formDate"
                               class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Продолжительность (минуты)
                        </label>
                        <input type="number" 
                               x-model="formDuration"
                               class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="60"
                               min="1">
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Описание тренировки
                        </label>
                        <textarea x-model="formDescription"
                                  rows="6"
                                  class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                  placeholder="Опишите план тренировки, упражнения, цели..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Статус
                        </label>
                        <select x-model="formStatus" 
                                class="block w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="planned">Запланирована</option>
                            <option value="completed">Завершена</option>
                            <option value="cancelled">Отменена</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Секция упражнений -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Упражнения</h3>
                    <div class="flex gap-3">
                        <button type="button" onclick="openExerciseModal()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Добавить упражнение
                        </button>
                        <button type="button" onclick="openTemplateModal()" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Добавить шаблон
                        </button>
                    </div>
                </div>
                
                <!-- Выбранные упражнения -->
                <div id="selectedExercisesContainer" class="space-y-3" style="display: none;">
                    <h4 class="text-sm font-medium text-gray-700">Выбранные упражнения:</h4>
                    <div id="selectedExercisesList" class="space-y-2">
                        <!-- Здесь будут отображаться выбранные упражнения -->
                    </div>
                </div>
                
                <!-- Пустое состояние -->
                <div id="emptyExercisesState" class="text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                    <p>Добавьте упражнения или выберите шаблон тренировки</p>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <button type="button" 
                        @click="showList()" 
                        class="inline-flex items-center px-6 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Отмена
                </button>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <span x-text="currentWorkout?.id ? 'Обновить' : 'Создать'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- ПРОСМОТР ТРЕНИРОВКИ -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Просмотр тренировки</h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Назад
            </button>
        </div>
        
        <div x-show="currentWorkout" class="space-y-6">
            <!-- Заголовок и статус -->
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-2" x-text="currentWorkout?.title"></h4>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold"
                              :class="{
                                  'bg-green-100 text-green-800': currentWorkout?.status === 'completed',
                                  'bg-red-100 text-red-800': currentWorkout?.status === 'cancelled',
                                  'bg-blue-100 text-blue-800': currentWorkout?.status === 'planned'
                              }"
                              x-text="getStatusLabel(currentWorkout?.status)">
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Описание -->
            <div class="prose max-w-none" x-show="currentWorkout?.description">
                <h5 class="text-lg font-semibold text-gray-900 mb-3">Описание</h5>
                <p class="text-gray-600 whitespace-pre-line" x-text="currentWorkout?.description"></p>
            </div>
            
            <!-- Детали -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Дата</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout ? new Date(currentWorkout.date).toLocaleDateString('ru-RU') : ''"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4" x-show="currentWorkout?.duration">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Продолжительность</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.duration + ' мин'"></p>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">Участник</span>
                    </div>
                    <p class="text-lg font-semibold text-gray-900" x-text="currentWorkout?.athlete?.name || currentWorkout?.trainer?.name || 'Неизвестно'"></p>
                </div>
            </div>
            
            <!-- Действия -->
            @if(auth()->user()->hasRole('trainer'))
                <div class="flex space-x-2 pt-6 border-t border-gray-200">
                    <button @click="showEdit(currentWorkout?.id)" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        Редактировать
                    </button>
                    
                    <button @click="deleteWorkout(currentWorkout?.id)" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                        Удалить
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Красивое модальное окно для упражнений -->
<div id="exerciseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Выбор упражнений</h3>
            <button onclick="closeExerciseModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <!-- Поиск и фильтры -->
            <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center;">
                <!-- Поиск -->
                <input type="text" 
                       id="exercise-search" 
                       placeholder="Поиск упражнений..." 
                       style="flex: 1; min-width: 200px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;"
                       onkeyup="filterExercises()"
                       onfocus="this.style.borderColor = '#4f46e5'"
                       onblur="this.style.borderColor = '#d1d5db'">
                
                <!-- Фильтр категории -->
                <select id="category-filter" 
                        onchange="filterExercises()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все категории</option>
                    <option value="Грудь">Грудь</option>
                    <option value="Спина">Спина</option>
                    <option value="Ноги">Ноги</option>
                    <option value="Плечи">Плечи</option>
                    <option value="Руки">Руки</option>
                    <option value="Кардио">Кардио</option>
                    <option value="Гибкость">Гибкость</option>
                </select>
                
                <!-- Фильтр оборудования -->
                <select id="equipment-filter" 
                        onchange="filterExercises()"
                        style="min-width: 150px; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; background: white; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor = '#4f46e5'"
                        onblur="this.style.borderColor = '#d1d5db'">
                    <option value="">Все оборудование</option>
                    <option value="Штанга">Штанга</option>
                    <option value="Гантели">Гантели</option>
                    <option value="Собственный вес">Собственный вес</option>
                    <option value="Тренажеры">Тренажеры</option>
                    <option value="Скакалка">Скакалка</option>
                    <option value="Турник">Турник</option>
                </select>
            </div>
            
            <div id="exercises-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                <p style="color: black;">Загрузка упражнений...</p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">🔍</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">Упражнения не найдены</h3>
                <p style="font-size: 14px;">Попробуйте изменить параметры поиска</p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeExerciseModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Отмена</button>
            <button onclick="addSelectedExercises()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;">Готово</button>
        </div>
    </div>
</div>

<!-- Красивое модальное окно для шаблонов -->
<div id="templateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-width: 80%; max-height: 80%; width: 100%; overflow: hidden;">
        <!-- Заголовок -->
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 18px; font-weight: 600; color: #111827; margin: 0;">Выбор шаблона тренировки</h3>
            <button onclick="closeTemplateModal()" style="color: #6b7280; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">×</button>
        </div>
        
        <!-- Содержимое -->
        <div style="padding: 20px; max-height: 60vh; overflow-y: auto;">
            <div id="templates-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                <p style="color: black;">Загрузка шаблонов...</p>
            </div>
            
            <!-- Сообщение о пустых результатах -->
            <div id="no-templates-results" style="display: none; text-align: center; padding: 40px; color: #6b7280;">
                <div style="font-size: 48px; margin-bottom: 16px;">📋</div>
                <h3 style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">Шаблоны не найдены</h3>
                <p style="font-size: 14px;">Создайте шаблон тренировки для быстрого планирования</p>
            </div>
        </div>
        
        <!-- Кнопки -->
        <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 12px;">
            <button onclick="closeTemplateModal()" style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; cursor: pointer;">Отмена</button>
            <button onclick="addSelectedTemplate()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer;">Готово</button>
        </div>
    </div>
</div>

<script>
// Глобальные переменные для модальных окон
let exercises = [];
let templates = [];
let selectedTemplate = null;

// Функции для работы с модальными окнами
function openExerciseModal() {
    document.getElementById('exerciseModal').style.display = 'block';
    loadExercises();
}

function closeExerciseModal() {
    document.getElementById('exerciseModal').style.display = 'none';
}

function openTemplateModal() {
    document.getElementById('templateModal').style.display = 'block';
    loadTemplates();
}

function closeTemplateModal() {
    document.getElementById('templateModal').style.display = 'none';
}

// Загрузка упражнений
async function loadExercises() {
    try {
        const response = await fetch('/api/exercises');
        const data = await response.json();
        if (data.success) {
            exercises = data.exercises;
            renderExercises();
        }
    } catch (error) {
        console.error('Ошибка загрузки упражнений:', error);
    }
}

// Загрузка шаблонов
async function loadTemplates() {
    try {
        const response = await fetch('/api/workout-templates');
        const data = await response.json();
        if (data.success) {
            templates = data.templates;
            renderTemplates();
        }
    } catch (error) {
        console.error('Ошибка загрузки шаблонов:', error);
    }
}

// Отображение упражнений
function renderExercises() {
    const container = document.getElementById('exercises-container');
    if (exercises.length === 0) {
        container.innerHTML = '<p style="color: black;">Упражнения не найдены</p>';
        return;
    }
    
    container.innerHTML = exercises.map(exercise => `
        <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s;" 
             onclick="toggleExercise(this, ${exercise.id}, '${exercise.name}', '${exercise.category}', '${exercise.equipment}')">
            <h4 style="font-weight: 500; color: #111827; margin-bottom: 8px;">${exercise.name}</h4>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">${exercise.category}</p>
            <p style="font-size: 14px; color: #9ca3af;">${exercise.equipment}</p>
        </div>
    `).join('');
}

// Отображение шаблонов
function renderTemplates() {
    const container = document.getElementById('templates-container');
    if (templates.length === 0) {
        container.innerHTML = '<p style="color: black;">Шаблоны не найдены</p>';
        return;
    }
    
    container.innerHTML = templates.map(template => `
        <div class="template-item" data-template-id="${template.id}" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s;" onclick="toggleTemplate(this, ${template.id}, '${template.name}', ${JSON.stringify(template.exercises || []).replace(/"/g, '&quot;')})">
            <h4 style="font-weight: 500; color: #111827; margin-bottom: 8px;">${template.name}</h4>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 4px;">${template.exercises ? template.exercises.length : 0} упражнений</p>
            <p style="font-size: 14px; color: #9ca3af;">${template.description || ''}</p>
        </div>
    `).join('');
}

// Переключение упражнения
function toggleExercise(element, id, name, category, equipment) {
    const isSelected = element.style.backgroundColor === 'rgb(239, 246, 255)';
    
    if (isSelected) {
        element.style.backgroundColor = 'white';
        element.style.borderColor = '#e5e7eb';
    } else {
        element.style.backgroundColor = 'rgb(239, 246, 255)';
        element.style.borderColor = 'rgb(147, 197, 253)';
    }
    
    // Сохраняем данные в data-атрибуты
    element.dataset.selected = !isSelected;
    element.dataset.exerciseId = id;
    element.dataset.exerciseName = name;
    element.dataset.exerciseCategory = category;
    element.dataset.exerciseEquipment = equipment;
}

// Переключение шаблона
function toggleTemplate(element, id, name, exercises) {
    // Убираем выделение с других элементов
    document.querySelectorAll('.template-item').forEach(el => {
        el.style.backgroundColor = 'white';
        el.style.borderColor = '#e5e7eb';
    });
    
    // Выделяем текущий элемент
    element.style.backgroundColor = 'rgb(239, 246, 255)';
    element.style.borderColor = 'rgb(147, 197, 253)';
    
    // Сохраняем выбранный шаблон
    selectedTemplate = {
        id: id,
        name: name,
        exercises: exercises
    };
}

// Фильтрация упражнений
function filterExercises() {
    const searchTerm = document.getElementById('exercise-search').value.toLowerCase();
    const categoryFilter = document.getElementById('category-filter').value.toLowerCase();
    const equipmentFilter = document.getElementById('equipment-filter').value.toLowerCase();

    const exerciseElements = document.querySelectorAll('#exercises-container > div');
    const noResults = document.getElementById('no-results');
    let visibleCount = 0;

    exerciseElements.forEach(element => {
        const name = element.querySelector('h4').textContent.toLowerCase();
        const category = element.querySelector('p').textContent.toLowerCase();
        const equipment = element.querySelectorAll('p')[1].textContent.toLowerCase();

        const matchesSearch = name.includes(searchTerm);
        const matchesCategory = !categoryFilter || category.includes(categoryFilter);
        const matchesEquipment = !equipmentFilter || equipment.includes(equipmentFilter);

        if (matchesSearch && matchesCategory && matchesEquipment) {
            element.style.display = 'block';
            visibleCount++;
        } else {
            element.style.display = 'none';
        }
    });

    // Показываем/скрываем сообщение о пустых результатах
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// Добавление выбранных упражнений
function addSelectedExercises() {
    const selectedElements = document.querySelectorAll('#exerciseModal [data-selected="true"]');
    const selectedExercises = Array.from(selectedElements).map(el => ({
        id: parseInt(el.dataset.exerciseId),
        name: el.dataset.exerciseName,
        category: el.dataset.exerciseCategory,
        equipment: el.dataset.exerciseEquipment
    }));
    
    console.log('Выбрано упражнений:', selectedExercises.length);
    console.log('Упражнения:', selectedExercises);
    
    // Отображаем выбранные упражнения в форме
    displaySelectedExercises(selectedExercises);
    
    closeExerciseModal();
}

// Отображение выбранных упражнений в форме
function displaySelectedExercises(exercises) {
    const container = document.getElementById('selectedExercisesContainer');
    const list = document.getElementById('selectedExercisesList');
    const emptyState = document.getElementById('emptyExercisesState');
    
    // Принудительно устанавливаем 4 колонки на больших экранах
    setTimeout(() => {
        const grids = document.querySelectorAll('.exercise-params-grid');
        grids.forEach(grid => {
            if (window.innerWidth >= 768) {
                grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
            }
        });
    }, 100);
    
    if (exercises.length > 0) {
        // Скрываем пустое состояние
        emptyState.style.display = 'none';
        
        // Показываем контейнер с упражнениями
        container.style.display = 'block';
        
        // Отображаем упражнения
        list.innerHTML = exercises.map((exercise, index) => `
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg" data-exercise-id="${exercise.id}">
                <!-- Заголовок упражнения -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-indigo-600 font-medium">${index + 1}.</span>
                        <span class="font-medium text-gray-900">${exercise.name}</span>
                        <span class="text-sm text-gray-600">(${exercise.category} • ${exercise.equipment})</span>
                    </div>
                    <button type="button" onclick="removeExercise(${exercise.id})" class="text-red-600 hover:text-red-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Параметры упражнения - современный дизайн -->
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                    <div class="exercise-params-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <!-- Подходы -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Подходы (раз)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="sets_${exercise.id}" 
                                       min="1" 
                                       max="20" 
                                       value="3"
                                       class="w-full px-4 py-3 text-lg font-semibold text-center bg-gradient-to-r from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all duration-200 hover:border-indigo-300">
                            </div>
                        </div>
                        
                        <!-- Повторения -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Повторения (раз)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="reps_${exercise.id}" 
                                       min="1" 
                                       max="100" 
                                       value="10"
                                       class="w-full px-4 py-3 text-lg font-semibold text-center bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl focus:ring-4 focus:ring-green-100 focus:border-green-400 transition-all duration-200 hover:border-green-300">
                            </div>
                        </div>
                        
                        <!-- Вес -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Вес (кг)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="weight_${exercise.id}" 
                                       min="0" 
                                       max="1000" 
                                       step="0.5"
                                       value="0"
                                       class="w-full px-4 py-3 text-lg font-semibold text-center bg-gradient-to-r from-orange-50 to-red-50 border-2 border-orange-200 rounded-xl focus:ring-4 focus:ring-orange-100 focus:border-orange-400 transition-all duration-200 hover:border-orange-300">
                            </div>
                        </div>
                        
                        <!-- Отдых -->
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Отдых (мин)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       name="rest_${exercise.id}" 
                                       min="0" 
                                       max="60" 
                                       step="0.5"
                                       value="2"
                                       class="w-full px-4 py-3 text-lg font-semibold text-center bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl focus:ring-4 focus:ring-purple-100 focus:border-purple-400 transition-all duration-200 hover:border-purple-300">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Темп -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Темп
                            </label>
                            <input type="text" 
                                   name="tempo_${exercise.id}" 
                                   placeholder="2-1-2 (опускание-пауза-подъем)"
                                   class="w-full px-4 py-3 text-sm bg-gradient-to-r from-blue-50 to-cyan-50 border-2 border-blue-200 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all duration-200 hover:border-blue-300 placeholder-gray-500">
                        </div>
                        
                        <!-- Примечания -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Примечания
                            </label>
                            <input type="text" 
                                   name="notes_${exercise.id}" 
                                   placeholder="Дополнительные заметки..."
                                   class="w-full px-4 py-3 text-sm bg-gradient-to-r from-gray-50 to-slate-50 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-gray-100 focus:border-gray-400 transition-all duration-200 hover:border-gray-300 placeholder-gray-500">
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    } else {
        // Показываем пустое состояние
        emptyState.style.display = 'block';
        container.style.display = 'none';
    }
}

// Удаление упражнения из списка
function removeExercise(exerciseId) {
    // Находим элемент с упражнением и удаляем его
    const exerciseElement = document.querySelector(`[data-exercise-id="${exerciseId}"]`);
    if (exerciseElement) {
        exerciseElement.remove();
    }
    
    // Обновляем отображение
    const remainingExercises = Array.from(document.querySelectorAll('#selectedExercisesList > div')).map(el => {
        const name = el.querySelector('.font-medium').textContent;
        const category = el.querySelector('.text-gray-600').textContent.match(/\(([^•]+)/)[1].trim();
        const equipment = el.querySelector('.text-gray-600').textContent.match(/• ([^)]+)/)[1].trim();
        return { id: exerciseId, name, category, equipment };
    });
    
    displaySelectedExercises(remainingExercises);
}

// Добавление выбранного шаблона
function addSelectedTemplate() {
    if (selectedTemplate) {
        console.log('Выбран шаблон:', selectedTemplate);
        console.log('Упражнения в шаблоне:', selectedTemplate.exercises);
        
        // Отображаем упражнения из шаблона
        displaySelectedExercises(selectedTemplate.exercises);
    }
    closeTemplateModal();
}

// Простые функции для модальных окон
document.addEventListener('DOMContentLoaded', function() {
    // Закрытие по клику на фон
    document.getElementById('exerciseModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeExerciseModal();
        }
    });
    
    document.getElementById('templateModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTemplateModal();
        }
    });
    
    // Принудительно устанавливаем 4 колонки на больших экранах
    function setGridColumns() {
        const grids = document.querySelectorAll('.exercise-params-grid');
        grids.forEach(grid => {
            if (window.innerWidth >= 768) {
                grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
            } else {
                grid.style.gridTemplateColumns = 'repeat(2, 1fr)';
            }
        });
    }
    
    // Устанавливаем при загрузке
    setGridColumns();
    
    // Устанавливаем при изменении размера окна
    window.addEventListener('resize', setGridColumns);
});
</script>

@endsection

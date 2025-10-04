@extends("crm.layouts.app")

@section("title", "Настройки спортсмена")
@section("page-title", "Настройки")

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route("crm.athlete.exercises") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
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
    <a href="{{ route('crm.athlete.settings') }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
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
    <a href="{{ route("crm.athlete.exercises") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
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
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link active">
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
    <a href="{{ route("crm.athlete.exercises") }}" class="mobile-nav-link">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        Упражнения
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
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

<script>
// Функциональность для настроек спортсмена
function athleteSettingsApp() {
    return {
        isEditing: false,
        isSaving: false,
        
        // Переключение режима редактирования
        toggleEdit() {
            this.isEditing = !this.isEditing;
        },
        
        // Сохранение профиля через AJAX
        async saveProfile(event) {
            if (this.isSaving) return;
            
            this.isSaving = true;
            
            try {
                const formData = new FormData(event.target);
                
                const response = await fetch('{{ route("crm.athlete.profile.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showSuccess('Профиль обновлен!', 'Ваши данные успешно сохранены');
                    this.isEditing = false;
                    // Обновляем данные на странице без перезагрузки
                    this.updateProfileData(formData);
                } else {
                    showError('Ошибка', result.message || 'Не удалось сохранить профиль');
                }
            } catch (error) {
                console.error('Ошибка при сохранении:', error);
                showError('Ошибка', 'Произошла ошибка при сохранении профиля');
            } finally {
                this.isSaving = false;
            }
        },
        
        // Обновление данных профиля на странице
        updateProfileData(formData) {
            // Обновляем поля в режиме просмотра
            const nameValue = formData.get('name');
            const emailValue = formData.get('email');
            const phoneValue = formData.get('phone');
            const ageValue = formData.get('age');
            const genderValue = formData.get('gender');
            const heightValue = formData.get('height');
            const birthDateValue = formData.get('birth_date');
            const sportLevelValue = formData.get('sport_level');
            const goalsValues = formData.getAll('goals[]');
            
            // Обновляем отображаемые значения
            this.updateDisplayValue('name', nameValue);
            this.updateDisplayValue('email', emailValue);
            this.updateDisplayValue('phone', phoneValue || 'Не указан');
            this.updateDisplayValue('age', ageValue ? ageValue + ' лет' : 'Не указан');
            this.updateDisplayValue('gender', this.getGenderText(genderValue));
            this.updateDisplayValue('height', heightValue ? heightValue + ' см' : 'Не указан');
            this.updateDisplayValue('birth_date', birthDateValue ? this.formatDate(birthDateValue) : 'Не указана');
            this.updateDisplayValue('sport_level', this.getSportLevelText(sportLevelValue));
            this.updateDisplayValue('goals', this.getGoalsText(goalsValues));
        },
        
        // Вспомогательные функции для обновления отображения
        updateDisplayValue(fieldName, value) {
            const elements = document.querySelectorAll(`[data-field="${fieldName}"]`);
            elements.forEach(el => {
                if (el.tagName === 'DIV') {
                    el.textContent = value;
                }
            });
        },
        
        getGenderText(gender) {
            const genderMap = {
                'male': 'Мужской',
                'female': 'Женский'
            };
            return genderMap[gender] || 'Не указан';
        },
        
        getSportLevelText(level) {
            const levelMap = {
                'beginner': 'Начинающий',
                'intermediate': 'Средний',
                'advanced': 'Продвинутый'
            };
            return levelMap[level] || 'Не указан';
        },
        
        getGoalsText(goals) {
            if (!goals || goals.length === 0) return 'Не указаны';
            
            const goalMap = {
                'weight_loss': 'Похудение',
                'muscle_gain': 'Набор мышечной массы',
                'muscle_tone': 'Тонизация мышц',
                'endurance': 'Выносливость',
                'strength': 'Сила',
                'flexibility': 'Гибкость'
            };
            
            return goals.map(goal => goalMap[goal] || goal).join(', ');
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('ru-RU');
        }
    }
}
</script>

@section("content")
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="athleteSettingsApp()" x-cloak class="space-y-6">

    <!-- Профиль спортсмена -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">{{ __('common.profile') }}</h2>
        
        <!-- Режим просмотра -->
        <div x-show="!isEditing">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.name') }}</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="name">
                        {{ $athlete->name }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.email') }}</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="email">
                        {{ $athlete->email }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.phone') }}</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="phone">
                        {{ $athlete->phone ?? 'Не указан' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.age') }}</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="age">
                        {{ $athlete->age ?? 'Не указан' }} лет
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="gender">
                        @if($athlete->gender === 'male')
                            Мужской
                        @elseif($athlete->gender === 'female')
                            Женский
                        @else
                            Не указан
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('common.height') }}</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="height">
                        {{ $athlete->height ? $athlete->height . ' см' : 'Не указан' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="birth_date">
                        {{ $athlete->birth_date ? $athlete->birth_date->format('d.m.Y') : 'Не указана' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Уровень подготовки</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="sport_level">
                        @if($athlete->sport_level === 'beginner')
                            Начинающий
                        @elseif($athlete->sport_level === 'intermediate')
                            Средний
                        @elseif($athlete->sport_level === 'advanced')
                            Продвинутый
                        @else
                            Не указан
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Цели</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900" data-field="goals">
                        @if($athlete->goals && count($athlete->goals) > 0)
                            {{ implode(', ', array_map(function($goal) {
                                return match($goal) {
                                    'weight_loss' => 'Похудение',
                                    'muscle_gain' => 'Набор мышечной массы',
                                    'muscle_tone' => 'Тонизация мышц',
                                    'endurance' => 'Выносливость',
                                    'strength' => 'Сила',
                                    'flexibility' => 'Гибкость',
                                    default => $goal
                                };
                            }, $athlete->goals)) }}
                        @else
                            Не указаны
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Кнопка редактирования -->
            <div class="mt-6 flex justify-end">
                <button @click="toggleEdit()" 
                        x-text="isEditing ? 'Отмена' : 'Редактировать профиль'"
                        :class="isEditing ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-600 hover:bg-blue-700'"
                        class="px-4 py-2 text-white rounded-lg transition-colors">
                </button>
            </div>
        </div>

        <!-- Режим редактирования -->
        <div x-show="isEditing">
            <form @submit.prevent="saveProfile" class="space-y-6">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px;">
                    <!-- Имя -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $athlete->name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $athlete->email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Телефон -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $athlete->phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="+7 (999) 123-45-67">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Возраст -->
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Возраст</label>
                        <input type="number" 
                               id="age" 
                               name="age" 
                               value="{{ old('age', $athlete->age) }}"
                               min="1" 
                               max="120"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('age') border-red-500 @enderror"
                               placeholder="25">
                        @error('age')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Пол -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                        <select id="gender" 
                                name="gender"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                            <option value="">Выберите пол</option>
                            <option value="male" {{ old('gender', $athlete->gender) === 'male' ? 'selected' : '' }}>Мужской</option>
                            <option value="female" {{ old('gender', $athlete->gender) === 'female' ? 'selected' : '' }}>Женский</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Рост -->
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                        <input type="number" 
                               id="height" 
                               name="height" 
                               value="{{ old('height', $athlete->height) }}"
                               min="50" 
                               max="250"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('height') border-red-500 @enderror"
                               placeholder="175">
                        @error('height')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Дата рождения -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                        <input type="date" 
                               id="birth_date" 
                               name="birth_date" 
                               value="{{ old('birth_date', $athlete->birth_date ? $athlete->birth_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Уровень подготовки -->
                    <div>
                        <label for="sport_level" class="block text-sm font-medium text-gray-700 mb-2">Уровень подготовки</label>
                        <select id="sport_level" 
                                name="sport_level"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sport_level') border-red-500 @enderror">
                            <option value="">Выберите уровень</option>
                            <option value="beginner" {{ old('sport_level', $athlete->sport_level) === 'beginner' ? 'selected' : '' }}>Начинающий</option>
                            <option value="intermediate" {{ old('sport_level', $athlete->sport_level) === 'intermediate' ? 'selected' : '' }}>Средний</option>
                            <option value="advanced" {{ old('sport_level', $athlete->sport_level) === 'advanced' ? 'selected' : '' }}>Продвинутый</option>
                        </select>
                        @error('sport_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Цели -->
                <div style="grid-column: 1 / -1;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Цели тренировок</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                        @php
                            $goals = old('goals', $athlete->goals ?? []);
                        @endphp
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="weight_loss" 
                                   {{ in_array('weight_loss', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.weight_loss') }}</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="muscle_gain" 
                                   {{ in_array('muscle_gain', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.muscle_gain') }}</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="muscle_tone" 
                                   {{ in_array('muscle_tone', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.muscle_tone') }}</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="endurance" 
                                   {{ in_array('endurance', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.endurance') }}</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="strength" 
                                   {{ in_array('strength', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.strength') }}</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; padding: 12px; background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s; hover:border-blue-300 hover:bg-blue-50;">
                            <input type="checkbox" 
                                   name="goals[]" 
                                   value="flexibility" 
                                   {{ in_array('flexibility', $goals) ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; margin-right: 12px; accent-color: #3b82f6;">
                            <span style="font-size: 14px; color: #374151; font-weight: 500;">{{ __('common.flexibility') }}</span>
                        </label>
                    </div>
                    @error('goals')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="toggleEdit()" 
                            :disabled="isSaving"
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Отмена
                    </button>
                    <button type="submit" 
                            :disabled="isSaving"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSaving">Сохранить изменения</span>
                        <span x-show="isSaving" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Сохранение...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Настройки языка и валюты -->
    <div x-show="!isEditing" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('common.language') }} и {{ __('common.currency') }}</h2>
        
        
        <div class="space-y-6">
            
            
            <!-- Скрытые поля для значений -->
            <input type="hidden" id="language_code_hidden" name="language_code" value="{{ $athlete->language_code ?? 'ru' }}">
            <input type="hidden" id="currency_code_hidden" name="currency_code" value="{{ $athlete->currency_code ?? 'RUB' }}">
            
            <!-- Язык -->
            <div class="space-y-4">
                <h3 class="text-base font-medium text-gray-900">
                    <i class="fas fa-language mr-2"></i>{{ __('common.language') }} интерфейса
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    @foreach(\App\Models\Language::getActive() as $language)
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ ($athlete->language_code ?? 'ru') === $language->code ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" onclick="updateLanguageCode('{{ $language->code }}')" style="flex: 1; min-width: 200px; max-width: 300px;">
                            <input type="radio" name="language_radio" value="{{ $language->code }}" 
                                   {{ ($athlete->language_code ?? 'ru') === $language->code ? 'checked' : '' }}
                                   class="sr-only" onchange="updateLanguageCode('{{ $language->code }}')">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">{{ $language->flag }}</span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $language->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $language->native_name }}</div>
                                </div>
                            </div>
                            @if(($athlete->language_code ?? 'ru') === $language->code)
                                <div class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-blue-500"></i>
                                </div>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Валюта -->
            <div class="space-y-4">
                <h3 class="text-base font-medium text-gray-900">
                    <i class="fas fa-dollar-sign mr-2"></i>{{ __('common.currency') }}
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                    @foreach(\App\Models\Currency::getActive() as $currency)
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 {{ ($athlete->currency_code ?? 'RUB') === $currency->code ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}" onclick="updateCurrencyCode('{{ $currency->code }}')" style="flex: 1; min-width: 200px; max-width: 300px;">
                            <input type="radio" name="currency_radio" value="{{ $currency->code }}" 
                                   {{ ($athlete->currency_code ?? 'RUB') === $currency->code ? 'checked' : '' }}
                                   class="sr-only" onchange="updateCurrencyCode('{{ $currency->code }}')">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg font-medium">{{ $currency->symbol }}</span>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $currency->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $currency->code }}</div>
                                </div>
                            </div>
                            @if(($athlete->currency_code ?? 'RUB') === $currency->code)
                                <div class="absolute top-2 right-2">
                                    <i class="fas fa-check-circle text-blue-500"></i>
                                </div>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        function updateLanguageCode(code) {
            document.getElementById('language_code_hidden').value = code;
            // Обновляем визуальное состояние
            document.querySelectorAll('label[onclick*="updateLanguageCode"]').forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-200');
            });
            event.target.closest('label').classList.remove('border-gray-200');
            event.target.closest('label').classList.add('border-blue-500', 'bg-blue-50');
            
            // Автоматически сохраняем настройки
            saveSettings('language');
        }

        function updateCurrencyCode(code) {
            document.getElementById('currency_code_hidden').value = code;
            // Обновляем визуальное состояние
            document.querySelectorAll('label[onclick*="updateCurrencyCode"]').forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-200');
            });
            event.target.closest('label').classList.remove('border-gray-200');
            event.target.closest('label').classList.add('border-blue-500', 'bg-blue-50');
            
            // Автоматически сохраняем настройки
            saveSettings('currency');
        }

        async function saveSettings(type) {
            const languageCode = document.getElementById('language_code_hidden').value;
            const currencyCode = document.getElementById('currency_code_hidden').value;
            
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'PUT');
                formData.append('language_code', languageCode);
                formData.append('currency_code', currencyCode);
                
                const response = await fetch('{{ route("crm.athlete.settings.update") }}', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    
                    // Показываем соответствующее уведомление
                    if (type === 'language') {
                        showSuccess('Язык обновлен', 'Язык интерфейса успешно изменен');
                        // Перезагружаем страницу только при смене языка
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else if (type === 'currency') {
                        showSuccess('Валюта обновлена', 'Валюта успешно изменена');
                        // При смене валюты не перезагружаем страницу
                    } else {
                        showSuccess('Настройки сохранены', 'Настройки успешно обновлены');
                        // Перезагружаем страницу через небольшую задержку
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                } else {
                    showError('Ошибка сохранения', 'Не удалось сохранить настройки');
                }
            } catch (error) {
                showError('Ошибка сохранения', 'Произошла ошибка при сохранении настроек');
            }
        }

    </script>


</div>
@endsection

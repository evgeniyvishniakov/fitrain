@extends("crm.layouts.app")

@section("title", "Прогресс спортсмена")
@section("page-title", "Прогресс")

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section("sidebar")
    <a href="{{ route("crm.dashboard.main") }}" class="nav-link {{ request()->routeIs('crm.dashboard.main') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2">
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
    <a href="{{ route("crm.workouts.index") }}" class="nav-link {{ request()->routeIs('crm.workouts.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="nav-link {{ request()->routeIs('crm.nutrition.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="nav-link {{ request()->routeIs('crm.athlete.settings*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("mobile-menu")
    <a href="{{ route("crm.dashboard.main") }}" class="mobile-nav-link {{ request()->routeIs('crm.dashboard.main') ? 'active' : '' }}">
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
    <a href="{{ route("crm.workouts.index") }}" class="mobile-nav-link {{ request()->routeIs('crm.workouts.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.athlete.progress") }}" class="mobile-nav-link active">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.nutrition.index") }}" class="mobile-nav-link {{ request()->routeIs('crm.nutrition.*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
        </svg>
        Дневник питания
    </a>
    <a href="{{ route('crm.athlete.settings') }}" class="mobile-nav-link {{ request()->routeIs('crm.athlete.settings*') ? 'active' : '' }}">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("content")
<div class="space-y-6 fade-in-up">
    
    <!-- Заголовок -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Мой прогресс</h1>
            <p class="text-gray-600 mt-1">Отслеживайте свои достижения и результаты тренировок</p>
        </div>
    </div>

    <!-- Статистика прогресса -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Всего тренировок</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentWorkouts->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Записей прогресса</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $progressData->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Измерений</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $measurements->count() }}</p>
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
                    <p class="text-sm font-medium text-gray-600">Последняя тренировка</p>
                    <p class="text-lg font-bold text-gray-900">
                        @if($recentWorkouts->count() > 0)
                            {{ $recentWorkouts->first()->created_at->format('d.m.Y') }}
                        @else
                            Нет данных
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
            <h3 class="text-lg font-semibold text-gray-900">Графики прогресса</h3>
        </div>
        <div class="p-6">
            <!-- Фильтры графиков -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Период:</label>
                        <select id="timeFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">Все измерения</option>
                            <option value="month">Последний месяц</option>
                            <option value="3months">Последние 3 месяца</option>
                            <option value="6months">Последние 6 месяцев</option>
                            <option value="year">Последний год</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Фильтр:</label>
                        <select id="chartFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">Все параметры</option>
                            <option value="weight">Вес и ИМТ</option>
                            <option value="body">Процент жира и мышечная масса</option>
                            <option value="volumes">Объемы тела</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- График веса -->
                <div id="weightChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Динамика веса (кг)</h4>
                    <div class="relative h-64">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>

                <!-- График ИМТ -->
                <div id="bmiChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Динамика ИМТ</h4>
                    <div class="relative h-64">
                        <canvas id="bmiChart"></canvas>
                    </div>
                </div>

                <!-- График процента жира -->
                <div id="bodyFatChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Процент жира (%)</h4>
                    <div class="relative h-64">
                        <canvas id="bodyFatChart"></canvas>
                    </div>
                </div>

                <!-- График мышечной массы -->
                <div id="muscleMassChartContainer" class="bg-white rounded-lg p-4 border border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Мышечная масса (кг)</h4>
                    <div class="relative h-64">
                        <canvas id="muscleMassChart"></canvas>
                    </div>
                </div>

                <!-- График объемов тела -->
                <div id="bodyVolumesChartContainer" class="bg-white rounded-lg p-4 border border-gray-200 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900">Объемы тела (см)</h4>
                        <select id="volumesFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="all">Все объемы</option>
                            <option value="chest">Грудь</option>
                            <option value="waist">Талия</option>
                            <option value="hips">Бедра</option>
                            <option value="bicep">Бицепс</option>
                            <option value="thigh">Бедро</option>
                            <option value="neck">Шея</option>
                        </select>
                    </div>
                    <div class="relative h-96">
                        <canvas id="bodyVolumesChart"></canvas>
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
});

// Функция для инициализации графиков
function initCharts() {
    console.log('=== ИНИЦИАЛИЗАЦИЯ ГРАФИКОВ ===');
    
    // Получаем данные измерений
    const measurements = @json($measurements->all());
    
    console.log('Количество измерений:', measurements.length);
    console.log('Измерения:', measurements);
    
    if (measurements.length === 0) {
        console.log('Нет измерений, пропускаем создание графиков');
        return;
    }
    
    // Проверяем, что Chart.js загружен
    if (typeof Chart === 'undefined') {
        console.error('Chart.js не загружен');
        return;
    }
    console.log('Chart.js загружен успешно');

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
        console.log('Нет валидных измерений для графиков');
        return;
    }
    
    // Подготавливаем метки для графиков
    const labels = validMeasurements.map(m => {
        const date = new Date(m.measurement_date);
        return date.toLocaleDateString('ru-RU', { month: 'short', day: 'numeric' });
    });

    console.log('Валидные измерения:', validMeasurements);
    console.log('Метки для графиков:', labels);

    // Проверяем наличие canvas элементов
    console.log('Canvas weightChart:', document.getElementById('weightChart'));
    console.log('Canvas bmiChart:', document.getElementById('bmiChart'));
    console.log('Canvas bodyFatChart:', document.getElementById('bodyFatChart'));
    console.log('Canvas muscleMassChart:', document.getElementById('muscleMassChart'));
    console.log('Canvas bodyVolumesChart:', document.getElementById('bodyVolumesChart'));

    // Создаем графики
    createWeightChart(labels, validMeasurements);
    createBMIChart(labels, validMeasurements);
    createBodyFatChart(labels, validMeasurements);
    createMuscleMassChart(labels, validMeasurements);
    createBodyVolumesChart(labels, validMeasurements);
    
    console.log('=== ГРАФИКИ СОЗДАНЫ ===');
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
    console.log('=== СОЗДАНИЕ ГРАФИКА ВЕСА ===');
    const ctx = document.getElementById('weightChart');
    if (!ctx) {
        console.error('Canvas элемент weightChart не найден');
        return;
    }

    const weightData = measurements.map(m => m.weight).filter(val => val !== null && val !== undefined);
    console.log('Данные веса:', weightData);

    try {
        window.weightChart = new Chart(ctx, {
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
        console.log('График веса создан успешно');
    } catch (error) {
        console.error('Ошибка создания графика веса:', error);
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
                    label: 'ИМТ',
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
                    label: 'Процент жира (%)',
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
                    label: 'Мышечная масса (кг)',
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
    console.log('=== СОЗДАНИЕ ГРАФИКА ОБЪЕМОВ ТЕЛА ===');
    const ctx = document.getElementById('bodyVolumesChart');
    if (!ctx) {
        console.error('Canvas элемент bodyVolumesChart не найден');
        return;
    }

    // Подготавливаем данные для каждого объема
    const chestData = measurements.map(m => m.chest).filter(val => val !== null && val !== undefined);
    const waistData = measurements.map(m => m.waist).filter(val => val !== null && val !== undefined);
    const hipsData = measurements.map(m => m.hips).filter(val => val !== null && val !== undefined);
    const bicepData = measurements.map(m => m.bicep).filter(val => val !== null && val !== undefined);
    const thighData = measurements.map(m => m.thigh).filter(val => val !== null && val !== undefined);
    const neckData = measurements.map(m => m.neck).filter(val => val !== null && val !== undefined);

    console.log('Данные объемов тела:', {
        chest: chestData,
        waist: waistData,
        hips: hipsData,
        bicep: bicepData,
        thigh: thighData,
        neck: neckData
    });

    try {
        window.bodyVolumesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Грудь (см)',
                        data: measurements.map(m => m.chest),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: chestData.length === 0
                    },
                    {
                        label: 'Талия (см)',
                        data: measurements.map(m => m.waist),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: waistData.length === 0
                    },
                    {
                        label: 'Бедра (см)',
                        data: measurements.map(m => m.hips),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: hipsData.length === 0
                    },
                    {
                        label: 'Бицепс (см)',
                        data: measurements.map(m => m.bicep),
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: bicepData.length === 0
                    },
                    {
                        label: 'Бедро (см)',
                        data: measurements.map(m => m.thigh),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4,
                        hidden: thighData.length === 0
                    },
                    {
                        label: 'Шея (см)',
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
                                return context.dataset.label + ': ' + (context.parsed.y || '—') + ' см';
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
                            text: 'Объем (см)'
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
        console.log('График объемов тела создан успешно');
    } catch (error) {
        console.error('Ошибка создания графика объемов тела:', error);
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
    }
    
    if (chartFilter) {
        chartFilter.addEventListener('change', function() {
            filterChartsByType(this.value);
        });
    }
    
    if (volumesFilter) {
        volumesFilter.addEventListener('change', function() {
            filterVolumesChart(this.value);
        });
    }
}

// Фильтрация графиков по времени
function filterChartsByTime(timeFilter) {
    console.log('Фильтрация по времени:', timeFilter);
    
    // Получаем все измерения
    const allMeasurements = @json($measurements->all());
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
    console.log('Фильтрация по типу:', chartFilter);
    
    // Скрываем/показываем контейнеры графиков
    const containers = {
        weight: document.getElementById('weightChartContainer'),
        bmi: document.getElementById('bmiChartContainer'),
        bodyFat: document.getElementById('bodyFatChartContainer'),
        muscleMass: document.getElementById('muscleMassChartContainer'),
        volumes: document.getElementById('bodyVolumesChartContainer')
    };
    
    // Сначала скрываем все
    Object.values(containers).forEach(container => {
        if (container) {
            container.style.display = 'none';
        }
    });
    
    // Показываем нужные в зависимости от фильтра
    switch (chartFilter) {
        case 'all':
            Object.values(containers).forEach(container => {
                if (container) {
                    container.style.display = 'block';
                }
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
    console.log('Пересоздание графиков с данными:', measurements);
    
    if (measurements.length === 0) {
        console.log('Нет данных для отображения');
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
        console.log('Нет валидных измерений для графиков');
        return;
    }
    
    // Подготавливаем метки для графиков
    const labels = validMeasurements.map(m => {
        const date = new Date(m.measurement_date);
        return date.toLocaleDateString('ru-RU', { month: 'short', day: 'numeric' });
    });
    
    // Создаем графики
    createWeightChart(labels, validMeasurements);
    createBMIChart(labels, validMeasurements);
    createBodyFatChart(labels, validMeasurements);
    createMuscleMassChart(labels, validMeasurements);
    createBodyVolumesChart(labels, validMeasurements);
    
    console.log('Графики пересозданы');
}

// Фильтрация графика объемов тела
function filterVolumesChart(volumesFilter) {
    console.log('Фильтрация объемов тела:', volumesFilter);
    
    if (!window.bodyVolumesChart) {
        console.log('График объемов тела не создан');
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
            hideAllExcept(datasets, ['Грудь (см)']);
            break;
        case 'waist':
            hideAllExcept(datasets, ['Талия (см)']);
            break;
        case 'hips':
            hideAllExcept(datasets, ['Бедра (см)']);
            break;
        case 'bicep':
            hideAllExcept(datasets, ['Бицепс (см)']);
            break;
        case 'thigh':
            hideAllExcept(datasets, ['Бедро (см)']);
            break;
        case 'neck':
            hideAllExcept(datasets, ['Шея (см)']);
            break;
    }
    
    // Обновляем график
    chart.update();
    console.log('График объемов тела обновлен');
}

// Вспомогательная функция для скрытия всех линий кроме указанных
function hideAllExcept(datasets, allowedLabels) {
    datasets.forEach(dataset => {
        dataset.hidden = !allowedLabels.includes(dataset.label);
    });
}
</script>
@endsection

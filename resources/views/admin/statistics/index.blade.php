@extends('admin.layouts.app')

@section('title', 'Статистика')
@section('page-title', 'Статистика и аналитика')

@section('content')
<div class="space-y-6">
    <!-- Общая статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Пользователи -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Всего пользователей</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">+{{ $stats['growth']['users_this_month'] }} за месяц</span>
            </div>
        </div>

        <!-- Тренеры -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Тренеры</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['trainers'] }}</p>
                </div>
            </div>
        </div>

        <!-- Спортсмены -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-running text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Спортсмены</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['athletes'] }}</p>
                </div>
            </div>
        </div>

        <!-- Тренировки -->
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dumbbell text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Тренировки</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['workouts']['total'] }}</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 text-sm font-medium">+{{ $stats['growth']['workouts_this_month'] }} за месяц</span>
            </div>
        </div>
    </div>

    <!-- Графики -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Рост пользователей -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Рост пользователей (12 месяцев)</h3>
            </div>
            <div class="h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Топ тренеров -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Топ тренеров по количеству спортсменов</h3>
            <div class="space-y-4">
                @forelse($topTrainers as $trainer)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $trainer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $trainer->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">{{ $trainer->athletes_count }}</p>
                            <p class="text-xs text-gray-500">спортсменов</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Нет данных</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Действия -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Экспорт данных</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.statistics.export', 'users') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Экспорт пользователей
            </a>
            <a href="{{ route('admin.statistics.export', 'workouts') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Экспорт тренировок
            </a>
            <a href="{{ route('admin.statistics.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-chart-line mr-2"></i>Детальная статистика пользователей
            </a>
            <a href="{{ route('admin.statistics.workouts') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-chart-bar mr-2"></i>Детальная статистика тренировок
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // График роста пользователей
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthData = @json($userGrowth);
    
    // Подготавливаем данные для графика
    const labels = userGrowthData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('ru-RU', { month: 'short', year: 'numeric' });
    });
    
    const data = userGrowthData.map(item => item.count);
    
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Регистрации',
                data: data,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
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
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection
















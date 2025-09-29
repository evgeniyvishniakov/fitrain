@extends('crm.layouts.app')

@section('title', 'Дневник питания')
@section('page-title', 'Дневник питания')

@section('content')
<div class="min-h-screen ">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Описание -->
    
        <div x-data="nutritionApp()" x-init="loadNutritionPlans()" x-cloak>
            <!-- Статистика питания -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-red">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Калории сегодня</div>
                        <div class="stat-value" x-text="getTodayCalories()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-blue">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Белки сегодня (г)</div>
                        <div class="stat-value" x-text="getTodayProteins()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-yellow">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Углеводы сегодня (г)</div>
                        <div class="stat-value" x-text="getTodayCarbs()"></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-icon-green">
                        <svg class="stat-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Жиры сегодня (г)</div>
                        <div class="stat-value" x-text="getTodayFats()"></div>
                    </div>
                </div>
            </div>
            
            <!-- Планы питания -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h4 class="text-md font-semibold text-gray-900 mb-4">Планы питания</h4>
                
                <!-- Индикатор загрузки -->
                <div x-show="loadingNutritionPlans" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <p class="mt-2 text-gray-500">Загрузка планов питания...</p>
                </div>
                
                <!-- Пустое состояние -->
                <div x-show="!loadingNutritionPlans && nutritionPlans.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Нет планов питания</h3>
                    <p class="text-gray-500">У вас пока нет планов питания</p>
                </div>
                
                <!-- Список планов -->
                <div x-show="!loadingNutritionPlans && nutritionPlans.length > 0" class="space-y-4">
                    <template x-for="plan in nutritionPlans" :key="plan.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h5 class="text-lg font-medium text-gray-900">
                                        <span x-text="plan.title || `План питания на ${new Date(0, plan.month - 1).toLocaleString('ru-RU', {month: 'long'})} ${plan.year} г.`"></span>
                                        <span class="text-sm text-gray-600" x-text="`(${plan.nutrition_days ? plan.nutrition_days.length : 0} дней)`"></span>
                                    </h5>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="showDetailedNutritionPlan(plan)" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                            title="Подробнее">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Подробнее
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно детального просмотра плана питания -->
<div x-data="{ detailedNutritionPlan: null }" x-show="detailedNutritionPlan" x-cloak x-transition class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; display: none !important;">
    <div class="bg-white rounded-lg w-full max-w-6xl mx-4 max-h-[85vh] overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900" x-text="detailedNutritionPlan ? (detailedNutritionPlan.title || `План питания на ${new Date(0, detailedNutritionPlan.month - 1).toLocaleString('ru-RU', {month: 'long'})} ${detailedNutritionPlan.year} г.`) : ''"></h3>
            <button @click="detailedNutritionPlan = null" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(85vh-120px)]">
            <template x-if="detailedNutritionPlan">
                <div>
                    <!-- Описание плана -->
                    <div class="mb-6" x-show="detailedNutritionPlan.description">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Описание</h4>
                        <p class="text-gray-600" x-text="detailedNutritionPlan.description"></p>
                    </div>
                    
                    <!-- Статистика -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-red-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.calories || 0), 0)) : 0"></div>
                            <div class="text-sm text-red-800">Общие калории</div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.proteins || 0), 0)) : 0"></div>
                            <div class="text-sm text-blue-800">Общие белки (г)</div>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.carbs || 0), 0)) : 0"></div>
                            <div class="text-sm text-yellow-800">Общие углеводы (г)</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.fats || 0), 0)) : 0"></div>
                            <div class="text-sm text-green-800">Общие жиры (г)</div>
                        </div>
                    </div>
                    
                    <!-- Таблица дней -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">День</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Белки (г)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Жиры (г)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Углеводы (г)</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Калории</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">Заметки</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300">
                                <template x-for="day in (detailedNutritionPlan.nutrition_days || [])" :key="day.id">
                                    <tr>
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900" x-text="new Date(day.date).getDate()"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="parseFloat(day.proteins || 0).toFixed(1)"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="parseFloat(day.fats || 0).toFixed(1)"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="parseFloat(day.carbs || 0).toFixed(1)"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="parseFloat(day.calories || 0).toFixed(1)"></td>
                                        <td class="px-3 py-2 text-sm text-gray-900" x-text="day.notes || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Пустое состояние для дней -->
                    <div x-show="!detailedNutritionPlan.nutrition_days || detailedNutritionPlan.nutrition_days.length === 0" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>Нет данных по дням</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function nutritionApp() {
    return {
        // Переменные для планов питания
        nutritionPlans: [],
        loadingNutritionPlans: true,
        
        // Загрузить планы питания
        async loadNutritionPlans() {
            this.loadingNutritionPlans = true;
            try {
                const response = await fetch('/athlete/nutrition-plans', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.nutritionPlans = await response.json();
                } else {
                    console.error('Ошибка загрузки планов питания');
                }
            } catch (error) {
                console.error('Ошибка:', error);
            } finally {
                this.loadingNutritionPlans = false;
            }
        },
        
        // Показать детальный просмотр плана
        showDetailedNutritionPlan(plan) {
            // Находим модальное окно и устанавливаем данные
            const modal = document.querySelector('[x-data*="detailedNutritionPlan"]');
            if (modal && window.Alpine) {
                const modalData = window.Alpine.$data(modal);
                modalData.detailedNutritionPlan = plan;
            }
        },
        
        // Получить сегодняшнюю дату в формате YYYY-MM-DD
        getTodayDate() {
            return new Date().toISOString().split('T')[0];
        },
        
        // Найти день питания на сегодня
        findTodayNutritionDay() {
            const today = this.getTodayDate();
            for (let plan of this.nutritionPlans) {
                if (plan.nutrition_days) {
                    const todayDay = plan.nutrition_days.find(day => day.date === today);
                    if (todayDay) {
                        return todayDay;
                    }
                }
            }
            return null;
        },
        
        // Получить калории на сегодня
        getTodayCalories() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.calories || 0).toFixed(0) : '—';
        },
        
        // Получить белки на сегодня
        getTodayProteins() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.proteins || 0).toFixed(0) : '—';
        },
        
        // Получить углеводы на сегодня
        getTodayCarbs() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.carbs || 0).toFixed(0) : '—';
        },
        
        // Получить жиры на сегодня
        getTodayFats() {
            const todayDay = this.findTodayNutritionDay();
            return todayDay ? parseFloat(todayDay.fats || 0).toFixed(0) : '—';
        }
    }
}
</script>

<style>
/* Скрыть элементы с x-cloak до инициализации Alpine.js */
[x-cloak] {
    display: none !important;
}
</style>
@endsection
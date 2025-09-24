@extends("crm.layouts.app")

@section("title", "Измерения")
@section("page-title", "История измерений")

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
                <div class="stat-label">Всего измерений</div>
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
                <div class="stat-label">Последнее измерение</div>
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
                <div class="stat-label">Текущий вес</div>
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
                    $bmi = null;
                    $bmiCategory = ['text' => '—', 'color' => 'text-gray-500'];
                    if ($lastMeasurement && $lastMeasurement->weight && auth()->user()->height) {
                        $bmi = $lastMeasurement->weight / ((auth()->user()->height/100) ** 2);
                        if ($bmi < 18.5) {
                            $bmiCategory = ['text' => 'Недостаточный вес', 'color' => 'text-blue-600'];
                        } elseif ($bmi < 25) {
                            $bmiCategory = ['text' => 'Нормальный вес', 'color' => 'text-green-600'];
                        } elseif ($bmi < 30) {
                            $bmiCategory = ['text' => 'Избыточный вес', 'color' => 'text-yellow-600'];
                        } else {
                            $bmiCategory = ['text' => 'Ожирение', 'color' => 'text-red-600'];
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
            <h3 class="text-lg font-semibold text-gray-900">История измерений</h3>
            <button onclick="showAddMeasurementModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Добавить измерение
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
                                    <h4 class="card-title text-lg" x-text="new Date(measurement.measurement_date).toLocaleDateString('ru-RU')"></h4>
                                    <div class="flex space-x-2">
                                        <button @click="editMeasurement(measurement.id)" class="text-indigo-600 hover:text-indigo-800" title="Редактировать">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="deleteMeasurement(measurement.id)" class="text-red-600 hover:text-red-800" title="Удалить">
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
                                        <div class="text-xs text-blue-800">Вес (кг)</div>
                                    </div>
                                    <div class="text-center p-3 rounded-lg" x-show="measurement.weight" :class="getBMICategory(measurement.weight / Math.pow({{ auth()->user()->height ?? 170 }}/100, 2)).bg">
                                        <div class="text-xl font-bold" :class="getBMICategory(measurement.weight / Math.pow({{ auth()->user()->height ?? 170 }}/100, 2)).color" x-text="formatNumber(measurement.weight / Math.pow({{ auth()->user()->height ?? 170 }}/100, 2), '')"></div>
                                        <div class="text-xs" :class="getBMICategory(measurement.weight / Math.pow({{ auth()->user()->height ?? 170 }}/100, 2)).color">ИМТ</div>
                                    </div>
                                </div>
                                
                                <!-- Дополнительные параметры -->
                                <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">% жира:</span>
                                        <span class="font-medium" x-text="measurement.body_fat_percentage ? formatNumber(measurement.body_fat_percentage, '%') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Мышцы:</span>
                                        <span class="font-medium" x-text="measurement.muscle_mass ? formatNumber(measurement.muscle_mass, ' кг') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Вода:</span>
                                        <span class="font-medium" x-text="measurement.water_percentage ? formatNumber(measurement.water_percentage, '%') : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Пульс:</span>
                                        <span class="font-medium" x-text="measurement.resting_heart_rate ? Math.round(parseFloat(measurement.resting_heart_rate)) + ' уд/мин' : '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Давление:</span>
                                        <span class="font-medium" x-text="measurement.blood_pressure_systolic && measurement.blood_pressure_diastolic ? Math.round(parseFloat(measurement.blood_pressure_systolic)) + '/' + Math.round(parseFloat(measurement.blood_pressure_diastolic)) : '—'"></span>
                                    </div>
                                </div>
                                
                                <!-- Объемы тела -->
                                <template x-if="measurement.chest || measurement.waist || measurement.hips || measurement.bicep || measurement.thigh || measurement.neck">
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Объемы тела (см)</h5>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <template x-if="measurement.chest">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Грудь:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.chest, '')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.waist">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Талия:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.waist, '')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.hips">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Бедра:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.hips, '')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.bicep">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Бицепс:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.bicep, '')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.thigh">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Бедро:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.thigh, '')"></span>
                                                </div>
                                            </template>
                                            <template x-if="measurement.neck">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500">Шея:</span>
                                                    <span class="font-medium" x-text="formatNumber(measurement.neck, '')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Комментарии -->
                                <template x-if="measurement.notes">
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-medium text-gray-700 mb-1">Комментарии</h5>
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
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Нет измерений</h3>
                    <p class="text-gray-500 mb-4">Начните отслеживать свои измерения для анализа прогресса</p>
                    <button onclick="showAddMeasurementModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        Добавить первое измерение
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
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Добавить измерение</h3>
                <button onclick="closeMeasurementModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="measurementForm" method="POST">
                @csrf
                <div id="formMethod" style="display: none;"></div>
                
                <!-- Основные параметры -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дата измерения *</label>
                        <input type="date" name="measurement_date" id="measurement_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Вес (кг) *</label>
                        <input type="number" name="weight" id="weight" step="0.1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                        <input type="number" name="height" id="height" step="0.1" readonly
                               value="{{ auth()->user()->height ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Рост берется из вашего профиля</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Процент жира (%)</label>
                        <input type="number" name="body_fat_percentage" id="body_fat_percentage" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                
                <!-- Состав тела -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Состав тела</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Мышечная масса (кг)</label>
                            <input type="number" name="muscle_mass" id="muscle_mass" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Процент воды (%)</label>
                            <input type="number" name="water_percentage" id="water_percentage" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Медицинские показатели -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Медицинские показатели</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пульс в покое (уд/мин)</label>
                            <input type="number" name="resting_heart_rate" id="resting_heart_rate" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Давление (систолическое)</label>
                            <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Давление (диастолическое)</label>
                            <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" step="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Объемы тела -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Объемы тела (см)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Грудь</label>
                            <input type="number" name="chest" id="chest" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Талия</label>
                            <input type="number" name="waist" id="waist" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедра</label>
                            <input type="number" name="hips" id="hips" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бицепс</label>
                            <input type="number" name="bicep" id="bicep" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедро</label>
                            <input type="number" name="thigh" id="thigh" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Шея</label>
                            <input type="number" name="neck" id="neck" step="0.1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <!-- Комментарии -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Комментарии</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Добавьте заметки о вашем состоянии, самочувствии или изменениях..."></textarea>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeMeasurementModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Отмена
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Сохранить
                    </button>
                </div>
            </form>
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
    
    if (modalTitle) modalTitle.textContent = 'Добавить измерение';
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
    
    if (modalTitle) modalTitle.textContent = 'Редактировать измерение';
    if (measurementForm) measurementForm.action = `/athlete/measurements/${measurementId}`;
    if (formMethod) formMethod.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Загружаем данные измерения
    fetch(`/athlete/measurements/${measurementId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const measurement = data.measurement;
                
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
                if (measurementDate) measurementDate.value = measurement.measurement_date;
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
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных:', error);
            alert('Ошибка загрузки данных измерения');
        });
    
    if (modal) modal.classList.remove('hidden');
}

function deleteMeasurement(measurementId) {
    if (confirm('Вы уверены, что хотите удалить это измерение?')) {
        fetch(`/athlete/measurements/${measurementId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при удалении измерения');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Ошибка при удалении измерения');
        });
    }
}

function submitMeasurementForm() {
    const form = document.getElementById('measurementForm');
    const formData = new FormData(form);
    
    let url = form.action;
    let method = 'POST';
    
    if (currentMeasurementId) {
        // Редактирование существующего измерения
        url = `{{ url('/athlete/measurements') }}/${currentMeasurementId}`;
        formData.append('_method', 'PUT');
    }
    
    // Добавляем CSRF токен
    formData.append('_token', '{{ csrf_token() }}');
    
    fetch(url, {
        method: method,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeMeasurementModal();
            // Перезагружаем страницу для обновления данных
            window.location.reload();
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при сохранении измерения');
    });
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

// Делаем функции глобальными для доступа из Alpine.js после загрузки DOM
document.addEventListener('DOMContentLoaded', function() {
    window.showAddMeasurementModal = showAddMeasurementModal;
    window.editMeasurement = editMeasurement;
    window.deleteMeasurement = deleteMeasurement;
    window.submitMeasurementForm = submitMeasurementForm;
    window.closeMeasurementModal = closeMeasurementModal;
});

</script>
@endsection
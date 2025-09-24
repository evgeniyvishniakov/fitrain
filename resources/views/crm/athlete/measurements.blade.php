@extends("crm.layouts.app")

@section("title", "Измерения")
@section("page-title", "История измерений")

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
                <div class="stat-label">ИМТ</div>
                <div class="stat-value">{{ $lastMeasurement && $lastMeasurement->weight && auth()->user()->height ? number_format($lastMeasurement->weight / ((auth()->user()->height/100) ** 2), 1) : '—' }}</div>
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="space-y-6">
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
        
        @if($measurements->count() > 0)
            <!-- Карточки измерений -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($measurements as $measurement)
                <div class="card hover:shadow-lg transition-shadow duration-200">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h4 class="card-title text-lg">{{ $measurement->measurement_date->format('d.m.Y') }}</h4>
                            <div class="flex space-x-2">
                                <button onclick="editMeasurement({{ $measurement->id }})" class="text-indigo-600 hover:text-indigo-800" title="Редактировать">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button onclick="deleteMeasurement({{ $measurement->id }})" class="text-red-600 hover:text-red-800" title="Удалить">
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
                                <div class="text-xl font-bold text-blue-600">{{ $measurement->weight ?? '—' }}</div>
                                <div class="text-xs text-blue-800">Вес (кг)</div>
                            </div>
                            @if($measurement->weight && auth()->user()->height)
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-xl font-bold text-purple-600">{{ number_format($measurement->weight / ((auth()->user()->height/100) ** 2), 1) }}</div>
                                <div class="text-xs text-purple-800">ИМТ</div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Дополнительные параметры -->
                        <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">% жира:</span>
                                <span class="font-medium">{{ $measurement->body_fat_percentage ? number_format($measurement->body_fat_percentage, 1) . '%' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Мышцы:</span>
                                <span class="font-medium">{{ $measurement->muscle_mass ? number_format($measurement->muscle_mass, 1) . ' кг' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Вода:</span>
                                <span class="font-medium">{{ $measurement->water_percentage ? number_format($measurement->water_percentage, 1) . '%' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Пульс:</span>
                                <span class="font-medium">{{ $measurement->resting_heart_rate ? number_format($measurement->resting_heart_rate, 0) . ' уд/мин' : '—' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Давление:</span>
                                <span class="font-medium">
                                    @if($measurement->blood_pressure_systolic && $measurement->blood_pressure_diastolic)
                                        {{ number_format($measurement->blood_pressure_systolic, 0) }}/{{ number_format($measurement->blood_pressure_diastolic, 0) }}
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <!-- Объемы тела -->
                        @if($measurement->chest || $measurement->waist || $measurement->hips || $measurement->bicep || $measurement->thigh || $measurement->neck)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Объемы тела (см)</h5>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                @if($measurement->chest)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Грудь:</span>
                                    <span class="font-medium">{{ number_format($measurement->chest, 1) }}</span>
                                </div>
                                @endif
                                @if($measurement->waist)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Талия:</span>
                                    <span class="font-medium">{{ number_format($measurement->waist, 1) }}</span>
                                </div>
                                @endif
                                @if($measurement->hips)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Бедра:</span>
                                    <span class="font-medium">{{ number_format($measurement->hips, 1) }}</span>
                                </div>
                                @endif
                                @if($measurement->bicep)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Бицепс:</span>
                                    <span class="font-medium">{{ number_format($measurement->bicep, 1) }}</span>
                                </div>
                                @endif
                                @if($measurement->thigh)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Бедро:</span>
                                    <span class="font-medium">{{ number_format($measurement->thigh, 1) }}</span>
                                </div>
                                @endif
                                @if($measurement->neck)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Шея:</span>
                                    <span class="font-medium">{{ number_format($measurement->neck, 1) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <!-- Комментарии -->
                        @if($measurement->notes)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-700 mb-1">Комментарии</h5>
                            <p class="text-sm text-gray-600">{{ $measurement->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Пагинация -->
            <div class="mt-8">
                {{ $measurements->links() }}
            </div>
        @else
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
        @endif
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

function showAddMeasurementModal() {
    currentMeasurementId = null;
    document.getElementById('modalTitle').textContent = 'Добавить измерение';
    document.getElementById('measurementForm').action = '{{ route("crm.athlete.measurements.store") }}';
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('measurementForm').reset();
    document.getElementById('measurement_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('measurementModal').classList.remove('hidden');
}

function editMeasurement(measurementId) {
    currentMeasurementId = measurementId;
    document.getElementById('modalTitle').textContent = 'Редактировать измерение';
    document.getElementById('measurementForm').action = `/athlete/measurements/${measurementId}`;
    document.getElementById('formMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    
    // Загружаем данные измерения
    fetch(`/athlete/measurements/${measurementId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const measurement = data.measurement;
                document.getElementById('measurement_date').value = measurement.measurement_date;
                document.getElementById('weight').value = measurement.weight || '';
                document.getElementById('height').value = {{ auth()->user()->height ?? 'null' }};
                document.getElementById('body_fat_percentage').value = measurement.body_fat_percentage || '';
                document.getElementById('muscle_mass').value = measurement.muscle_mass || '';
                document.getElementById('water_percentage').value = measurement.water_percentage || '';
                document.getElementById('resting_heart_rate').value = measurement.resting_heart_rate || '';
                document.getElementById('blood_pressure_systolic').value = measurement.blood_pressure_systolic || '';
                document.getElementById('blood_pressure_diastolic').value = measurement.blood_pressure_diastolic || '';
                document.getElementById('chest').value = measurement.chest || '';
                document.getElementById('waist').value = measurement.waist || '';
                document.getElementById('hips').value = measurement.hips || '';
                document.getElementById('bicep').value = measurement.bicep || '';
                document.getElementById('thigh').value = measurement.thigh || '';
                document.getElementById('neck').value = measurement.neck || '';
                document.getElementById('notes').value = measurement.notes || '';
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки данных:', error);
            alert('Ошибка загрузки данных измерения');
        });
    
    document.getElementById('measurementModal').classList.remove('hidden');
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

function closeMeasurementModal() {
    document.getElementById('measurementModal').classList.add('hidden');
    currentMeasurementId = null;
}
</script>
@endsection
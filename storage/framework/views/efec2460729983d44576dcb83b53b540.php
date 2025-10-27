<?php $__env->startSection("title", __('common.athletes')); ?>
<?php $__env->startSection("page-title", __('common.athletes')); ?>

<style>
@media (max-width: 767px) {
    .desktop-version {
        display: none !important;
    }
    .mobile-version {
        display: block !important;
    }
}
@media (min-width: 768px) {
    .desktop-version {
        display: flex !important;
    }
    .mobile-version {
        display: none !important;
    }
}

/* Сетка для информации о пакете */
.package-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 1024px) {
    .package-info-grid {
        grid-template-columns: 1fr 1fr;
    }
}

/* Скрытие скроллбара для вкладок */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Адаптация прогресса для мобилки */
@media (max-width: 767px) {
    /* Уменьшенные отступы для мобилки */
    .p-6 {
        padding: 0.7rem !important;
    }
    
    .progress-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
        margin: 0 -1rem !important;
        padding: 0 1rem !important;
    }
    
    .progress-controls {
        width: 100% !important;
        justify-content: space-between !important;
        margin-bottom: 25px !important;
    }
    
    .progress-select {
        flex: 1 !important;
        max-width: 150px !important;
    }
    
    .progress-count {
        font-size: 0.875rem !important;
        white-space: nowrap !important;
    }
    
    .chart-container {
        height: 300px !important;
        margin: 0 -3.4rem !important;
        padding: 0 1.5rem !important;
    }
    
    .stats-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
        margin: 0 -1rem !important;
        padding: 0 1rem !important;
    }
    
    .stats-card {
        padding: 1.5rem !important;
        margin: 0 !important;
    }
    
    .stats-title {
        font-size: 0.875rem !important;
    }
    
    .stats-value {
        font-size: 1.75rem !important;
    }
    
    /* Графики прогресса - расширяем контейнеры */
    .progress-chart-container {
        margin: 0 -1.5rem !important;
        padding: 0 1.5rem !important;
        gap: 1.5rem !important;
    }
    
    .progress-chart-card {
        padding: 1rem !important;
        margin: 0 !important;
        margin-bottom: 1.5rem !important;
    }
    
    .progress-chart-card:last-child {
        margin-bottom: 0 !important;
    }
}

/* Стили для grid измерений */
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

/* Стили для пагинации */
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

<script>
// SPA функциональность для спортсменов
function athletesApp() {
    return {
        currentView: 'list', // list, create, edit, view, addMeasurement, editMeasurement, addPayment, editPayment, addNutrition
        athletes: <?php echo json_encode($athletes->items(), 15, 512) ?>,
        currentAthlete: null,
        activeTab: 'overview', // для вкладок в просмотре
        measurements: [], // Массив для хранения измерений
        currentMeasurement: null, // Текущее измерение для редактирования
        selectedMeasurement: 'chest', // Выбранный объем для отображения
        selectedPeriod: 'all', // Выбранный период для фильтрации
        search: '',
        sportLevel: '',
        currentPage: 1,
        itemsPerPage: 12,
        measurementsCurrentPage: 1,
        measurementsItemsPerPage: 6,
        
        
        // Поля формы
        formName: '',
        formEmail: '',
        formPassword: '',
        showPassword: false,
        formPhone: '',
        formBirthDate: '',
        formGender: '',
        formSportLevel: '',
        formGoals: [],
        formHealthRestrictions: '',
        formIsActive: '1',
        
        // Поля формы измерений
        measurementDate: '',
        measurementWeight: '',
        measurementHeight: '',
        measurementBodyFat: '',
        measurementMuscleMass: '',
        measurementWaterPercentage: '',
        measurementChest: '',
        measurementWaist: '',
        measurementHips: '',
        measurementBicep: '',
        measurementThigh: '',
        measurementNeck: '',
        measurementHeartRate: '',
        measurementBloodPressureSystolic: '',
        measurementBloodPressureDiastolic: '',
        measurementNotes: '',
        
        // Поля формы плана питания
        nutritionMonth: '',
        nutritionYear: '',
        nutritionTitle: '',
        nutritionDescription: '',
        
        // Поля формы платежей
        paymentData: {
            package_type: '',
            total_sessions: 0,
            used_sessions: 0,
            package_price: 0,
            purchase_date: '',
            expires_date: '',
            payment_method: '<?php echo e(__('common.cash')); ?>',
            description: ''
        },
        
        // Навигация
        showList() {
            this.currentView = 'list';
            this.currentAthlete = null;
        },
        
        showCreate() {
            this.currentView = 'create';
            this.currentAthlete = null;
            this.formName = '';
            this.formEmail = '';
            this.formPassword = '';
            this.showPassword = false;
            this.formPhone = '';
            this.formBirthDate = '';
            this.formGender = '';
            this.formSportLevel = '';
            this.formGoals = [];
            this.formHealthRestrictions = '';
            this.formIsActive = '1';
        },
        
        // Генерация пароля
        generatePassword() {
            const length = 12;
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            let password = "";
            
            // Добавляем минимум по одному символу каждого типа
            password += "abcdefghijklmnopqrstuvwxyz"[Math.floor(Math.random() * 26)]; // строчная буква
            password += "ABCDEFGHIJKLMNOPQRSTUVWXYZ"[Math.floor(Math.random() * 26)]; // заглавная буква
            password += "0123456789"[Math.floor(Math.random() * 10)]; // цифра
            password += "!@#$%^&*"[Math.floor(Math.random() * 8)]; // спецсимвол
            
            // Заполняем остальные символы случайно
            for (let i = 4; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }
            
            // Перемешиваем символы
            password = password.split('').sort(() => Math.random() - 0.5).join('');
            
            this.formPassword = password;
            this.showPassword = true; // Показываем сгенерированный пароль
        },
        
        showEdit(athleteId) {
            this.currentView = 'edit';
            this.currentAthlete = this.athletes.find(a => a.id === athleteId);
            this.formName = this.currentAthlete.name;
            this.formEmail = this.currentAthlete.email;
            this.formPassword = ''; // Оставляем пустым для редактирования
            this.showPassword = false;
            this.formPhone = this.currentAthlete.phone || '';
            this.formBirthDate = this.currentAthlete.birth_date || '';
            this.formGender = this.currentAthlete.gender || '';
            
            
            this.formSportLevel = this.currentAthlete.sport_level || '';
            this.formGoals = this.currentAthlete.goals || [];
            this.formHealthRestrictions = this.currentAthlete.health_restrictions ? JSON.stringify(this.currentAthlete.health_restrictions) : '';
            this.formIsActive = this.currentAthlete.is_active ? '1' : '0';
        },
        
        async showView(athleteId) {
            this.currentView = 'view';
            this.currentAthlete = this.athletes.find(a => a.id === athleteId);
            this.activeTab = 'overview'; // сбрасываем на первую вкладку
            
            // Очищаем предыдущие графики
            this.destroyCharts();
            
            this.loadingAthleteData = true;
            
            try {
                // Загружаем измерения спортсмена
                await this.loadMeasurements(athleteId);
                // Загружаем планы питания
                await this.loadNutritionPlans();
            } finally {
                this.loadingAthleteData = false;
            }
        },
        
        // Обновление финансовых данных из истории платежей
        updateFinanceFromPaymentHistory() {
            if (!this.currentAthlete) return;
            
            const paymentHistory = this.currentAthlete.payment_history || [];
            let totalSessionsFromHistory = 0;
            
            paymentHistory.forEach(payment => {
                const description = payment.description || '';
                const sessions = this.extractSessionsFromDescription(description);
                totalSessionsFromHistory += sessions;
            });
            
            // Обновляем финансовые данные для отображения
            this.currentAthlete.finance = {
                id: this.currentAthlete.id,
                package_type: this.currentAthlete.package_type,
                total_sessions: totalSessionsFromHistory || this.currentAthlete.total_sessions || 0,
                used_sessions: this.currentAthlete.used_sessions || 0,
                remaining_sessions: (totalSessionsFromHistory || this.currentAthlete.total_sessions || 0) - (this.currentAthlete.used_sessions || 0),
                package_price: this.currentAthlete.package_price || 0,
                purchase_date: this.currentAthlete.purchase_date,
                expires_date: this.currentAthlete.expires_date,
                status: (totalSessionsFromHistory || this.currentAthlete.total_sessions || 0) > 0 ? 'active' : 'inactive',
                total_paid: this.currentAthlete.total_paid || 0,
                last_payment_date: this.currentAthlete.last_payment_date,
                payment_history: paymentHistory
            };
        },
        
        // Форматирование чисел - убираем лишние нули
        formatNumber(value, unit = '') {
            if (value === null || value === undefined || value === '') return '';
            const num = parseFloat(value);
            if (isNaN(num)) return '';
            // Убираем лишние нули после точки
            const formatted = num % 1 === 0 ? num.toString() : num.toFixed(1).replace(/\.?0+$/, '');
            return formatted + unit;
        },

        // Функция для определения категории ИМТ
        getBMICategory(bmi) {
            if (!bmi || isNaN(bmi)) return { text: '—', color: 'text-gray-500', bg: 'bg-gray-100' };
            
            if (bmi < 18.5) {
                return { text: '<?php echo e(__('common.underweight')); ?>', color: 'text-blue-600', bg: 'bg-blue-100' };
            } else if (bmi < 25) {
                return { text: '<?php echo e(__('common.normal_weight')); ?>', color: 'text-green-600', bg: 'bg-green-100' };
            } else if (bmi < 30) {
                return { text: '<?php echo e(__('common.overweight')); ?>', color: 'text-yellow-600', bg: 'bg-yellow-100' };
            } else {
                return { text: '<?php echo e(__('common.obesity')); ?>', color: 'text-red-600', bg: 'bg-red-100' };
            }
        },

        // Функция для расчета изменения веса
        getWeightChange() {
            if (this.measurements.length < 2) return '—';
            
            const firstWeight = this.measurements[this.measurements.length - 1].weight;
            const lastWeight = this.measurements[0].weight;
            const change = lastWeight - firstWeight;
            
            if (change > 0) {
                return '+' + this.formatNumber(change, '<?php echo e(__('common.weight_change_kg')); ?>');
            } else if (change < 0) {
                return this.formatNumber(change, '<?php echo e(__('common.weight_change_kg')); ?>');
            } else {
                return '<?php echo e(__('common.weight_change_zero')); ?>';
            }
        },

        // Очистка графиков
        destroyCharts() {
            if (window.weightChart && typeof window.weightChart.destroy === 'function') {
                window.weightChart.destroy();
                window.weightChart = null;
            }
            if (window.bodyCompositionChart && typeof window.bodyCompositionChart.destroy === 'function') {
                window.bodyCompositionChart.destroy();
                window.bodyCompositionChart = null;
            }
            if (window.measurementsChart && typeof window.measurementsChart.destroy === 'function') {
                window.measurementsChart.destroy();
                window.measurementsChart = null;
            }
        },

        // Обновление графиков при переключении вкладки
        updateCharts() {
            
            // Ждем, пока вкладка станет видимой
            setTimeout(() => {
                if (window.weightChart && typeof window.weightChart.resize === 'function') {
                    window.weightChart.resize();
                    // console.log('График веса обновлен');
                }
                if (window.bodyCompositionChart && typeof window.bodyCompositionChart.resize === 'function') {
                    window.bodyCompositionChart.resize();
                    // console.log('График состава тела обновлен');
                }
                if (window.measurementsChart && typeof window.measurementsChart.resize === 'function') {
                    window.measurementsChart.resize();
                    // console.log('График объемов обновлен');
                }
            }, 100);
        },

        // Обновление графика объемов при изменении выбора
        updateMeasurementsChart() {
            if (this.measurements.length === 0) return;
            
            const filteredMeasurements = this.getFilteredMeasurements();
            if (filteredMeasurements.length === 0) return;
            
            const sortedMeasurements = [...filteredMeasurements].reverse();
            const labels = sortedMeasurements.map(m => {
                const date = new Date(m.measurement_date);
                return date.toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>', { month: 'short', day: 'numeric' });
            });
            
            this.createMeasurementsChart(labels, sortedMeasurements);
        },

        // Получение отфильтрованных измерений по периоду
        getFilteredMeasurements() {
            if (this.measurements.length === 0) return [];
            
            if (this.selectedPeriod === 'all') {
                return this.measurements;
            }
            
            const months = parseInt(this.selectedPeriod);
            const cutoffDate = new Date();
            cutoffDate.setMonth(cutoffDate.getMonth() - months);
            
            return this.measurements.filter(measurement => {
                const measurementDate = new Date(measurement.measurement_date);
                return measurementDate >= cutoffDate;
            });
        },

        // Подсчет количества отфильтрованных измерений
        getFilteredMeasurementsCount() {
            return this.getFilteredMeasurements().length;
        },

        // Обновление фильтра периодов
        updatePeriodFilter() {
            this.initCharts();
        },

        // Инициализация графиков
        initCharts() {
            // console.log('=== ИНИЦИАЛИЗАЦИЯ ГРАФИКОВ ===');
            // console.log('Количество измерений:', this.measurements.length);
            // console.log('Измерения:', this.measurements);
            
            if (this.measurements.length === 0) {
                // console.log('Нет измерений, пропускаем создание графиков');
                return;
            }
            
            // Проверяем, что Chart.js загружен
            if (typeof Chart === 'undefined') {
                console.error('<?php echo e(__('common.chart_js_not_loaded')); ?>');
                return;
            }
            // console.log('Chart.js загружен успешно');

            // Очищаем предыдущие графики
            this.destroyCharts();

            // Получаем отфильтрованные данные по периоду
            const filteredMeasurements = this.getFilteredMeasurements();
            // console.log('Отфильтрованные измерения:', filteredMeasurements);
            
            if (filteredMeasurements.length === 0) {
                // console.log('Нет измерений в выбранном периоде');
                return;
            }

            // Подготовка данных
            const sortedMeasurements = [...filteredMeasurements].reverse(); // Сортируем по дате
            const labels = sortedMeasurements.map(m => {
                const date = new Date(m.measurement_date);
                return date.toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>', { month: 'short', day: 'numeric' });
            });

            // console.log('Отсортированные измерения:', sortedMeasurements);
            // console.log('Метки для графиков:', labels);

            // Проверяем наличие canvas элементов
            // console.log('Canvas weightChart:', document.getElementById('weightChart'));
            // console.log('Canvas bodyCompositionChart:', document.getElementById('bodyCompositionChart'));
            // console.log('Canvas measurementsChart:', document.getElementById('measurementsChart'));

            // График веса
            this.createWeightChart(labels, sortedMeasurements);
            
            // График состава тела
            this.createBodyCompositionChart(labels, sortedMeasurements);
            
            // График объемов
            this.createMeasurementsChart(labels, sortedMeasurements);
            
            // console.log('=== ГРАФИКИ СОЗДАНЫ ===');
        },

        // Создание графика веса
        createWeightChart(labels, measurements) {
            // console.log('=== СОЗДАНИЕ ГРАФИКА ВЕСА ===');
            const ctx = document.getElementById('weightChart');
            if (!ctx) {
                console.error('<?php echo e(__('common.weight_canvas_not_found')); ?>');
                return;
            }
            // console.log('Canvas элемент найден:', ctx);

            const weightData = measurements.map(m => m.weight);
            // console.log('Данные веса:', weightData);
            // console.log('Метки:', labels);

            // Уничтожаем предыдущий график если есть
            if (window.weightChart && typeof window.weightChart.destroy === 'function') {
                window.weightChart.destroy();
            }

            try {
                window.weightChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '<?php echo e(__('common.weight_kg')); ?>',
                        data: measurements.map(m => m.weight),
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
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 20,
                            right: 20
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            min: function(context) {
                                const values = context.chart.data.datasets[0].data;
                                return Math.min(...values) - 2;
                            },
                            max: function(context) {
                                const values = context.chart.data.datasets[0].data;
                                return Math.max(...values) + 2;
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            // console.log('График веса создан успешно');
            } catch (error) {
                console.error('<?php echo e(__('common.weight_chart_error')); ?>:', error);
            }
        },

        // Создание графика состава тела
        createBodyCompositionChart(labels, measurements) {
            const ctx = document.getElementById('bodyCompositionChart');
            if (!ctx) return;

            if (window.bodyCompositionChart && typeof window.bodyCompositionChart.destroy === 'function') {
                window.bodyCompositionChart.destroy();
            }

            window.bodyCompositionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '<?php echo e(__('common.body_fat_percentage')); ?>',
                        data: measurements.map(m => m.body_fat_percentage),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y'
                    }, {
                        label: '<?php echo e(__('common.muscle_mass_kg')); ?>',
                        data: measurements.map(m => m.muscle_mass),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 20,
                            right: 20
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: '<?php echo e(__('common.body_fat_percentage_short')); ?>'
                            },
                            min: function(context) {
                                const values = context.chart.data.datasets[0].data;
                                return Math.min(...values) - 2;
                            },
                            max: function(context) {
                                const values = context.chart.data.datasets[0].data;
                                return Math.max(...values) + 2;
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: '<?php echo e(__('common.muscle_mass_kg_short')); ?>'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                            min: function(context) {
                                const values = context.chart.data.datasets[1].data;
                                return Math.min(...values) - 2;
                            },
                            max: function(context) {
                                const values = context.chart.data.datasets[1].data;
                                return Math.max(...values) + 2;
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        },

        // Создание графика объемов
        createMeasurementsChart(labels, measurements) {
            const ctx = document.getElementById('measurementsChart');
            if (!ctx) return;

            if (window.measurementsChart && typeof window.measurementsChart.destroy === 'function') {
                window.measurementsChart.destroy();
            }

            // Получаем данные для выбранного измерения
            const measurementConfig = {
                chest: { label: '<?php echo e(__('common.chest_cm')); ?>', color: '#8B5CF6', bgColor: 'rgba(139, 92, 246, 0.1)' },
                waist: { label: '<?php echo e(__('common.waist_cm')); ?>', color: '#F59E0B', bgColor: 'rgba(245, 158, 11, 0.1)' },
                hips: { label: '<?php echo e(__('common.hips_cm')); ?>', color: '#EC4899', bgColor: 'rgba(236, 72, 153, 0.1)' },
                bicep: { label: '<?php echo e(__('common.bicep_cm')); ?>', color: '#10B981', bgColor: 'rgba(16, 185, 129, 0.1)' },
                thigh: { label: '<?php echo e(__('common.thigh_cm')); ?>', color: '#EF4444', bgColor: 'rgba(239, 68, 68, 0.1)' },
                neck: { label: '<?php echo e(__('common.neck_cm')); ?>', color: '#6B7280', bgColor: 'rgba(107, 114, 128, 0.1)' }
            };

            let datasets = [];

            if (this.selectedMeasurement === 'all') {
                // Показываем все объемы
                datasets = Object.keys(measurementConfig).map(key => ({
                    label: measurementConfig[key].label,
                    data: measurements.map(m => m[key]),
                    borderColor: measurementConfig[key].color,
                    backgroundColor: measurementConfig[key].bgColor,
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }));
            } else {
                // Показываем только выбранный объем
                const config = measurementConfig[this.selectedMeasurement];
                datasets = [{
                    label: config.label,
                    data: measurements.map(m => m[this.selectedMeasurement]),
                    borderColor: config.color,
                    backgroundColor: config.bgColor,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }];
            }

            window.measurementsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            display: this.selectedMeasurement === 'all'
                        }
                    },
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 20,
                            left: 20,
                            right: 20
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            min: function(context) {
                                const allValues = [];
                                context.chart.data.datasets.forEach(dataset => {
                                    allValues.push(...dataset.data);
                                });
                                return Math.min(...allValues) - 3;
                            },
                            max: function(context) {
                                const allValues = [];
                                context.chart.data.datasets.forEach(dataset => {
                                    allValues.push(...dataset.data);
                                });
                                return Math.max(...allValues) + 3;
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        },
        
        // Загрузка измерений
        async loadMeasurements(athleteId) {
            try {
                const response = await fetch(`/trainer/athletes/${athleteId}/measurements`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // console.log('Загруженные измерения:', result.measurements);
                    this.measurements = result.measurements;
                    this.measurementsCurrentPage = 1;
                    
                    // Инициализируем графики после загрузки измерений
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                    
                    // Обновляем данные спортсмена актуальными данными с сервера
                    if (result.athlete) {
                        this.currentAthlete = { ...this.currentAthlete, ...result.athlete };
                        
                        // Обновляем спортсмена в общем списке
                        const athleteIndex = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                        if (athleteIndex !== -1) {
                            this.athletes[athleteIndex] = { ...this.athletes[athleteIndex], ...result.athlete };
                        }
                    }
                } else {
                    console.error('<?php echo e(__('common.measurements_loading_error')); ?>:', result.message);
                    this.measurements = [];
                }
            } catch (error) {
                console.error('<?php echo e(__('common.measurements_loading_error')); ?>:', error);
                this.measurements = [];
            }
        },
        
        showAddMeasurement() {
            this.currentView = 'addMeasurement';
            this.currentMeasurement = null;
            this.measurementDate = new Date().toISOString().split('T')[0];
            
            // Берем вес и рост из последнего измерения, если есть, иначе из профиля
            const latestMeasurement = this.measurements.length > 0 ? this.measurements[0] : null;
            this.measurementWeight = latestMeasurement?.weight || this.currentAthlete?.weight || '';
            this.measurementHeight = latestMeasurement?.height || this.currentAthlete?.height || '';
            this.measurementBodyFat = '';
            this.measurementMuscleMass = '';
            this.measurementWaterPercentage = '';
            this.measurementChest = '';
            this.measurementWaist = '';
            this.measurementHips = '';
            this.measurementBicep = '';
            this.measurementThigh = '';
            this.measurementNeck = '';
            this.measurementHeartRate = '';
            this.measurementBloodPressureSystolic = '';
            this.measurementBloodPressureDiastolic = '';
            this.measurementNotes = '';
        },
        
        showAddNutritionPlan() {
            this.currentView = 'addNutrition';
            this.nutritionMonth = new Date().getMonth() + 1;
            this.nutritionYear = new Date().getFullYear();
            this.nutritionTitle = '';
            this.nutritionDescription = '';
        },
        
        getDaysInMonth(month, year) {
            return new Date(year, month, 0).getDate();
        },
        
        async saveNutritionPlanForm() {
            try {
                const nutritionData = {
                    athlete_id: this.currentAthlete.id,
                    month: this.nutritionMonth,
                    year: this.nutritionYear,
                    title: this.nutritionTitle,
                    description: this.nutritionDescription,
                    days: []
                };
                
                // Собираем данные по дням - только заполненные
                const daysInMonth = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
                for (let day = 1; day <= daysInMonth; day++) {
                    const proteins = document.querySelector(`input[name="proteins_${day}"]`)?.value;
                    const fats = document.querySelector(`input[name="fats_${day}"]`)?.value;
                    const carbs = document.querySelector(`input[name="carbs_${day}"]`)?.value;
                    const notes = document.querySelector(`input[name="notes_${day}"]`)?.value;
                    
                    // Добавляем день только если есть хотя бы одно заполненное поле
                    if (proteins || fats || carbs || notes) {
                        nutritionData.days.push({
                            date: `${this.nutritionYear}-${String(this.nutritionMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`,
                            proteins: parseFloat(proteins) || 0,
                            fats: parseFloat(fats) || 0,
                            carbs: parseFloat(carbs) || 0,
                            notes: notes || ''
                        });
                    }
                }
                
                // Если нет заполненных дней, создаем план без дней
                if (nutritionData.days.length === 0) {
                    console.log('Нет заполненных дней, создаем пустой план');
                }
                
                const response = await fetch('/trainer/nutrition-plans', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(nutritionData)
                });
                
                if (response.ok) {
                    this.currentView = 'view';
                    this.activeTab = 'nutrition';
                    this.loadNutritionPlans(); // Перезагружаем список планов
                } else {
                    const error = await response.json();
                    console.error('<?php echo e(__('common.server_error')); ?>:', error);
                    alert(error.error || '<?php echo e(__('common.nutrition_plan_creation_error')); ?>');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                console.error('Данные:', nutritionData);
                alert('<?php echo e(__('common.nutrition_plan_error')); ?>: ' + error.message);
            }
        },
        
        // Быстрое заполнение колонки
        fillColumn(column) {
            const columnNames = {
                'proteins': '<?php echo e(__('common.proteins_g_header')); ?>',
                'fats': '<?php echo e(__('common.fats_g_header')); ?>', 
                'carbs': '<?php echo e(__('common.carbs_g_header')); ?>'
            };
            
            const value = prompt(`Введите значение для ${columnNames[column]}:`, '');
            if (value === null || value === '') return;
            
            const daysInMonth = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
            for (let day = 1; day <= daysInMonth; day++) {
                const input = document.querySelector(`input[name="${column}_${day}"]`);
                if (input) {
                    input.value = value;
                    this.calculateCalories(day);
                }
            }
        },
        
        // Заполнение всех колонок сразу
        fillAllColumns() {
            const proteins = prompt('<?php echo e(__('common.proteins_g_header')); ?>:', '');
            if (proteins === null) return;
            
            const fats = prompt('<?php echo e(__('common.fats_g_header')); ?>:', '');
            if (fats === null) return;
            
            const carbs = prompt('<?php echo e(__('common.carbs_g_header')); ?>:', '');
            if (carbs === null) return;
            
            const daysInMonth = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
            for (let day = 1; day <= daysInMonth; day++) {
                if (proteins !== '') {
                    const proteinsInput = document.querySelector(`input[name="proteins_${day}"]`);
                    if (proteinsInput) proteinsInput.value = proteins;
                }
                if (fats !== '') {
                    const fatsInput = document.querySelector(`input[name="fats_${day}"]`);
                    if (fatsInput) fatsInput.value = fats;
                }
                if (carbs !== '') {
                    const carbsInput = document.querySelector(`input[name="carbs_${day}"]`);
                    if (carbsInput) carbsInput.value = carbs;
                }
                this.calculateCalories(day);
            }
        },
        
        // Очистка всех полей
        clearAll() {
            if (!confirm('<?php echo e(__('common.clear_all_fields_confirm')); ?>')) return;
            
            const daysInMonth = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
            for (let day = 1; day <= daysInMonth; day++) {
                document.querySelector(`input[name="proteins_${day}"]`).value = '';
                document.querySelector(`input[name="fats_${day}"]`).value = '';
                document.querySelector(`input[name="carbs_${day}"]`).value = '';
                document.querySelector(`input[name="notes_${day}"]`).value = '';
                document.querySelector(`input[name="calories_${day}"]`).value = '';
            }
        },
        
        // Расчет калорий для конкретного дня
        calculateCalories(day) {
            const proteins = parseFloat(document.querySelector(`input[name="proteins_${day}"]`)?.value) || 0;
            const fats = parseFloat(document.querySelector(`input[name="fats_${day}"]`)?.value) || 0;
            const carbs = parseFloat(document.querySelector(`input[name="carbs_${day}"]`)?.value) || 0;
            
            const calories = (proteins * 4) + (fats * 9) + (carbs * 4);
            const caloriesInput = document.querySelector(`input[name="calories_${day}"]`);
            if (caloriesInput) {
                // Убираем лишние нули в конце
                caloriesInput.value = parseFloat(calories.toFixed(1));
            }
        },
        
        // Выделение ячейки (для будущих функций)
        selectCell(element) {
            if (!element || !element.classList) return;
            
            // Убираем выделение с других ячеек
            document.querySelectorAll('.excel-cell').forEach(cell => {
                if (cell.classList) {
                    cell.classList.remove('ring-2', 'ring-blue-500');
                }
            });
            
            // Выделяем текущую ячейку
            element.classList.add('ring-2', 'ring-blue-500');
        },
        
        // Переменные для Excel-стиля
        dragStartValue: null,
        dragStartColumn: null,
        dragStartDay: null,
        isDragging: false,
        
        // Переменные для модального окна
        quickFillModalVisible: false,
        quickFillData: {
            proteins: 120,
            fats: 50,
            carbs: 200,
            startDay: 1,
            endDay: 31
        },
        
        // Переменные для планов питания
        nutritionPlans: [],
        loadingNutritionPlans: false,
        detailedNutritionPlan: null,
        
        // Состояние загрузки данных спортсмена
        loadingAthleteData: false,
        
        // Загрузить планы питания
        async loadNutritionPlans() {
            if (!this.currentAthlete) return;
            
            this.loadingNutritionPlans = true;
            this.detailedNutritionPlan = null; // Закрываем модальное окно "Подробнее" при загрузке
            try {
                const response = await fetch(`/trainer/nutrition-plans?athlete_id=${this.currentAthlete.id}`, {
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
        
        // Редактировать план питания
        editNutritionPlan(plan) {
            this.detailedNutritionPlan = null; // Закрываем модальное окно "Подробнее"
            this.currentView = 'addNutrition';
            this.nutritionMonth = plan.month;
            this.nutritionYear = plan.year;
            this.nutritionTitle = plan.title || '';
            this.nutritionDescription = plan.description || '';
            
            // Заполняем данные по дням
            this.$nextTick(() => {
                if (plan.nutrition_days) {
                    plan.nutrition_days.forEach(day => {
                        const dayNumber = new Date(day.date).getDate();
                        const proteinsInput = document.querySelector(`input[name="proteins_${dayNumber}"]`);
                        const fatsInput = document.querySelector(`input[name="fats_${dayNumber}"]`);
                        const carbsInput = document.querySelector(`input[name="carbs_${dayNumber}"]`);
                        const notesInput = document.querySelector(`input[name="notes_${dayNumber}"]`);
                        
                        if (proteinsInput) proteinsInput.value = day.proteins ? parseFloat(day.proteins) : '';
                        if (fatsInput) fatsInput.value = day.fats ? parseFloat(day.fats) : '';
                        if (carbsInput) carbsInput.value = day.carbs ? parseFloat(day.carbs) : '';
                        if (notesInput) notesInput.value = day.notes || '';
                        
                        this.calculateCalories(dayNumber);
                    });
                }
            });
        },
        
        // Удалить план питания
        deleteNutritionPlan(planId) {
            const plan = this.nutritionPlans.find(p => p.id === planId);
            const planTitle = plan ? (plan.title || `<?php echo e(__('common.nutrition_plan_on')); ?> ${new Date(0, plan.month - 1).toLocaleString('ru-RU', {month: 'long'})} ${plan.year} <?php echo e(__('common.year_short')); ?>`) : '<?php echo e(__('common.nutrition_plan_default')); ?>';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete')); ?> <?php echo e(__('common.nutrition_plan')); ?>',
                    message: `Вы уверены, что хотите удалить "${planTitle}"?`,
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
                    onConfirm: () => this.performDeleteNutritionPlan(planId)
                }
            }));
        },
        
        async performDeleteNutritionPlan(planId) {
            try {
                const response = await fetch(`/trainer/nutrition-plans/${planId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.loadNutritionPlans(); // Перезагружаем список
                } else {
                    alert('<?php echo e(__('common.delete_nutrition_plan_error')); ?>');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                alert('<?php echo e(__('common.delete_nutrition_plan_error')); ?>');
            }
        },
        
        // Показать детальный просмотр плана
        showDetailedNutritionPlan(plan) {
            this.detailedNutritionPlan = plan;
        },
        
        // Закрыть детальный просмотр
        closeDetailedNutritionPlan() {
            this.detailedNutritionPlan = null;
        },
        
        // Получить текущий вес
        getCurrentWeight() {
            if (this.measurements && this.measurements.length > 0) {
                return this.formatNumber(this.measurements[0].weight, ' кг');
            }
            if (this.currentAthlete?.current_weight) {
                return this.formatNumber(this.currentAthlete.current_weight, ' кг');
            }
            return '—';
        },
        
        // Получить текущий рост
        getCurrentHeight() {
            if (this.measurements && this.measurements.length > 0) {
                return this.formatNumber(this.measurements[0].height, ' см');
            }
            if (this.currentAthlete?.current_height) {
                return this.formatNumber(this.currentAthlete.current_height, ' см');
            }
            return '—';
        },
        
        // Показать модальное окно быстрого заполнения
        showQuickFillModal() {
            this.quickFillData.endDay = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
            this.quickFillModalVisible = true;
        },
        
        // Применить быстрое заполнение
        applyQuickFill() {
            const { proteins, fats, carbs, startDay, endDay } = this.quickFillData;
            const daysInMonth = this.getDaysInMonth(this.nutritionMonth, this.nutritionYear);
            
            if (startDay < 1 || endDay > daysInMonth || startDay > endDay) {
                alert('<?php echo e(__('common.invalid_days_error')); ?>');
                return;
            }
            
            // Заполняем все ячейки
            for (let day = startDay; day <= endDay; day++) {
                const proteinsInput = document.querySelector(`input[name="proteins_${day}"]`);
                const fatsInput = document.querySelector(`input[name="fats_${day}"]`);
                const carbsInput = document.querySelector(`input[name="carbs_${day}"]`);
                
                if (proteinsInput) proteinsInput.value = proteins;
                if (fatsInput) fatsInput.value = fats;
                if (carbsInput) carbsInput.value = carbs;
                
                this.calculateCalories(day);
            }
            
            this.quickFillModalVisible = false;
        },
        
        
        showAddPayment() {
            this.currentView = 'addPayment';
            this.paymentData = {
                package_type: '',
                total_sessions: 0,
                used_sessions: 0,
                package_price: 0,
                purchase_date: new Date().toISOString().split('T')[0],
                expires_date: '',
                payment_method: '<?php echo e(__('common.cash')); ?>',
                description: ''
            };
        },
        
        // Функция для отображения типов пакетов
        getPackageTypeLabel(type) {
            const labels = {
                'single': '<?php echo e(__('common.single_workout')); ?>',
                '4_sessions': '<?php echo e(__('common.4_workouts')); ?>',
                '8_sessions': '<?php echo e(__('common.8_workouts')); ?>',
                '12_sessions': '<?php echo e(__('common.12_workouts')); ?>',
                'unlimited': '<?php echo e(__('common.unlimited_month')); ?>',
                'custom': '<?php echo e(__('common.custom_amount')); ?>'
            };
            return labels[type] || type;
        },
        
        // Функция для перевода способов оплаты на украинский
        getPaymentMethodLabel(method) {
            const labels = {
                'cash': 'Готівка',
                'card': 'Банківська картка',
                'transfer': 'Банківський переказ',
                'other': 'Інше'
            };
            return labels[method] || method;
        },
        
        // Автоматическое заполнение количества тренировок по типу пакета
        updateSessionsByPackageType() {
            const packageType = this.paymentData.package_type;
            switch(packageType) {
                case 'single':
                    this.paymentData.total_sessions = 1;
                    break;
                case '4_sessions':
                    this.paymentData.total_sessions = 4;
                    break;
                case '8_sessions':
                    this.paymentData.total_sessions = 8;
                    break;
                case '12_sessions':
                    this.paymentData.total_sessions = 12;
                    break;
                case 'unlimited':
                    this.paymentData.total_sessions = 999; // Символическое значение для безлимита
                    break;
                case 'custom':
                    // Для произвольного количества не меняем значение
                    break;
                default:
                    this.paymentData.total_sessions = 0;
            }
        },
        
        editMeasurement(measurement) {
            this.currentView = 'editMeasurement';
            this.currentMeasurement = measurement;
            // Преобразуем дату в формат YYYY-MM-DD для input[type="date"]
            const date = new Date(measurement.measurement_date);
            const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
            this.measurementDate = localDate.toISOString().split('T')[0];
            this.measurementWeight = measurement.weight || '';
            this.measurementHeight = measurement.height || '';
            this.measurementBodyFat = measurement.body_fat_percentage || '';
            this.measurementMuscleMass = measurement.muscle_mass || '';
            this.measurementWaterPercentage = measurement.water_percentage || '';
            this.measurementChest = measurement.chest || '';
            this.measurementWaist = measurement.waist || '';
            this.measurementHips = measurement.hips || '';
            this.measurementBicep = measurement.bicep || '';
            this.measurementThigh = measurement.thigh || '';
            this.measurementNeck = measurement.neck || '';
            this.measurementHeartRate = measurement.resting_heart_rate || '';
            this.measurementBloodPressureSystolic = measurement.blood_pressure_systolic || '';
            this.measurementBloodPressureDiastolic = measurement.blood_pressure_diastolic || '';
            this.measurementNotes = measurement.notes || '';
        },
        
        // Фильтрация
        get filteredAthletes() {
            let filtered = this.athletes;
            
            if (this.search) {
                filtered = filtered.filter(a => 
                    a.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
            
            if (this.sportLevel) {
                filtered = filtered.filter(a => a.sport_level === this.sportLevel);
            }
            
            return filtered;
        },
        
        // Пагинация
        get totalPages() {
            const total = Math.ceil(this.filteredAthletes.length / this.itemsPerPage);
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
        
        get paginatedAthletes() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredAthletes.slice(start, end);
        },
        
        // Навигация по страницам
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
        
        // Пагинация для измерений
        get measurementsTotalPages() {
            return Math.ceil(this.measurements.length / this.measurementsItemsPerPage);
        },
        
        get paginatedMeasurements() {
            const start = (this.measurementsCurrentPage - 1) * this.measurementsItemsPerPage;
            const end = start + this.measurementsItemsPerPage;
            return this.measurements.slice(start, end);
        },
        
        get measurementsVisiblePages() {
            const pages = [];
            const total = this.measurementsTotalPages;
            const current = this.measurementsCurrentPage;
            
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
        
        goToMeasurementsPage(page) {
            this.measurementsCurrentPage = page;
        },
        
        measurementsPreviousPage() {
            if (this.measurementsCurrentPage > 1) {
                this.measurementsCurrentPage--;
            }
        },
        
        measurementsNextPage() {
            if (this.measurementsCurrentPage < this.measurementsTotalPages) {
                this.measurementsCurrentPage++;
            }
        },
        
        // Метки статусов
        getSportLevelLabel(level) {
            const labels = {
                'beginner': '<?php echo e(__('common.novice')); ?>',
                'intermediate': '<?php echo e(__('common.amateur')); ?>', 
                'advanced': '<?php echo e(__('common.pro')); ?>'
            };
            return labels[level] || level;
        },

        // Метки способов оплаты
        getPaymentMethodText(method) {
            const labels = {
                'cash': '<?php echo e(__('common.cash')); ?>',
                'card': '<?php echo e(__('common.bank_card')); ?>',
                'transfer': '<?php echo e(__('common.bank_transfer')); ?>',
                'other': '<?php echo e(__('common.other')); ?>'
            };
            return labels[method] || method;
        },
        
        // Сохранение
        async saveAthlete() {
            const athleteData = {
                name: this.formName,
                email: this.formEmail,
                password: this.formPassword,
                phone: this.formPhone,
                birth_date: this.formBirthDate,
                gender: this.formGender,
                sport_level: this.formSportLevel,
                goals: this.formGoals,
                health_restrictions: this.formHealthRestrictions
            };
            
            try {
                const url = this.currentAthlete ? `/trainer/athletes/${this.currentAthlete.id}` : '/trainer/athletes';
                const method = this.currentAthlete ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(athleteData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: this.currentAthlete && this.currentAthlete.id ? '<?php echo e(__('common.athlete_updated')); ?>' : '<?php echo e(__('common.athlete_added')); ?>',
                            message: this.currentAthlete && this.currentAthlete.id ? 
                                '<?php echo e(__('common.athlete_data_updated')); ?>' : 
                                '<?php echo e(__('common.athlete_successfully_added')); ?>'
                        }
                    }));
                    
                    // Обновляем список спортсменов
                    if (this.currentAthlete && this.currentAthlete.id) {
                        // Редактирование - обновляем существующую
                        const index = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                        if (index !== -1) {
                            this.athletes[index] = { ...this.currentAthlete, ...athleteData };
                        }
                    } else {
                        // Создание - добавляем новую
                        this.athletes.unshift(result.athlete);
                    }
                    
                    // Переключаемся на список
                    this.showList();
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.saving_error')); ?>',
                            message: result.message || '<?php echo e(__('common.athlete_saving_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.athlete_saving_error')); ?>'
                    }
                }));
            }
        },
        
        // Обновление
        async createAthlete() {
            try {
                const athleteData = {
                    name: this.formName,
                    email: this.formEmail,
                    password: this.formPassword,
                    phone: this.formPhone,
                    birth_date: this.formBirthDate,
                    gender: this.formGender,
                    sport_level: this.formSportLevel,
                    goals: this.formGoals,
                    is_active: this.formIsActive === '1',
                    trainer_id: <?php echo e(auth()->id()); ?>

                };
                
                const response = await fetch('<?php echo e(route("crm.trainer.store-athlete")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(athleteData)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Добавляем нового спортсмена в список
                    this.athletes.unshift(result.athlete);
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.success')); ?>',
                            message: '<?php echo e(__('common.athlete_successfully_created')); ?>'
                        }
                    }));
                    
                    // Возвращаемся к списку
                    this.currentView = 'list';
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.error')); ?>',
                            message: result.message || '<?php echo e(__('common.athlete_creation_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка создания спортсмена:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.athlete_creation_error')); ?>'
                    }
                }));
            }
        },
        
        async updateAthlete() {
            try {
                // Показываем загрузку
                this.isLoading = true;
                
                const requestData = {
                    name: this.formName,
                    email: this.formEmail,
                    phone: this.formPhone,
                    age: this.formAge,
                    weight: this.formWeight,
                    height: this.formHeight,
                    gender: this.formGender,
                    birth_date: this.formBirthDate,
                    password: this.formPassword || null
                };
                
                // console.log('Отправляем данные:', requestData);
                // console.log('URL:', `/trainer/athletes/${this.currentAthlete.id}`);
                
                // Отправляем AJAX запрос
                const response = await fetch(`/trainer/athletes/${this.currentAthlete.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });
                
                // console.log('Статус ответа:', response.status);
                // console.log('Заголовки ответа:', response.headers);
                
                const responseText = await response.text();
                // console.log('Ответ сервера:', responseText);
                
                if (response.ok) {
                    const responseData = JSON.parse(responseText);
                    
                    // Обновляем данные в текущем объекте
                    this.currentAthlete.name = this.formName;
                    this.currentAthlete.email = this.formEmail;
                    this.currentAthlete.phone = this.formPhone;
                    this.currentAthlete.age = this.formAge;
                    this.currentAthlete.weight = this.formWeight;
                    this.currentAthlete.height = this.formHeight;
                    this.currentAthlete.gender = this.formGender;
                    this.currentAthlete.birth_date = this.formBirthDate;
                    
                    // Обновляем данные в списке спортсменов
                    const athleteIndex = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                    if (athleteIndex !== -1) {
                        this.athletes[athleteIndex].name = this.formName;
                        this.athletes[athleteIndex].email = this.formEmail;
                        this.athletes[athleteIndex].phone = this.formPhone;
                        this.athletes[athleteIndex].age = this.formAge;
                        this.athletes[athleteIndex].weight = this.formWeight;
                        this.athletes[athleteIndex].height = this.formHeight;
                        this.athletes[athleteIndex].gender = this.formGender;
                        this.athletes[athleteIndex].birth_date = this.formBirthDate;
                    }
                    
                    // Перезагружаем связанные данные (измерения, прогресс и т.д.)
                    await this.loadMeasurements(this.currentAthlete.id);
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.success')); ?>',
                            message: '<?php echo e(__('common.athlete_data_updated_success')); ?>'
                        }
                    }));
                    
                    // Возвращаемся к карточке спортсмена
                    this.currentView = 'view';
                    this.activeTab = 'overview';
                } else {
                    console.error('<?php echo e(__('common.server_error_status')); ?>:', response.status, responseText);
                    throw new Error(`Ошибка сервера: ${response.status} - ${responseText}`);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: `Не удалось обновить данные спортсмена: ${error.message}`
                    }
                }));
            } finally {
                this.isLoading = false;
            }
        },
        
        // Сохранение измерений
        async saveMeasurement() {
            try {
                const measurementData = {
                    measurement_date: this.measurementDate,
                    weight: this.measurementWeight ? parseFloat(this.measurementWeight) : null,
                    height: this.measurementHeight ? parseFloat(this.measurementHeight) : null,
                    body_fat_percentage: this.measurementBodyFat ? parseFloat(this.measurementBodyFat) : null,
                    muscle_mass: this.measurementMuscleMass ? parseFloat(this.measurementMuscleMass) : null,
                    water_percentage: this.measurementWaterPercentage ? parseFloat(this.measurementWaterPercentage) : null,
                    chest: this.measurementChest ? parseFloat(this.measurementChest) : null,
                    waist: this.measurementWaist ? parseFloat(this.measurementWaist) : null,
                    hips: this.measurementHips ? parseFloat(this.measurementHips) : null,
                    bicep: this.measurementBicep ? parseFloat(this.measurementBicep) : null,
                    thigh: this.measurementThigh ? parseFloat(this.measurementThigh) : null,
                    neck: this.measurementNeck ? parseFloat(this.measurementNeck) : null,
                    resting_heart_rate: this.measurementHeartRate ? parseInt(this.measurementHeartRate) : null,
                    blood_pressure_systolic: this.measurementBloodPressureSystolic ? parseInt(this.measurementBloodPressureSystolic) : null,
                    blood_pressure_diastolic: this.measurementBloodPressureDiastolic ? parseInt(this.measurementBloodPressureDiastolic) : null,
                    notes: this.measurementNotes || '',
                };
                
                const isEdit = this.currentMeasurement !== null;
                const url = isEdit 
                    ? `/trainer/athletes/${this.currentAthlete.id}/measurements/${this.currentMeasurement.id}`
                    : `/trainer/athletes/${this.currentAthlete.id}/measurements`;
                const method = isEdit ? 'PUT' : 'POST';
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(measurementData)
                });
                
                const result = await response.json();
                
                // Отладочная информация
                // console.log('Ответ сервера:', result);
                // console.log('Данные измерения:', result.measurement);
                
                if (response.ok) {
                    if (isEdit) {
                        // Обновляем существующее измерение в массиве
                        const index = this.measurements.findIndex(m => m.id === this.currentMeasurement.id);
                        if (index !== -1) {
                            this.measurements[index] = result.measurement;
                        }
                    } else {
                        // Добавляем новое измерение в массив
                        this.measurements.unshift(result.measurement);
                    }
                    
                    // Обновляем вес и рост в профиле спортсмена
                    if (result.measurement.weight) {
                        this.currentAthlete.weight = result.measurement.weight;
                        // console.log('Обновлен вес в профиле:', this.currentAthlete.weight);
                    }
                    if (result.measurement.height) {
                        this.currentAthlete.height = result.measurement.height;
                        // console.log('Обновлен рост в профиле:', this.currentAthlete.height);
                    }
                    
                    // Обновляем спортсмена в общем списке
                    const athleteIndex = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                    if (athleteIndex !== -1) {
                        this.athletes[athleteIndex].weight = this.currentAthlete.weight;
                        this.athletes[athleteIndex].height = this.currentAthlete.height;
                        // console.log('Обновлен спортсмен в списке:', this.athletes[athleteIndex]);
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.success')); ?>',
                            message: isEdit ? '<?php echo e(__('common.measurement_updated')); ?>' : '<?php echo e(__('common.measurements_saved')); ?>'
                        }
                    }));
                    
                    // Возвращаемся к просмотру спортсмена на вкладке измерений
                    this.currentView = 'view';
                    this.activeTab = 'measurements';
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.saving_error')); ?>',
                            message: result.message || '<?php echo e(__('common.measurement_saving_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.measurement_saving_error')); ?>'
                    }
                }));
            }
        },
        
        // Извлечение количества тренировок из описания платежа
        extractSessionsFromDescription(description) {
            if (!description) {
                return 0;
            }
            
            // Основной вариант: "4 тренировки", "8 тренировок", "12 тренировок" (RU)
            const mainMatch = description.match(/(\d+)\s+(?:тренировка|тренировки|тренировок)/i);
            if (mainMatch) {
                return parseInt(mainMatch[1]);
            }
            
            // Вариант с "тренирки"
            const tMatch = description.match(/(\d+)\s*тренирки/i);
            if (tMatch) {
                return parseInt(tMatch[1]);
            }
            
            // Украинский вариант: "4 тренування", "8 тренувань", "12 тренувань"
            const ukMatch = description.match(/(\d+)\s+(?:тренування|тренувань)/i);
            if (ukMatch) {
                return parseInt(ukMatch[1]);
            }
            
            // Английский вариант: "4 workouts", "8 workouts", "12 workouts"
            const enMatch = description.match(/(\d+)\s+workouts?/i);
            if (enMatch) {
                return parseInt(enMatch[1]);
            }
            
            // Разовая тренировка (RU)
            if (/Разовая\s*тренировка/i.test(description)) {
                return 1;
            }
            
            // Разове тренування (UA)
            if (/Разове\s*тренування/i.test(description)) {
                return 1;
            }
            
            // Single workout (EN)
            if (/Single\s*workout/i.test(description)) {
                return 1;
            }
            
            // Безлимит (RU)
            if (/Безлимит/i.test(description)) {
                return 30;
            }
            
            // Безліміт (UA)
            if (/Безліміт/i.test(description)) {
                return 30;
            }
            
            // Unlimited (EN)
            if (/Unlimited/i.test(description)) {
                return 30;
            }
            
            return 0;
        },
        
        // Сохранение платежа
        async savePayment() {
            try {
                const paymentData = {
                    package_type: this.paymentData.package_type,
                    total_sessions: parseInt(this.paymentData.total_sessions),
                    used_sessions: parseInt(this.paymentData.used_sessions) || 0,
                    package_price: parseFloat(this.paymentData.package_price),
                    purchase_date: this.paymentData.purchase_date,
                    expires_date: this.paymentData.expires_date,
                    payment_method: this.paymentData.payment_method,
                    description: this.paymentData.description
                };
                
                // Определяем URL и метод в зависимости от того, редактируем или создаем
                const isEdit = this.paymentData.id && this.paymentData.id !== this.currentAthlete.id;
                const url = isEdit 
                    ? `/api/athletes/${this.currentAthlete.id}/payments/${this.paymentData.id}`
                    : `/api/athletes/${this.currentAthlete.id}/payments`;
                const method = isEdit ? 'PUT' : 'POST';
                
                // console.log('Sending request to:', url);
                // console.log('Method:', method);
                // console.log('Data:', paymentData);
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(paymentData)
                });
                
                // console.log('Response status:', response.status);
                // console.log('Response headers:', response.headers);
                
                let result;
                try {
                    result = await response.json();
                } catch (error) {
                    console.error('JSON parse error:', error);
                    const text = await response.text();
                    console.error('Response text:', text);
                    throw new Error('Сервер вернул неверный ответ. Проверьте консоль для подробностей.');
                }
                
                if (response.ok) {
                    // Обновляем данные спортсмена из ответа сервера
                    if (result.data) {
                        // API возвращает данные из TrainerFinance, нужно обновить finance объект
                        const financeData = result.data;
                        const paymentHistory = financeData.payment_history || [];
                        
                        // Обновляем finance объект в currentAthlete
                        if (this.currentAthlete && this.currentAthlete.finance) {
                            this.currentAthlete.finance = {
                                id: this.currentAthlete.id,
                                package_type: financeData.package_type,
                                total_sessions: financeData.total_sessions,
                                used_sessions: financeData.used_sessions,
                                remaining_sessions: financeData.total_sessions - financeData.used_sessions,
                                package_price: financeData.package_price,
                                purchase_date: financeData.purchase_date,
                                expires_date: financeData.expires_date,
                                status: financeData.total_sessions > 0 ? 'active' : 'inactive',
                                total_paid: financeData.total_paid,
                                last_payment_date: financeData.last_payment_date,
                                payment_history: paymentHistory
                            };
                        }
                        
                        console.log('Updated finance data:', this.currentAthlete?.finance);
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.success')); ?>',
                            message: isEdit ? '<?php echo e(__('common.payment_updated')); ?>' : '<?php echo e(__('common.payment_saved')); ?>'
                        }
                    }));
                    
                    // Возвращаемся к просмотру спортсмена на вкладке финансов
                    this.currentView = 'view';
                    this.activeTab = 'finance';
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.saving_error')); ?>',
                            message: result.message || '<?php echo e(__('common.payment_saving_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.payment_saving_error')); ?>'
                    }
                }));
            }
        },
        
        // Редактирование платежа
        editPayment(payment) {
            this.currentView = 'editPayment';
            this.paymentData = {
                id: payment.id,
                package_type: payment.package_type || '',
                total_sessions: payment.total_sessions || 0,
                used_sessions: payment.used_sessions || 0,
                package_price: payment.package_price || 0,
                purchase_date: payment.purchase_date || '',
                expires_date: payment.expires_date || '',
                payment_method: payment.payment_method || '<?php echo e(__('common.cash')); ?>',
                description: payment.description || ''
            };
        },
        
        editPaymentFromHistory(payment) {
            // Редактируем конкретный платеж из истории
            this.currentView = 'editPayment';
            
            // Форматируем даты для input type="date"
            const formatDateForInput = (dateString) => {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toISOString().split('T')[0];
            };
            
            this.paymentData = {
                id: payment.id, // ID конкретного платежа!
                package_type: this.currentAthlete?.finance?.package_type || '',
                total_sessions: this.currentAthlete?.finance?.total_sessions || 0,
                used_sessions: this.currentAthlete?.finance?.used_sessions || 0,
                package_price: payment.amount || 0,
                purchase_date: formatDateForInput(payment.date),
                expires_date: formatDateForInput(this.currentAthlete?.finance?.expires_date),
                payment_method: payment.payment_method || '<?php echo e(__('common.cash')); ?>',
                description: payment.description || ''
            };
        },
        
        // Удаление платежа
        async deletePayment(paymentId) {
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete_package_title')); ?>',
                    message: '<?php echo e(__('common.delete_package_confirm')); ?>',
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
                    onConfirm: () => this.performDeletePayment(paymentId)
                }
            }));
        },
        
        // Выполнение удаления платежа
        async performDeletePayment(paymentId) {
            try {
                const response = await fetch(`/api/athletes/${this.currentAthlete.id}/payments/${paymentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Обновляем данные спортсмена из ответа сервера
                    if (result.data) {
                        // API возвращает данные из TrainerFinance, нужно обновить finance объект
                        const financeData = result.data;
                        const paymentHistory = financeData.payment_history || [];
                        
                        // Обновляем finance объект в currentAthlete
                        if (this.currentAthlete && this.currentAthlete.finance) {
                            if (paymentHistory.length > 0) {
                                this.currentAthlete.finance = {
                                    id: this.currentAthlete.id,
                                    package_type: financeData.package_type,
                                    total_sessions: financeData.total_sessions,
                                    used_sessions: financeData.used_sessions,
                                    remaining_sessions: financeData.total_sessions - financeData.used_sessions,
                                    package_price: financeData.package_price,
                                    purchase_date: financeData.purchase_date,
                                    expires_date: financeData.expires_date,
                                    status: financeData.total_sessions > 0 ? 'active' : 'inactive',
                                    total_paid: financeData.total_paid,
                                    last_payment_date: financeData.last_payment_date,
                                    payment_history: paymentHistory
                                };
                            } else {
                                // Если платежей не осталось, обнуляем финансовые данные
                                this.currentAthlete.finance = {
                                    id: this.currentAthlete.id,
                                    package_type: null,
                                    total_sessions: 0,
                                    used_sessions: 0,
                                    remaining_sessions: 0,
                                    package_price: 0,
                                    purchase_date: null,
                                    expires_date: null,
                                    status: 'inactive',
                                    total_paid: 0,
                                    last_payment_date: null,
                                    payment_history: []
                                };
                            }
                        }
                        
                        console.log('Updated finance data after delete:', this.currentAthlete?.finance);
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.payment_deleted')); ?>',
                            message: '<?php echo e(__('common.payment_successfully_deleted')); ?>'
                        }
                    }));
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.delete_error')); ?>',
                            message: result.message || '<?php echo e(__('common.payment_delete_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.delete_package_error')); ?>'
                    }
                }));
            }
        },
        
        // Удаление измерения
        async deleteMeasurement(measurementId) {
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete_measurement_title')); ?>',
                    message: '<?php echo e(__('common.delete_measurement_confirm')); ?>',
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
                    onConfirm: () => this.performDeleteMeasurement(measurementId)
                }
            }));
        },
        
        async performDeleteMeasurement(measurementId) {
            try {
                const response = await fetch(`/trainer/athletes/${this.currentAthlete.id}/measurements/${measurementId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    // Удаляем измерение из массива
                    this.measurements = this.measurements.filter(m => m.id !== measurementId);
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.measurement_deleted')); ?>',
                            message: '<?php echo e(__('common.measurement_successfully_deleted')); ?>'
                        }
                    }));
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.delete_error')); ?>',
                            message: result.message || '<?php echo e(__('common.measurement_delete_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.measurement_delete_error')); ?>'
                    }
                }));
            }
        },
        
        // Удаление
        deleteAthlete(id) {
            const athlete = this.athletes.find(a => a.id === id);
            const athleteName = athlete ? athlete.name : '<?php echo e(__('common.athlete_name_default')); ?>';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: '<?php echo e(__('common.delete_athlete_title')); ?>',
                    message: `Вы уверены, что хотите удалить спортсмена "${athleteName}"?`,
                    confirmText: '<?php echo e(__('common.delete')); ?>',
                    cancelText: '<?php echo e(__('common.cancel')); ?>',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/trainer/athletes/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok && result.success) {
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: '<?php echo e(__('common.athlete_deleted')); ?>',
                            message: result.message || '<?php echo e(__('common.athlete_successfully_deleted')); ?>'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.athletes = this.athletes.filter(a => a.id !== id);
                    this.totalAthletes = this.athletes.length;
                    
                    // Если удалили всех спортсменов на текущей странице, переходим на предыдущую
                    if (this.paginatedAthletes.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                    
                    // Если это был текущий просматриваемый спортсмен, закрываем просмотр
                    if (this.currentAthlete && this.currentAthlete.id === id) {
                        this.showList();
                    }
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: '<?php echo e(__('common.delete_error')); ?>',
                            message: result.message || '<?php echo e(__('common.athlete_delete_error')); ?>'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                // Показываем уведомление об ошибке
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: '<?php echo e(__('common.error')); ?>',
                        message: '<?php echo e(__('common.athlete_delete_error')); ?>'
                    }
                }));
            }
        }
    }
}
</script>

<?php $__env->startSection("sidebar"); ?>
    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        <?php echo e(__('common.dashboard')); ?>

    </a>
    <a href="<?php echo e(route('crm.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="<?php echo e(route("crm.workouts.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <?php echo e(__('common.workouts')); ?>

    </a>
    <a href="<?php echo e(route("crm.exercises.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Упражнения
    </a>
    <a href="<?php echo e(route("crm.workout-templates.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Шаблоны тренировок
    </a>
    <a href="<?php echo e(route("crm.progress.index")); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="<?php echo e(route("crm.trainer.athletes")); ?>" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Спортсмены
    </a>
    <a href="<?php echo e(route('crm.trainer.settings')); ?>" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>
<div x-data="athletesApp()" x-cloak class="space-y-6">
    
    <!-- Фильтры и поиск -->
    <div x-show="currentView === 'list'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
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
                           placeholder="<?php echo e(__('common.search_athletes')); ?>" 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр уровня -->
                <div class="status-container">
                    <select x-model="sportLevel" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value=""><?php echo e(__('common.all_levels')); ?></option>
                        <option value="beginner"><?php echo e(__('common.novice')); ?></option>
                        <option value="intermediate"><?php echo e(__('common.amateur')); ?></option>
                        <option value="advanced"><?php echo e(__('common.pro')); ?></option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    <button @click="showCreate()" 
                            class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                        <?php echo e(__('common.add_athlete')); ?>

                    </button>
                </div>
            </div>
            
            <!-- Активные фильтры -->
            <div x-show="search || sportLevel" class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-500"><?php echo e(__('common.active_filters')); ?>:</span>
                    
                    <span x-show="search" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Поиск: "<span x-text="search"></span>"
                        <button @click="search = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                    </span>
                    
                    <span x-show="sportLevel" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        <?php echo e(__('common.level')); ?>: <span x-text="getSportLevelLabel(sportLevel)"></span>
                        <button @click="sportLevel = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- СПИСОК СПОРТСМЕНОВ -->
    <div x-show="currentView === 'list'" class="space-y-4">
        <template x-for="athlete in paginatedAthletes" :key="athlete.id">
            <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-indigo-200 overflow-hidden">
                <!-- Уровень индикатор -->
                <div class="absolute top-0 left-0 w-full h-1" 
                     :class="{
                         'bg-green-500': athlete.sport_level === 'advanced',
                         'bg-yellow-500': athlete.sport_level === 'intermediate',
                         'bg-blue-500': athlete.sport_level === 'beginner'
                     }">
                </div>
                
                <div class="p-6">
                    <!-- Десктоп: все в одну строку -->
                    <div class="desktop-version flex items-center justify-between">
                        <!-- Левая часть: аватарка, имя и данные -->
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Аватарка спортсмена -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-semibold text-lg">
                                    <span x-text="(athlete.name || '?').charAt(0).toUpperCase()"></span>
                                </div>
                            </div>
                            
                            <!-- Имя и данные в одной строке -->
                            <div class="flex-1">
                                <div class="flex items-center flex-wrap gap-4">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors" x-text="athlete.name"></h3>
                                    <span x-show="athlete.age" class="text-sm text-gray-500" x-text="'<?php echo e(__('common.age')); ?>: ' + athlete.age + ' <?php echo e(__('common.years')); ?>'"></span>
                                    <span x-show="athlete.current_weight" class="text-sm text-gray-500" x-text="'<?php echo e(__('common.weight')); ?>: ' + formatNumber(athlete.current_weight, ' <?php echo e(__('common.weight_change_kg')); ?>')"></span>
                                    <span x-show="athlete.current_height" class="text-sm text-gray-500" x-text="'<?php echo e(__('common.height')); ?>: ' + formatNumber(athlete.current_height, ' см')"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Центральная часть: кнопки -->
                        <div class="flex items-center space-x-2 mx-4">
                            <button @click="showView(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                <?php echo e(__('common.view')); ?>

                            </button>
                            <button @click="showEdit(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                <?php echo e(__('common.edit')); ?>

                            </button>
                            <button @click="deleteAthlete(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                <?php echo e(__('common.delete')); ?>

                            </button>
                        </div>
                        
                        <!-- Правая часть: уровень -->
                        <div class="flex-shrink-0">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                  :class="{
                                      'bg-green-100 text-green-800': athlete.sport_level === 'advanced',
                                      'bg-yellow-100 text-yellow-800': athlete.sport_level === 'intermediate',
                                      'bg-blue-100 text-blue-800': athlete.sport_level === 'beginner'
                                  }"
                                  x-text="getSportLevelLabel(athlete.sport_level)">
                            </span>
                        </div>
                    </div>

                    <!-- Мобилка: вертикальная раскладка -->
                    <div class="mobile-version" style="display: none;">
                        <!-- Верхняя часть: аватарка, имя, данные и уровень -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <!-- Аватарка спортсмена -->
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg text-white font-semibold text-lg">
                                        <span x-text="(athlete.name || '?').charAt(0).toUpperCase()"></span>
                                    </div>
                                </div>
                                
                                <!-- Имя и данные -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors" x-text="athlete.name"></h3>
                                    <div class="flex flex-wrap gap-2 text-sm text-gray-500 mt-1">
                                        <span x-show="athlete.age" x-text="'<?php echo e(__('common.age')); ?>: ' + athlete.age + ' <?php echo e(__('common.years')); ?>'"></span>
                                        <span x-show="athlete.current_weight" x-text="'<?php echo e(__('common.weight')); ?>: ' + formatNumber(athlete.current_weight, ' <?php echo e(__('common.weight_change_kg')); ?>')"></span>
                                        <span x-show="athlete.current_height" x-text="'<?php echo e(__('common.height')); ?>: ' + formatNumber(athlete.current_height, ' см')"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Уровень -->
                            <div class="flex-shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-green-100 text-green-800': athlete.sport_level === 'advanced',
                                          'bg-yellow-100 text-yellow-800': athlete.sport_level === 'intermediate',
                                          'bg-blue-100 text-blue-800': athlete.sport_level === 'beginner'
                                      }"
                                      x-text="getSportLevelLabel(athlete.sport_level)">
                                </span>
                            </div>
                        </div>
                        
                        <!-- Нижняя часть: кнопки -->
                        <div class="flex space-x-2">
                            <button @click="showView(athlete.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                <?php echo e(__('common.view')); ?>

                            </button>
                            <button @click="showEdit(athlete.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                <?php echo e(__('common.edit')); ?>

                            </button>
                            <button @click="deleteAthlete(athlete.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                <?php echo e(__('common.delete')); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Пагинация -->
        <div x-show="filteredAthletes.length > 0 && totalPages > 1" class="mt-6">
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
        <div x-show="filteredAthletes.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                <span class="text-3xl text-gray-400">👥</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo e(__('common.no_athletes')); ?></h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto"><?php echo e(__('common.no_athletes_text')); ?></p>
            <button @click="showCreate()" 
                    class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.add_first_athlete')); ?>

            </button>
        </div>
    </div>

    <!-- ДОБАВЛЕНИЕ ПЛАТЕЖА -->
    <div x-show="currentView === 'addPayment'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900"><?php echo e(__('common.add_payment')); ?></h3>
            <button @click="currentView = 'view'; activeTab = 'finance'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="savePayment" class="space-y-6">
                <!-- Информация о пакете -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.package_info')); ?></h4>
                    <div class="package-info-grid">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.package_type')); ?></label>
                            <select x-model="paymentData.package_type" 
                                    @change="updateSessionsByPackageType()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value=""><?php echo e(__('common.select_package_type')); ?></option>
                                <option value="single"><?php echo e(__('common.single_workout')); ?></option>
                                <option value="4_sessions"><?php echo e(__('common.4_workouts')); ?></option>
                                <option value="8_sessions"><?php echo e(__('common.8_workouts')); ?></option>
                                <option value="12_sessions"><?php echo e(__('common.12_workouts')); ?></option>
                                <option value="unlimited"><?php echo e(__('common.unlimited_month')); ?></option>
                                <option value="custom"><?php echo e(__('common.custom_amount')); ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.workouts_count_label')); ?></label>
                            <input type="number" x-model="paymentData.total_sessions" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="0"
                                   :disabled="paymentData.package_type && paymentData.package_type !== 'custom'">
                            <p class="text-xs text-gray-500 mt-1" x-show="paymentData.package_type && paymentData.package_type !== 'custom'">
                                <?php echo e(__('common.auto_filled_by_package_type')); ?>

                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.cost_uah')); ?></label>
                            <input type="number" x-model="paymentData.package_price" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.payment_method')); ?></label>
                            <select x-model="paymentData.payment_method" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="cash"><?php echo e(__('common.cash')); ?></option>
                                <option value="card"><?php echo e(__('common.bank_card')); ?></option>
                                <option value="transfer"><?php echo e(__('common.bank_transfer')); ?></option>
                                <option value="other"><?php echo e(__('common.other')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Даты -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.dates')); ?></h4>
                    <div class="package-info-grid">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.purchase_date')); ?></label>
                            <input type="date" x-model="paymentData.purchase_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.validity_period')); ?></label>
                            <input type="date" x-model="paymentData.expires_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1"><?php echo e(__('common.validity_period_help')); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.additional_info')); ?></h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.description')); ?></label>
                            <textarea x-model="paymentData.description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="<?php echo e(__('common.payment_additional_info')); ?>"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end gap-4 pt-6 border-t">
                    <button type="button" @click="currentView = 'view'; activeTab = 'finance'" 
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        <?php echo e(__('common.save_payment')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- РЕДАКТИРОВАНИЕ ПЛАТЕЖА -->
    <div x-show="currentView === 'editPayment'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900"><?php echo e(__('common.edit_payment')); ?></h3>
            <button @click="currentView = 'view'; activeTab = 'finance'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="savePayment" class="space-y-6">
                <!-- Информация о пакете -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.package_info')); ?></h4>
                    <div class="package-info-grid">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.package_type')); ?></label>
                            <select x-model="paymentData.package_type" 
                                    @change="updateSessionsByPackageType()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value=""><?php echo e(__('common.select_package_type')); ?></option>
                                <option value="single"><?php echo e(__('common.single_workout')); ?></option>
                                <option value="4_sessions"><?php echo e(__('common.4_workouts')); ?></option>
                                <option value="8_sessions"><?php echo e(__('common.8_workouts')); ?></option>
                                <option value="12_sessions"><?php echo e(__('common.12_workouts')); ?></option>
                                <option value="unlimited"><?php echo e(__('common.unlimited_month')); ?></option>
                                <option value="custom"><?php echo e(__('common.custom_amount')); ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.workouts_count_label')); ?></label>
                            <input type="number" x-model="paymentData.total_sessions" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="0"
                                   :disabled="paymentData.package_type && paymentData.package_type !== 'custom'">
                            <p class="text-xs text-gray-500 mt-1" x-show="paymentData.package_type && paymentData.package_type !== 'custom'">
                                <?php echo e(__('common.auto_filled_by_package_type')); ?>

                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.used_workouts')); ?></label>
                            <input type="number" x-model="paymentData.used_sessions" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.cost_uah')); ?></label>
                            <input type="number" x-model="paymentData.package_price" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="0">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.payment_method')); ?></label>
                            <select x-model="paymentData.payment_method" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="cash"><?php echo e(__('common.cash')); ?></option>
                                <option value="card"><?php echo e(__('common.bank_card')); ?></option>
                                <option value="transfer"><?php echo e(__('common.bank_transfer')); ?></option>
                                <option value="other"><?php echo e(__('common.other')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Даты -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.dates')); ?></h4>
                    <div class="package-info-grid">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.purchase_date')); ?></label>
                            <input type="date" x-model="paymentData.purchase_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.validity_period')); ?></label>
                            <input type="date" x-model="paymentData.expires_date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1"><?php echo e(__('common.validity_period_help')); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.additional_info')); ?></h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.description')); ?></label>
                            <textarea x-model="paymentData.description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="<?php echo e(__('common.payment_additional_info')); ?>"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex justify-end gap-4 pt-6 border-t">
                    <button type="button" @click="currentView = 'view'; activeTab = 'finance'" 
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        <?php echo e(__('common.save_changes')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ПРОСМОТР СПОРТСМЕНА -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div x-show="currentAthlete">
            <!-- Заголовок карточки -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo e(__('common.athlete_profile')); ?></h1>
                    <button @click="showList()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                        <?php echo e(__('common.back')); ?> к списку
                    </button>
                </div>

                <!-- Карточка спортсмена с вкладками -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Заголовок карточки -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center">
                            <!-- Аватар -->
                            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mr-6">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            
                            <!-- Основная информация -->
                                <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900" x-text="currentAthlete?.name"></h2>
                                <div class="athlete-profile-data text-sm text-gray-500 mt-2">
                                    <div class="athlete-profile-item">
                                        <span class="athlete-profile-label"><?php echo e(__('common.age')); ?>:</span>
                                        <span class="athlete-profile-value" x-text="currentAthlete?.age || '—'"></span>
                                    </div>
                                    <div class="athlete-profile-item">
                                        <span class="athlete-profile-label"><?php echo e(__('common.weight')); ?>:</span>
                                        <span class="athlete-profile-value" x-show="!loadingAthleteData" x-text="getCurrentWeight()"></span>
                                        <span class="athlete-profile-value animate-pulse bg-gray-200 rounded" x-show="loadingAthleteData" style="width: 60px; height: 16px;"></span>
                                    </div>
                                    <div class="athlete-profile-item">
                                        <span class="athlete-profile-label"><?php echo e(__('common.height')); ?>:</span>
                                        <span class="athlete-profile-value" x-show="!loadingAthleteData" x-text="getCurrentHeight()"></span>
                                        <span class="athlete-profile-value animate-pulse bg-gray-200 rounded" x-show="loadingAthleteData" style="width: 60px; height: 16px;"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Быстрая статистика -->
                            <div class="text-right">
                                <div class="text-3xl font-bold" 
                                     :class="(measurements.length > 0 && measurements[0].weight && measurements[0].height) ? getBMICategory(measurements[0].weight / Math.pow(measurements[0].height/100, 2)).color : (currentAthlete?.weight && currentAthlete?.height ? getBMICategory(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)).color : 'text-gray-500')"
                                     x-text="(measurements.length > 0 && measurements[0].weight && measurements[0].height) ? formatNumber(measurements[0].weight / Math.pow(measurements[0].height/100, 2)) : (currentAthlete?.weight && currentAthlete?.height ? formatNumber(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)) : '—')"></div>
                                <div class="text-sm text-gray-500 flex items-center justify-end gap-1">
                                    <span>ИМТ</span>
                                    <!-- Иконка знака вопроса с подсказкой -->
                                    <div class="relative group">
                                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3 3 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                        </svg>
                                        <!-- Всплывающая подсказка -->
                                        <div class="absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            <div class="font-semibold mb-2"><?php echo e(__('common.bmi_index')); ?></div>
                                            <div class="space-y-1">
                                                <div class="flex justify-between"><span class="text-blue-300">Менее 18.5:</span> <span><?php echo e(__('common.bmi_categories.underweight')); ?></span></div>
                                                <div class="flex justify-between"><span class="text-green-300">18.5 - 24.9:</span> <span><?php echo e(__('common.bmi_categories.normal')); ?></span></div>
                                                <div class="flex justify-between"><span class="text-yellow-300">25 - 29.9:</span> <span><?php echo e(__('common.bmi_categories.overweight')); ?></span></div>
                                                <div class="flex justify-between"><span class="text-red-300">30 и более:</span> <span><?php echo e(__('common.bmi_categories.obese')); ?></span></div>
                                            </div>
                                            <div class="mt-2 pt-2 border-t border-gray-700 text-gray-300">
                                                <div x-text="(measurements.length > 0 && measurements[0].weight && measurements[0].height) ? '<?php echo e(__('common.your_bmi_calculated')); ?>: ' + formatNumber(measurements[0].weight / Math.pow(measurements[0].height/100, 2)) + ' (' + getBMICategory(measurements[0].weight / Math.pow(measurements[0].height/100, 2)).text + ')' : (currentAthlete?.weight && currentAthlete?.height ? '<?php echo e(__('common.your_bmi_calculated')); ?>: ' + formatNumber(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)) + ' (' + getBMICategory(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)).text + ')' : '<?php echo e(__('common.bmi_not_calculated')); ?>')"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                </div>
                            </div>

                    <!-- Вкладки -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6 overflow-x-auto scrollbar-hide" aria-label="Tabs" style="scrollbar-width: none; -ms-overflow-style: none;">
                            <button @click="activeTab = 'overview'" 
                                    :class="activeTab === 'overview' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.overview')); ?>

                                </button>
                            <button @click="activeTab = 'workouts'" 
                                    :class="activeTab === 'workouts' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.workouts')); ?>

                            </button>
                            <button @click="activeTab = 'progress'; $nextTick(() => updateCharts())" 
                                    :class="activeTab === 'progress' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.progress')); ?>

                            </button>
                            <button @click="activeTab = 'measurements'" 
                                    :class="activeTab === 'measurements' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.measurements')); ?>

                            </button>
                            <button @click="activeTab = 'nutrition'; loadNutritionPlans()" 
                                    :class="activeTab === 'nutrition' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.nutrition')); ?>

                            </button>
                            <button @click="activeTab = 'medical'" 
                                    :class="activeTab === 'medical' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.medical_data')); ?>

                            </button>
                            <button @click="activeTab = 'finance'" 
                                    :class="activeTab === 'finance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.finance')); ?>

                            </button>
                            <button @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                <?php echo e(__('common.general')); ?>

                            </button>
                        </nav>
                    </div>

                    <!-- Содержимое вкладок -->
                    <div class="p-6">
                        <!-- Вкладка "Обзор" -->
                        <div x-show="activeTab === 'overview'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600" x-text="currentAthlete?.progress?.length || 0"></div>
                                    <div class="text-sm text-green-800"><?php echo e(__('common.measurement_records_label')); ?></div>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600" x-text="(currentAthlete?.workouts || []).filter(workout => workout.status === 'completed').length"></div>
                                    <div class="text-sm text-blue-800"><?php echo e(__('common.workouts')); ?></div>
                                </div>
                                <div class="bg-orange-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-orange-600" x-text="(currentAthlete?.finance?.total_sessions || 0) - (currentAthlete?.workouts || []).filter(workout => workout.status === 'completed').length"></div>
                                    <div class="text-sm text-orange-800"><?php echo e(__('common.remaining')); ?></div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-purple-600" x-text="currentAthlete?.is_active ? '<?php echo e(__('common.yes')); ?>' : '<?php echo e(__('common.no')); ?>'"></div>
                                    <div class="text-sm text-purple-800"><?php echo e(__('common.active')); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка "Финансы" -->
                        <div x-show="activeTab === 'finance'" class="space-y-6">
                            <!-- Заголовок с кнопкой добавления -->
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.financial_data')); ?></h3>
                                <button @click="showAddPayment()" 
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <?php echo e(__('common.add_payment')); ?>

                                </button>
                            </div>


                            <!-- Общая статистика -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-4"><?php echo e(__('common.general_statistics')); ?></h4>
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600" x-text="(parseFloat(currentAthlete?.finance?.total_paid) || 0).toFixed(2)">0.00</div>
                                        <div class="text-sm text-gray-500"><?php echo e(__('common.total_paid')); ?></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-blue-600" x-text="currentAthlete?.finance?.payment_history?.length || 0">0</div>
                                        <div class="text-sm text-gray-500"><?php echo e(__('common.payments_count')); ?></div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-purple-600" x-text="currentAthlete?.finance?.last_payment_date ? new Date(currentAthlete.finance.last_payment_date).toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>') : '—'">—</div>
                                        <div class="text-sm text-gray-500"><?php echo e(__('common.last_payment')); ?></div>
                                    </div>
                                </div>
                            </div>

                            <!-- История платежей -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h4 class="text-md font-semibold text-gray-900 mb-4"><?php echo e(__('common.payment_history')); ?></h4>
                                <div class="space-y-3">
                                    <template x-for="(payment, index) in (currentAthlete?.finance?.payment_history || [])" :key="index">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900" x-text="payment.description"><?php echo e(__('common.package')); ?> 12 <?php echo e(__('common.workouts')); ?></div>
                                                <div class="text-sm text-gray-500" x-text="new Date(payment.date).toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>')">15.01.2024</div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-right">
                                                    <div class="font-semibold text-green-600" x-text="payment.amount + ' ₴'">12,000 ₴</div>
                                                    <div class="text-sm text-gray-500" x-text="getPaymentMethodText(payment.payment_method)">Карта</div>
                                                </div>
                                                <div class="flex space-x-1">
                                                    <button @click="editPaymentFromHistory(payment)" 
                                                            class="p-1 text-gray-400 hover:text-indigo-600 transition-colors"
                                                            title="<?php echo e(__('common.edit_payment')); ?>">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <button @click="deletePayment(payment.id)" 
                                                            class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                                            title="<?php echo e(__('common.delete_payment')); ?>">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Пустое состояние -->
                                    <div x-show="!currentAthlete?.finance?.payment_history?.length" class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        <p><?php echo e(__('common.no_payment_records')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка "Общие данные" -->
                        <div x-show="activeTab === 'general'" class="space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.personal_info')); ?></h3>
                                    <div class="package-info-grid">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500"><?php echo e(__('common.full_name')); ?></label>
                                            <div class="text-gray-900 font-medium" x-text="currentAthlete?.name"></div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500"><?php echo e(__('common.phone')); ?></label>
                                            <div class="text-gray-900" x-text="currentAthlete?.phone || '—'"></div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Возраст</label>
                                            <div class="text-gray-900 font-semibold" x-text="(currentAthlete?.age || '—') + ' <?php echo e(__('common.age_years')); ?>'"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.sports_info')); ?></h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500"><?php echo e(__('common.sport_level')); ?></label>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" 
                                                      x-text="currentAthlete?.sport_level || '<?php echo e(__('common.not_specified')); ?>'"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500"><?php echo e(__('common.status')); ?></label>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                      :class="currentAthlete?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                      x-text="currentAthlete?.is_active ? '<?php echo e(__('common.active')); ?>' : '<?php echo e(__('common.inactive')); ?>'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Остальные вкладки -->
                        <div x-show="activeTab === 'medical'" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Медицинские данные</h3>
                            <p class="mb-4"><?php echo e(__('common.medical_data_placeholder')); ?></p>
                        </div>

                        <div x-show="activeTab === 'measurements'" class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <?php echo e(__('common.body_measurements')); ?> (<span x-text="measurements.length"></span>)
                                </h3>
                                <button @click="showAddMeasurement()" 
                                        class="px-2 py-1 md:px-4 md:py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center text-xs md:text-base">
                                    <svg class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Добавить измерение
                                </button>
                            </div>
                            
                            <!-- История измерений -->
                            <div x-show="measurements.length > 0" class="space-y-4">
                                
                                <div class="measurements-grid">
                                    <template x-for="measurement in paginatedMeasurements" :key="measurement.id">
                                        <div class="card hover:shadow-lg transition-shadow duration-200">
                                            <div class="card-header">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="card-title text-lg" x-text="new Date(measurement.measurement_date).toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>')"></h4>
                                                    <div class="flex space-x-2">
                                                        <button @click="editMeasurement(measurement)" 
                                                                class="text-indigo-600 hover:text-indigo-800" 
                                                                title="<?php echo e(__('common.edit')); ?>">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>
                                                        <button @click="deleteMeasurement(measurement.id)" 
                                                                class="text-red-600 hover:text-red-800" 
                                                                title="<?php echo e(__('common.delete')); ?>">
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
                                                        <div class="text-xs text-blue-800"><?php echo e(__('common.weight_kg')); ?></div>
                                                    </div>
                                                    <div class="text-center p-3 rounded-lg" x-show="measurement.weight && measurement.height" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).bg">
                                                        <div class="text-xl font-bold" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).color" x-text="formatNumber(measurement.weight / Math.pow(measurement.height/100, 2), '')"></div>
                                                        <div class="text-xs" :class="getBMICategory(measurement.weight / Math.pow(measurement.height/100, 2)).color"><?php echo e(__('common.bmi')); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Объемы тела -->
                                                <template x-if="measurement.chest || measurement.waist || measurement.hips || measurement.bicep || measurement.thigh || measurement.neck">
                                                    <div class="mt-4 pt-4 pb-4 border-t border-b border-gray-200">
                                                        <h5 class="text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.body_volumes_label')); ?></h5>
                                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                                            <template x-if="measurement.chest">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.chest_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.chest, ' см')"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="measurement.waist">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.waist_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.waist, ' см')"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="measurement.hips">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.hips_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.hips, ' см')"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="measurement.bicep">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.bicep_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.bicep, ' см')"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="measurement.thigh">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.thigh_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.thigh, ' см')"></span>
                                                                </div>
                                                            </template>
                                                            <template x-if="measurement.neck">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-500"><?php echo e(__('common.neck_label')); ?>:</span>
                                                                    <span class="font-medium" x-text="formatNumber(measurement.neck, ' см')"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>
                                                
                                                <!-- <?php echo e(__('common.additional_params_label')); ?> -->
                                                <div class="mt-4">
                                                    <h5 class="text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.additional_params_label')); ?></h5>
                                                    <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500"><?php echo e(__('common.body_fat_label')); ?>:</span>
                                                        <span class="font-medium" x-text="measurement.body_fat_percentage ? formatNumber(measurement.body_fat_percentage, '%') : '—'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500"><?php echo e(__('common.muscle_mass_label')); ?>:</span>
                                                        <span class="font-medium" x-text="measurement.muscle_mass ? formatNumber(measurement.muscle_mass, ' кг') : '—'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500"><?php echo e(__('common.water_percentage_label')); ?>:</span>
                                                        <span class="font-medium" x-text="measurement.water_percentage ? formatNumber(measurement.water_percentage, '%') : '—'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500"><?php echo e(__('common.resting_heart_rate_label')); ?>:</span>
                                                        <span class="font-medium" x-text="measurement.resting_heart_rate ? Math.round(parseFloat(measurement.resting_heart_rate)) + ' <?php echo e(__('common.bpm')); ?>' : '—'"></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500"><?php echo e(__('common.blood_pressure_label')); ?>:</span>
                                                        <span class="font-medium" x-text="measurement.blood_pressure_systolic && measurement.blood_pressure_diastolic ? Math.round(parseFloat(measurement.blood_pressure_systolic)) + '/' + Math.round(parseFloat(measurement.blood_pressure_diastolic)) : '—'"></span>
                                                    </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Комментарии -->
                                                <template x-if="measurement.notes">
                                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                                        <h5 class="text-sm font-medium text-gray-700 mb-1"><?php echo e(__('common.comments_label')); ?></h5>
                                                        <p class="text-sm text-gray-600" x-text="measurement.notes"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Пагинация -->
                                <div class="pagination-wrapper" x-show="measurementsTotalPages > 1">
                                    <div class="pagination-container">
                                        <div class="pagination-nav">
                                            <!-- Предыдущая страница -->
                                            <button @click="measurementsPreviousPage()" 
                                                    :disabled="measurementsCurrentPage === 1"
                                                    :class="measurementsCurrentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
                                                    class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                            </button>
                                            
                                            <!-- Номера страниц -->
                                            <template x-for="page in measurementsVisiblePages" :key="page">
                                                <button @click="goToMeasurementsPage(page)" 
                                                        :class="page === measurementsCurrentPage ? 'bg-indigo-600 text-white border-indigo-600' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'"
                                                        class="px-3 py-2 text-sm font-medium border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                                    <span x-text="page"></span>
                                                </button>
                                            </template>
                                            
                                            <!-- Следующая страница -->
                                            <button @click="measurementsNextPage()" 
                                                    :disabled="measurementsCurrentPage === measurementsTotalPages"
                                                    :class="measurementsCurrentPage === measurementsTotalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'"
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
                            <div x-show="measurements.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo e(__('common.no_measurements')); ?></h3>
                                <p class="mb-4"><?php echo e(__('common.add_first_measurement')); ?></p>
                            </div>
                        </div>

                        <div x-show="activeTab === 'progress'" class="space-y-6">
                            <!-- Заголовок с фильтром периодов -->
                            <div class="progress-header flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.athlete_progress')); ?></h3>
                                <div class="progress-controls flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700"><?php echo e(__('common.period')); ?>:</span>
                                    <select x-model="selectedPeriod" @change="updatePeriodFilter()" class="progress-select px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="all"><?php echo e(__('common.all_data')); ?></option>
                                        <option value="1"><?php echo e(__('common.last_month')); ?></option>
                                        <option value="3"><?php echo e(__('common.last_3_months')); ?></option>
                                        <option value="6"><?php echo e(__('common.last_6_months')); ?></option>
                                        <option value="12"><?php echo e(__('common.last_year')); ?></option>
                                    </select>
                                    <span class="progress-count text-sm text-gray-500" x-text="getFilteredMeasurementsCount() + ' <?php echo e(__('common.measurements_count')); ?>'"></span>
                                </div>
                            </div>

                            <!-- Графики прогресса -->
                            <div x-show="measurements.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo e(__('common.no_measurements')); ?></h3>
                                <p class="mb-4"><?php echo e(__('common.add_measurements_for_progress')); ?></p>
                                <button @click="activeTab = 'measurements'" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <?php echo e(__('common.add_measurement')); ?>

                                </button>
                            </div>

                            <!-- Графики -->
                            <div x-show="measurements.length > 0" class="progress-chart-container space-y-6">
                                <!-- График веса -->
                                <div class="progress-chart-card bg-white border border-gray-200 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.weight_dynamics')); ?></h4>
                                    <div class="chart-container relative" style="height: 400px;">
                                        <canvas id="weightChart"></canvas>
                                    </div>
                                </div>

                                <!-- График процента жира и мышечной массы -->
                                <div class="progress-chart-card bg-white border border-gray-200 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.body_composition')); ?></h4>
                                    <div class="chart-container relative" style="height: 400px;">
                                        <canvas id="bodyCompositionChart"></canvas>
                                    </div>
                                </div>

                                <!-- График объемов -->
                                <div class="progress-chart-card bg-white border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.body_volumes')); ?></h4>
                                        <select x-model="selectedMeasurement" @change="updateMeasurementsChart()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="all"><?php echo e(__('common.all')); ?></option>
                                            <option value="chest"><?php echo e(__('common.chest_label')); ?></option>
                                            <option value="waist"><?php echo e(__('common.waist_label')); ?></option>
                                            <option value="hips"><?php echo e(__('common.hips_label')); ?></option>
                                            <option value="bicep"><?php echo e(__('common.bicep_label')); ?></option>
                                            <option value="thigh"><?php echo e(__('common.thigh_label')); ?></option>
                                            <option value="neck"><?php echo e(__('common.neck_label')); ?></option>
                                        </select>
                                    </div>
                                    <div class="chart-container relative" style="height: 400px;">
                                        <canvas id="measurementsChart"></canvas>
                                    </div>
                                </div>

                                <!-- Сводная статистика -->
                                <div class="stats-grid grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="stats-card bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                                        <div class="stats-title text-sm opacity-90">Начальный вес</div>
                                        <div class="stats-value text-2xl font-bold" x-text="measurements.length > 0 ? formatNumber(measurements[measurements.length - 1].weight, ' кг') : '—'"></div>
                                    </div>
                                    <div class="stats-card bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                                        <div class="stats-title text-sm opacity-90">Текущий вес</div>
                                        <div class="stats-value text-2xl font-bold" x-text="measurements.length > 0 ? formatNumber(measurements[0].weight, ' кг') : '—'"></div>
                                    </div>
                                    <div class="stats-card bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                                        <div class="stats-title text-sm opacity-90">Изменение веса</div>
                                        <div class="stats-value text-2xl font-bold" x-text="getWeightChange()"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div x-show="activeTab === 'workouts'" class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.athlete_workouts')); ?></h3>
                                <span class="text-sm text-gray-500" x-text="(currentAthlete?.workouts || []).length + ' <?php echo e(__('common.workouts_count')); ?>'"></span>
                            </div>
                            
                            <!-- Список тренировок -->
                            <div x-show="(currentAthlete?.workouts || []).length === 0" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Нет тренировок</h3>
                                <p class="text-gray-500">У этого спортсмена пока нет тренировок</p>
                            </div>
                            
                            <div x-show="(currentAthlete?.workouts || []).length > 0" class="space-y-3">
                                <template x-for="workout in (currentAthlete?.workouts || [])" :key="workout.id">
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <!-- Название и информация о тренировке -->
                                                <div class="workout-header">
                                                    <h4 class="text-lg font-semibold text-gray-900" x-text="workout.title"></h4>
                                                    <div class="workout-info">
                                                        <div class="workout-info-item">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            <span x-text="new Date(workout.date).toLocaleDateString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>') + (workout.time ? ' в ' + workout.time : '')"></span>
                                                        </div>
                                                        <div class="workout-info-item">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            <span x-text="workout.duration + ' <?php echo e(__('common.min')); ?>'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Описание тренировки -->
                                                <p x-show="workout.description" class="text-sm text-gray-600 mt-2" x-text="workout.description"></p>
                                                
                                                <!-- Упражнения -->
                                                <div x-show="(workout.exercises || []).length > 0" class="mt-3">
                                                    <div class="text-xs font-medium text-gray-500 mb-2">Упражнения:</div>
                                                    <div class="flex flex-wrap gap-1">
                                                        <template x-for="(exercise, index) in (workout.exercises || []).slice(0, 5)" :key="`exercise-${workout.id}-${index}`">
                                                            <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-full" x-text="exercise.name || 'Без названия'"></span>
                                                        </template>
                                                        <span x-show="(workout.exercises || []).length > 5" class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full" x-text="'+' + ((workout.exercises || []).length - 5) + ' еще'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Статус тренировки -->
                                            <div class="flex items-center ml-4">
                                                <span class="px-3 py-1 text-sm rounded-full font-medium"
                                                      :class="{
                                                          'bg-green-100 text-green-800': workout.status === 'completed',
                                                          'bg-blue-100 text-blue-800': workout.status === 'planned',
                                                          'bg-red-100 text-red-800': workout.status === 'cancelled'
                                                      }"
                                                      x-text="{
                                                          'completed': '<?php echo e(__('common.workout_completed')); ?>',
                                                          'planned': '<?php echo e(__('common.planned')); ?>', 
                                                          'cancelled': '<?php echo e(__('common.cancelled')); ?>'
                                                      }[workout.status] || workout.status"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                
                        <div x-show="activeTab === 'nutrition'" class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.nutrition_plans')); ?></h3>
                                <button @click="showAddNutritionPlan()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <?php echo e(__('common.create_nutrition_plan')); ?>

                                </button>
                            </div>
                            
                            <!-- Планы питания -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                
                                
                                <!-- Загрузка -->
                                <div x-show="loadingNutritionPlans" class="text-center py-8">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                    <p class="mt-2 text-gray-500"><?php echo e(__('common.loading_nutrition_plans')); ?></p>
                                </div>
                                
                                <!-- Список планов -->
                                <div x-show="!loadingNutritionPlans && nutritionPlans.length > 0" class="space-y-4">
                                    <template x-for="plan in nutritionPlans" :key="plan.id">
                                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors cursor-pointer" @click="showDetailedNutritionPlan(plan)">
                                            <div class="nutrition-plan-card">
                                                <div class="nutrition-plan-title">
                                                    <h5 class="text-lg font-medium text-gray-900">
                                                        <span x-text="plan.title || `<?php echo e(__('common.nutrition_plan_for')); ?> ${new Date(0, plan.month - 1).toLocaleString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>', {month: 'long'})} ${plan.year} <?php echo e(__('common.year')); ?>.`"></span>
                                                        <span class="text-sm text-gray-600" x-text="`(${plan.nutrition_days ? plan.nutrition_days.length : 0} <?php echo e(__('common.days')); ?>)`"></span>
                                                    </h5>
                                                </div>
                                                <div class="nutrition-plan-buttons">
                                                    <button @click.stop="editNutritionPlan(plan)" 
                                                            class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors"
                                                            title="<?php echo e(__('common.edit')); ?> <?php echo e(__('common.nutrition_plan')); ?>">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        <span class="mobile-button-text"><?php echo e(__('common.edit')); ?></span>
                                                    </button>
                                                    <button @click.stop="showDetailedNutritionPlan(plan)" 
                                                            class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                                            title="Подробнее">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        <span class="mobile-button-text"><?php echo e(__('common.details')); ?></span>
                                                    </button>
                                                    <button @click.stop="deleteNutritionPlan(plan.id)" 
                                                            class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="<?php echo e(__('common.delete')); ?> <?php echo e(__('common.nutrition_plan')); ?>">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        <span class="mobile-button-text"><?php echo e(__('common.delete')); ?></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Пустое состояние -->
                                <div x-show="!loadingNutritionPlans && nutritionPlans.length === 0" class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p><?php echo e(__('common.no_nutrition_plans')); ?></p>
                                        <p class="text-sm"><?php echo e(__('common.create_first_nutrition_plan')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки действий внизу справа -->
            <div class="p-6 border-t border-gray-200">
                <div class="flex justify-end gap-3">
                    <button @click="showEdit(currentAthlete.id)" 
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <?php echo e(__('common.edit')); ?>

                    </button>
                    <button @click="deleteAthlete(currentAthlete.id)" 
                            class="px-4 py-2 text-red-600 bg-white border border-red-300 rounded-lg hover:bg-red-50 flex items-center transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <?php echo e(__('common.delete')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- РЕДАКТИРОВАНИЕ СПОРТСМЕНА -->
    <!-- Форма создания спортсмена -->
    <div x-show="currentView === 'create'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Добавление спортсмена</h3>
            <button @click="showList()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

                    </button>
                </div>
        
        <form @submit.prevent="createAthlete" class="space-y-6">
            <div class="form-columns">
                <!-- Левая колонка -->
                <div class="form-column-left">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                        <input type="text" x-model="formName" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" x-model="formEmail" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Пароль *</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="formPassword" required
                                   class="w-full px-3 py-2 pr-20 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <div class="absolute inset-y-0 right-0 flex items-center space-x-1 pr-2">
                                <button type="button" @click="generatePassword()" 
                                        class="px-2 py-1 text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                        title="Сгенерировать пароль">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                <button type="button" @click="showPassword = !showPassword" 
                                        class="px-2 py-1 text-xs text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded transition-colors"
                                        title="Показать/скрыть пароль">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
    </div>
</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                        <input type="tel" x-model="formPhone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                        <input type="date" x-model="formBirthDate"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                        <select x-model="formGender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Выберите пол</option>
                            <option value="male">Мужской</option>
                            <option value="female">Женский</option>
                        </select>
                    </div>
                </div>
                
                <!-- Правая колонка -->
                <div class="form-column-right">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Спортивный уровень</label>
                        <select x-model="formSportLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Выберите уровень</option>
                            <option value="beginner"><?php echo e(__('common.novice')); ?></option>
                            <option value="intermediate"><?php echo e(__('common.amateur')); ?></option>
                            <option value="advanced">Продвинутый</option>
                            <option value="professional">Профессионал</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цели</label>
                        <textarea x-model="formGoals" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Например: похудение, набор массы, подготовка к соревнованиям"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                        <select x-model="formIsActive" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="1">Активный</option>
                            <option value="0">Неактивный</option>
                        </select>
                    </div>
                    
                    <!-- Информация о измерениях -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Измерения спортсмена</h4>
                                <p class="text-sm text-blue-600 mt-1">После создания спортсмена добавьте первое измерение с весом, ростом и другими параметрами в разделе "Измерения".</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Создать спортсмена
                </button>
                <button type="button" @click="showList()" 
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <?php echo e(__('common.cancel')); ?>

                </button>
            </div>
        </form>
    </div>

    <!-- Форма редактирования спортсмена -->
    <div x-show="currentView === 'edit'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Редактирование спортсмена</h3>
            <button @click="currentView = 'view'; activeTab = 'overview'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

                    </button>
                </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="updateAthlete" class="space-y-6">
                <div class="form-columns">
                    <!-- Левая колонка -->
                    <div class="form-column-left">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Имя *</label>
                            <input type="text" x-model="formName" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" x-model="formEmail" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пароль (оставьте пустым, чтобы не менять)</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" x-model="formPassword"
                                       class="w-full px-3 py-2 pr-20 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 right-0 flex items-center space-x-1 pr-2">
                                    <button type="button" @click="generatePassword()" 
                                            class="px-2 py-1 text-xs text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition-colors"
                                            title="Сгенерировать пароль">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                    <button type="button" @click="showPassword = !showPassword" 
                                            class="px-2 py-1 text-xs text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded transition-colors"
                                            title="Показать/скрыть пароль">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                            <input type="tel" x-model="formPhone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата рождения</label>
                            <input type="date" x-model="formBirthDate"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пол</label>
                            <select x-model="formGender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Выберите пол</option>
                                <option value="male">Мужской</option>
                                <option value="female">Женский</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Правая колонка -->
                    <div class="form-column-right">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Спортивный уровень</label>
                            <select x-model="formSportLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Выберите уровень</option>
                                <option value="beginner"><?php echo e(__('common.novice')); ?></option>
                                <option value="intermediate"><?php echo e(__('common.amateur')); ?></option>
                                <option value="advanced">Продвинутый</option>
                                <option value="professional">Профессионал</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Цели</label>
                            <textarea x-model="formGoals" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Например: похудение, набор массы, подготовка к соревнованиям"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                            <select x-model="formIsActive" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Активный</option>
                                <option value="0">Неактивный</option>
                            </select>
                        </div>
                        
                        <!-- Информация о измерениях -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Измерения спортсмена</h4>
                                    <p class="text-sm text-blue-600 mt-1">Вес и рост спортсмена отображаются из последнего измерения. Для изменения веса/роста добавьте новое измерение в разделе "Измерения".</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.save_changes')); ?>

                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'overview'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ДОБАВЛЕНИЕ ИЗМЕРЕНИЙ -->
    <div x-show="currentView === 'addMeasurement'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Добавить измерение</h3>
            <button @click="currentView = 'view'; activeTab = 'measurements'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="saveMeasurement" class="space-y-6">
                <!-- Основная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.basic_info')); ?></h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата измерения</label>
                            <input type="date" x-model="measurementDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.weight_kg')); ?></label>
                            <input type="number" step="0.1" x-model="measurementWeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.height_cm')); ?></label>
                            <input type="number" step="0.1" x-model="measurementHeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Состав тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.body_composition')); ?></h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% жира</label>
                            <input type="number" step="0.1" x-model="measurementBodyFat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.muscle_mass_kg')); ?></label>
                            <input type="number" step="0.1" x-model="measurementMuscleMass" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% воды</label>
                            <input type="number" step="0.1" x-model="measurementWaterPercentage" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Объемы тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.body_volumes')); ?> (<?php echo e(__('common.cm')); ?>)</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Грудь</label>
                            <input type="number" step="0.1" x-model="measurementChest" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Талия</label>
                            <input type="number" step="0.1" x-model="measurementWaist" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедра</label>
                            <input type="number" step="0.1" x-model="measurementHips" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бицепс</label>
                            <input type="number" step="0.1" x-model="measurementBicep" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедро</label>
                            <input type="number" step="0.1" x-model="measurementThigh" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Шея</label>
                            <input type="number" step="0.1" x-model="measurementNeck" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Медицинские показатели -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Медицинские показатели</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.resting_heart_rate')); ?> (<?php echo e(__('common.bpm')); ?>)</label>
                            <input type="number" x-model="measurementHeartRate" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Систолическое давление</label>
                            <input type="number" x-model="measurementBloodPressureSystolic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Диастолическое давление</label>
                            <input type="number" x-model="measurementBloodPressureDiastolic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Заметки -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.notes')); ?></h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дополнительная информация</label>
                        <textarea x-model="measurementNotes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Любые дополнительные заметки о состоянии спортсмена..."></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.save_measurement')); ?>

                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'measurements'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- РЕДАКТИРОВАНИЕ ИЗМЕРЕНИЙ -->
    <div x-show="currentView === 'editMeasurement'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900"><?php echo e(__('common.edit')); ?> <?php echo e(__('common.measurement')); ?></h3>
            <button @click="currentView = 'view'; activeTab = 'measurements'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="saveMeasurement" class="space-y-6">
                <!-- Основная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.basic_info')); ?></h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата измерения</label>
                            <input type="date" x-model="measurementDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.weight_kg')); ?></label>
                            <input type="number" step="0.1" x-model="measurementWeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.height_cm')); ?></label>
                            <input type="number" step="0.1" x-model="measurementHeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Состав тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.body_composition')); ?></h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% жира</label>
                            <input type="number" step="0.1" x-model="measurementBodyFat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.muscle_mass_kg')); ?></label>
                            <input type="number" step="0.1" x-model="measurementMuscleMass" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% воды</label>
                            <input type="number" step="0.1" x-model="measurementWaterPercentage" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Объемы тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.body_volumes')); ?> (<?php echo e(__('common.cm')); ?>)</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Грудь</label>
                            <input type="number" step="0.1" x-model="measurementChest" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Талия</label>
                            <input type="number" step="0.1" x-model="measurementWaist" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедра</label>
                            <input type="number" step="0.1" x-model="measurementHips" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бицепс</label>
                            <input type="number" step="0.1" x-model="measurementBicep" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бедро</label>
                            <input type="number" step="0.1" x-model="measurementThigh" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Шея</label>
                            <input type="number" step="0.1" x-model="measurementNeck" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Медицинские показатели -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Медицинские показатели</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.resting_heart_rate')); ?> (<?php echo e(__('common.bpm')); ?>)</label>
                            <input type="number" x-model="measurementHeartRate" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Систолическое давление</label>
                            <input type="number" x-model="measurementBloodPressureSystolic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Диастолическое давление</label>
                            <input type="number" x-model="measurementBloodPressureDiastolic" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Заметки -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.notes')); ?></h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дополнительная информация</label>
                        <textarea x-model="measurementNotes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Любые дополнительные заметки о состоянии спортсмена..."></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.save_changes')); ?>

                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'measurements'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ДОБАВЛЕНИЕ ПЛАНА ПИТАНИЯ -->
    <div x-show="currentView === 'addNutrition'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Создать план питания</h3>
            <button @click="currentView = 'view'; activeTab = 'nutrition'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <?php echo e(__('common.back')); ?>

            </button>
        </div>
        
        <div x-show="currentAthlete">
            <form @submit.prevent="saveNutritionPlanForm">
                <!-- Основная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.basic_info')); ?></h4>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.month')); ?></label>
                            <select x-model="nutritionMonth" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1">Январь</option>
                                <option value="2">Февраль</option>
                                <option value="3">Март</option>
                                <option value="4">Апрель</option>
                                <option value="5">Май</option>
                                <option value="6">Июнь</option>
                                <option value="7">Июль</option>
                                <option value="8">Август</option>
                                <option value="9">Сентябрь</option>
                                <option value="10">Октябрь</option>
                                <option value="11">Ноябрь</option>
                                <option value="12">Декабрь</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.year')); ?></label>
                            <select x-model="nutritionYear" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <template x-for="year in [new Date().getFullYear() - 1, new Date().getFullYear(), new Date().getFullYear() + 1, new Date().getFullYear() + 2]" :key="year">
                                    <option :value="year" :selected="year === new Date().getFullYear()" x-text="year"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4"><?php echo e(__('common.additional_info')); ?></h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.plan_title_optional')); ?></label>
                            <input type="text" x-model="nutritionTitle" placeholder="<?php echo e(__('common.nutrition_plan_example')); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.description_optional')); ?></label>
                            <textarea x-model="nutritionDescription" rows="3" placeholder="<?php echo e(__('common.trainer_comments')); ?>"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Таблица Excel для заполнения питания по дням -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900"><?php echo e(__('common.nutrition_plan_by_days')); ?></h4>
                        <div class="flex gap-2">
                            <button type="button" @click="clearAll()" class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">
                                🗑️ <?php echo e(__('common.clear_all')); ?>

                            </button>
                            <button type="button" @click="showQuickFillModal()" class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                                🚀 <?php echo e(__('common.quick_fill')); ?>

                            </button>
                        </div>
                    </div>
                    
                    <!-- Подсказки -->
                    <div class="mb-4 text-sm text-gray-600">
                        <p>💡 <strong><?php echo e(__('common.tip')); ?>:</strong> <?php echo e(__('common.calories_calculated')); ?></p>
                        <p>📝 <?php echo e(__('common.fill_only_days')); ?></p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.day')); ?></th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                        <?php echo e(__('common.proteins_g_header')); ?>

                                        <button type="button" @click="fillColumn('proteins')" class="ml-1 text-blue-600 hover:text-blue-800" title="<?php echo e(__('common.quick_fill')); ?>">⚡</button>
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                        <?php echo e(__('common.fats_g_header')); ?>

                                        <button type="button" @click="fillColumn('fats')" class="ml-1 text-blue-600 hover:text-blue-800" title="<?php echo e(__('common.quick_fill')); ?>">⚡</button>
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300">
                                        <?php echo e(__('common.carbs_g_header')); ?>

                                        <button type="button" @click="fillColumn('carbs')" class="ml-1 text-blue-600 hover:text-blue-800" title="<?php echo e(__('common.quick_fill')); ?>">⚡</button>
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.calories')); ?></th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.notes')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300">
                                <template x-for="day in 31" :key="day">
                                    <tr x-show="day <= getDaysInMonth(nutritionMonth, nutritionYear)">
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 border-r border-gray-300" x-text="day"></td>
                                        <td class="px-3 py-2 border-r border-gray-300">
                                            <input type="number" step="0.1" :name="'proteins_' + day" 
                                                   class="w-full px-2 py-1 text-sm border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none excel-cell" 
                                                   style="-moz-appearance: textfield;" 
                                                   placeholder="0.0" @input="calculateCalories(day)" @focus="selectCell(this)">
                                        </td>
                                        <td class="px-3 py-2 border-r border-gray-300">
                                            <input type="number" step="0.1" :name="'fats_' + day" 
                                                   class="w-full px-2 py-1 text-sm border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none excel-cell" 
                                                   style="-moz-appearance: textfield;" 
                                                   placeholder="0.0" @input="calculateCalories(day)" @focus="selectCell(this)">
                                        </td>
                                        <td class="px-3 py-2 border-r border-gray-300">
                                            <input type="number" step="0.1" :name="'carbs_' + day" 
                                                   class="w-full px-2 py-1 text-sm border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none excel-cell" 
                                                   style="-moz-appearance: textfield;" 
                                                   placeholder="0.0" @input="calculateCalories(day)" @focus="selectCell(this)">
                                        </td>
                                        <td class="px-3 py-2 border-r border-gray-300">
                                            <input type="number" step="0.1" :name="'calories_' + day" 
                                                   class="w-full px-2 py-1 text-sm border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none bg-gray-50 calories-field" 
                                                   placeholder="0.0" readonly>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" :name="'notes_' + day" 
                                                   class="w-full px-2 py-1 text-sm border-0 focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
                                                   placeholder="<?php echo e(__('common.notes_placeholder')); ?>">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <style>
                        /* Убираем стрелочки у number полей */
                        input[type="number"]::-webkit-outer-spin-button,
                        input[type="number"]::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }
                        
                        /* Убираем стрелочки у поля калорий */
                        .calories-field::-webkit-outer-spin-button,
                        .calories-field::-webkit-inner-spin-button {
                            -webkit-appearance: none;
                            margin: 0;
                        }
                        
                        .calories-field {
                            -moz-appearance: textfield;
                        }
                        
                        /* Улучшаем редактирование ячеек */
                        .excel-cell {
                            user-select: text;
                            cursor: text;
                        }
                        
                        .excel-cell:focus {
                            user-select: all;
                        }
                    </style>
                </div>
                
                
                <!-- Модальное окно быстрого заполнения -->
                <div x-show="quickFillModalVisible && currentView === 'addNutrition'" x-transition class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important;">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">🚀 Быстрое заполнение</h3>
                            <button type="button" @click.prevent="quickFillModalVisible = false" class="text-gray-400 hover:text-gray-600">
                                ✕
                            </button>
                        </div>
                        
                        <div>
                            <div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('common.proteins_g')); ?></label>
                                    <input type="number" step="0.1" x-model="quickFillData.proteins" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="120" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('common.fats_g')); ?></label>
                                    <input type="number" step="0.1" x-model="quickFillData.fats" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="50" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1"><?php echo e(__('common.carbs_g')); ?></label>
                                    <input type="number" step="0.1" x-model="quickFillData.carbs" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="200" required>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Начальный день</label>
                                        <input type="number" min="1" x-model="quickFillData.startDay" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Конечный день</label>
                                        <input type="number" min="1" x-model="quickFillData.endDay" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               required>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 p-3 rounded-md mb-4">
                                    <p class="text-sm text-blue-700">
                                        <strong>Калории:</strong> <span x-text="(quickFillData.proteins * 4 + quickFillData.fats * 9 + quickFillData.carbs * 4).toFixed(1)"></span> ккал
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-3 mt-6">
                                <button type="button" @click.prevent="quickFillModalVisible = false" 
                                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                    <?php echo e(__('common.cancel')); ?>

                                </button>
                                <button type="button" @click="applyQuickFill()"
                                        class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    Применить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" @click="currentView = 'view'; activeTab = 'nutrition'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.cancel')); ?>

                    </button>
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        <?php echo e(__('common.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Модальное окно детального просмотра плана питания -->
    <div x-show="detailedNutritionPlan" x-transition class="fixed top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important;">
        <div class="bg-white rounded-lg w-full max-w-6xl mx-4 max-h-[85vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900" x-text="detailedNutritionPlan ? (detailedNutritionPlan.title || `<?php echo e(__('common.nutrition_plan_for')); ?> ${new Date(0, detailedNutritionPlan.month - 1).toLocaleString('<?php echo e(app()->getLocale() === 'ua' ? 'uk-UA' : (app()->getLocale() === 'ru' ? 'ru-RU' : 'en-US')); ?>', {month: 'long'})} ${detailedNutritionPlan.year} <?php echo e(__('common.year')); ?>.`) : ''"></h3>
                <button @click="closeDetailedNutritionPlan()" class="text-gray-400 hover:text-gray-600">
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
                        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                            <div class="bg-red-50 rounded-lg p-4 text-center" style="flex: 1;">
                                <div class="text-2xl font-bold text-red-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.calories || 0), 0)) : 0"></div>
                                <div class="text-sm text-red-800"><?php echo e(__('common.total_calories')); ?></div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 text-center" style="flex: 1;">
                                <div class="text-2xl font-bold text-blue-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.proteins || 0), 0)) : 0"></div>
                                <div class="text-sm text-blue-800"><?php echo e(__('common.total_proteins_g')); ?></div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4 text-center" style="flex: 1;">
                                <div class="text-2xl font-bold text-yellow-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.carbs || 0), 0)) : 0"></div>
                                <div class="text-sm text-yellow-800"><?php echo e(__('common.total_carbs_g')); ?></div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center" style="flex: 1;">
                                <div class="text-2xl font-bold text-green-600" x-text="detailedNutritionPlan.nutrition_days ? Math.round(detailedNutritionPlan.nutrition_days.reduce((sum, day) => sum + parseFloat(day.fats || 0), 0)) : 0"></div>
                                <div class="text-sm text-green-800"><?php echo e(__('common.total_fats_g')); ?></div>
                            </div>
                        </div>
                        
                        <!-- Таблица дней -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.day')); ?></th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.proteins_g')); ?></th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.fats_g')); ?></th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.carbs_g')); ?></th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.calories')); ?></th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-300"><?php echo e(__('common.notes')); ?></th>
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
</div>

<style>
/* Карточки планов питания */
.nutrition-plan-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.nutrition-plan-title {
    flex: 1;
}

.nutrition-plan-buttons {
    display: flex;
    align-items: center;
    gap: 4px;
}

.mobile-button-text {
    display: none;
}

/* Медиа-запрос для мобильных устройств */
@media (max-width: 640px) {
    .nutrition-plan-card {
        flex-direction: column;
        align-items: center;
    }
    
    .nutrition-plan-title {
        margin-bottom: 12px;
        text-align: center;
        line-height: 1.3;
    }
    
    .nutrition-plan-title h5 {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    
    .nutrition-plan-buttons {
        align-self: center;
        gap: 8px;
    }
    
    .nutrition-plan-buttons button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .nutrition-plan-buttons button:first-child {
        color: #4f46e5;
        background: #eef2ff;
        border: 1px solid #c7d2fe;
    }
    
    .nutrition-plan-buttons button:first-child:hover {
        background: #e0e7ff;
    }
    
    .nutrition-plan-buttons button:nth-child(2) {
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
    }
    
    .nutrition-plan-buttons button:nth-child(2):hover {
        background: #d1fae5;
    }
    
    .nutrition-plan-buttons button:last-child {
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    
    .nutrition-plan-buttons button:last-child:hover {
        background: #fee2e2;
    }
    
    .nutrition-plan-buttons button svg {
        display: none;
    }
    
    .mobile-button-text {
        display: inline;
    }
}

/* Специальный медиа-запрос для вкладки питания на мобилке */
@media (max-width: 767px) {
    .p-6 {
        padding: 0.5rem !important;
    }
}

/* Данные спортсмена в профиле - по умолчанию в ряд */
.athlete-profile-data {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.athlete-profile-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.athlete-profile-label {
    font-weight: 500;
    color: #6b7280;
}

.athlete-profile-value {
    font-weight: 600;
    color: #374151;
}

/* Данные спортсмена в профиле на мобилке - в колонку */
@media (max-width: 640px) {
    .athlete-profile-data {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .athlete-profile-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .athlete-profile-label {
        font-weight: 500;
        color: #6b7280;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        min-width: 50px;
    }
    
    .athlete-profile-value {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
}

/* Тренировки - адаптивность */
.workout-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.workout-info {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    gap: 16px;
    font-size: 14px;
    color: #6b7280;
}

.workout-info-item {
    display: flex;
    align-items: center;
}

.workout-status {
    margin-left: auto;
}


/* Тренировки на мобилке */
@media (max-width: 640px) {
    .workout-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    .workout-info {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }
    
    .workout-info-item {
        font-size: 13px;
    }
    
    .workout-status {
        margin-left: 0 !important;
        align-self: flex-start;
    }
}

/* Тренировки на десктопе - принудительно в ряд */
@media (min-width: 641px) {
    .workout-info {
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        gap: 16px !important;
    }
    
    .workout-info-item {
        white-space: nowrap !important;
    }
    
    .workout-status {
        margin-left: auto !important;
    }
}


/* Кнопки в профиле спортсмена */
.profile-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.profile-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.profile-btn-edit {
    color: #4b5563;
    background-color: #f9fafb;
    border: 1px solid #d1d5db;
}

.profile-btn-edit:hover {
    color: #1f2937;
    background-color: #f3f4f6;
}

.profile-btn-delete {
    color: #dc2626;
    background-color: #fef2f2;
    border: 1px solid #fca5a5;
}

.profile-btn-delete:hover {
    color: #991b1b;
    background-color: #fee2e2;
}

/* На десктопе - в одну линию */
@media (min-width: 768px) {
    .profile-buttons {
        flex-direction: row;
        gap: 8px;
    }
    
    .profile-btn {
        width: auto;
        font-size: 16px;
        padding: 8px 16px;
    }
}

/* Форма в две колонки */
.form-columns {
    display: block;
}

.form-column-left,
.form-column-right {
    margin-bottom: 24px;
}

.form-column-left > div,
.form-column-right > div {
    margin-bottom: 24px;
}

/* На десктопе - две колонки */
@media (min-width: 1024px) {
    .form-columns {
        display: flex;
        gap: 24px;
    }
    
    .form-column-left,
    .form-column-right {
        flex: 1;
        margin-bottom: 0;
    }
    
    .form-column-left > div,
    .form-column-right > div {
        margin-bottom: 24px;
    }
}
</style>

<!-- Модальное окно для создания плана питания -->
<div id="nutrition-plan-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Заголовок модального окна -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900" id="nutrition-modal-title">Создать план питания</h3>
                <button onclick="closeNutritionPlanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Содержимое модального окна -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <div id="nutrition-plan-content">
                    <!-- Содержимое будет загружено через JavaScript -->
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
let currentNutritionPlan = null;

function openNutritionPlanModal() {
    currentNutritionPlan = null;
    document.getElementById('nutrition-modal-title').textContent = 'Создать план питания';
    showCreatePlanForm();
    document.getElementById('nutrition-plan-modal').classList.remove('hidden');
}

function closeNutritionPlanModal() {
    document.getElementById('nutrition-plan-modal').classList.add('hidden');
    currentNutritionPlan = null;
}

function showCreatePlanForm() {
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1;
    const currentYear = currentDate.getFullYear();
    
    const content = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.month')); ?></label>
                    <select id="plan-month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="1" ${currentMonth === 1 ? 'selected' : ''}>Январь</option>
                        <option value="2" ${currentMonth === 2 ? 'selected' : ''}>Февраль</option>
                        <option value="3" ${currentMonth === 3 ? 'selected' : ''}>Март</option>
                        <option value="4" ${currentMonth === 4 ? 'selected' : ''}>Апрель</option>
                        <option value="5" ${currentMonth === 5 ? 'selected' : ''}>Май</option>
                        <option value="6" ${currentMonth === 6 ? 'selected' : ''}>Июнь</option>
                        <option value="7" ${currentMonth === 7 ? 'selected' : ''}>Июль</option>
                        <option value="8" ${currentMonth === 8 ? 'selected' : ''}>Август</option>
                        <option value="9" ${currentMonth === 9 ? 'selected' : ''}>Сентябрь</option>
                        <option value="10" ${currentMonth === 10 ? 'selected' : ''}>Октябрь</option>
                        <option value="11" ${currentMonth === 11 ? 'selected' : ''}>Ноябрь</option>
                        <option value="12" ${currentMonth === 12 ? 'selected' : ''}>Декабрь</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.year')); ?></label>
                    <select id="plan-year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        ${generateYearOptions(currentYear)}
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.plan_title_optional')); ?></label>
                <input type="text" id="plan-title" placeholder="<?php echo e(__('common.nutrition_plan_example')); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo e(__('common.description_optional')); ?></label>
                <textarea id="plan-description" rows="3" placeholder="<?php echo e(__('common.trainer_comments')); ?>"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>
    `;
    document.getElementById('nutrition-plan-content').innerHTML = content;
}

function generateYearOptions(currentYear) {
    let options = '';
    for (let year = currentYear - 1; year <= currentYear + 2; year++) {
        options += `<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`;
    }
    return options;
}

async function saveNutritionPlan_DISABLED() {
    const month = document.getElementById('plan-month').value;
    const year = document.getElementById('plan-year').value;
    const title = document.getElementById('plan-title').value;
    const description = document.getElementById('plan-description').value;
    
    if (!month || !year) {
        alert('Пожалуйста, выберите месяц и год');
        return;
    }
    
    try {
        const response = await fetch('/trainer/nutrition-plans', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                athlete_id: window.currentAthleteId,
                month: parseInt(month),
                year: parseInt(year),
                title: title,
                description: description
            })
        });
        
        if (response.ok) {
            closeNutritionPlanModal();
            loadNutritionPlans();
            alert('<?php echo e(__('common.nutrition_plan_created')); ?>');
        } else {
            const error = await response.json();
            alert(error.error || 'Ошибка при создании плана');
        }
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Ошибка при создании плана');
    }
}

async function loadNutritionPlans() {
    // Заглушка - здесь будет загрузка планов питания
    console.log('Загружаем планы питания для спортсмена:', window.currentAthleteId);
}

// Добавляем переменную для текущего ID спортсмена
window.currentAthleteId = null;

// Обновляем currentAthleteId при выборе спортсмена
document.addEventListener('DOMContentLoaded', function() {
    // Находим Alpine.js компонент и следим за изменениями currentAthlete
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                // Ищем Alpine.js элемент
                const alpineElement = document.querySelector('[x-data*="athletesApp"]');
                if (alpineElement && window.Alpine) {
                    const data = window.Alpine.$data(alpineElement);
                    if (data && data.currentAthlete && data.currentAthlete.id) {
                        window.currentAthleteId = data.currentAthlete.id;
                    }
                }
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true
    });
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make("crm.layouts.app", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/trainer/athletes/index.blade.php ENDPATH**/ ?>
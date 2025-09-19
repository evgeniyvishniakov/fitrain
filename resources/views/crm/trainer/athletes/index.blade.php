@extends("crm.layouts.app")

@section("title", "Спортсмены")
@section("page-title", "Спортсмены")

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

/* Скрытие скроллбара для вкладок */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>

<script>
// SPA функциональность для спортсменов
function athletesApp() {
    return {
        currentView: 'list', // list, create, edit, view, addMeasurement, editMeasurement
        athletes: @json($athletes->items()),
        currentAthlete: null,
        activeTab: 'overview', // для вкладок в просмотре
        measurements: [], // Массив для хранения измерений
        currentMeasurement: null, // Текущее измерение для редактирования
        search: '',
        sportLevel: '',
        currentPage: 1,
        itemsPerPage: 12,
        
        
        // Поля формы
        formName: '',
        formEmail: '',
        formPassword: '',
        showPassword: false,
        formPhone: '',
        formBirthDate: '',
        formGender: '',
        formWeight: '',
        formHeight: '',
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
            this.formWeight = '';
            this.formHeight = '';
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
            
            // Берем вес и рост из последнего измерения, если есть, иначе из профиля
            const latestMeasurement = this.measurements.length > 0 ? this.measurements[0] : null;
            this.formWeight = latestMeasurement?.weight || this.currentAthlete.weight || '';
            this.formHeight = latestMeasurement?.height || this.currentAthlete.height || '';
            
            this.formSportLevel = this.currentAthlete.sport_level || '';
            this.formGoals = this.currentAthlete.goals || [];
            this.formHealthRestrictions = this.currentAthlete.health_restrictions ? JSON.stringify(this.currentAthlete.health_restrictions) : '';
            this.formIsActive = this.currentAthlete.is_active ? '1' : '0';
        },
        
        async showView(athleteId) {
            this.currentView = 'view';
            this.currentAthlete = this.athletes.find(a => a.id === athleteId);
            this.activeTab = 'overview'; // сбрасываем на первую вкладку
            
            console.log('Спортсмен ДО загрузки измерений:', this.currentAthlete);
            
            // Загружаем измерения спортсмена
            await this.loadMeasurements(athleteId);
            
            console.log('Спортсмен ПОСЛЕ загрузки измерений:', this.currentAthlete);
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
                return { text: 'Недостаточный вес', color: 'text-blue-600', bg: 'bg-blue-100' };
            } else if (bmi < 25) {
                return { text: 'Нормальный вес', color: 'text-green-600', bg: 'bg-green-100' };
            } else if (bmi < 30) {
                return { text: 'Избыточный вес', color: 'text-yellow-600', bg: 'bg-yellow-100' };
            } else {
                return { text: 'Ожирение', color: 'text-red-600', bg: 'bg-red-100' };
            }
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
                    console.log('Загруженные измерения:', result.measurements);
                    console.log('Актуальные данные спортсмена с сервера:', result.athlete);
                    this.measurements = result.measurements;
                    
                    // Обновляем данные спортсмена актуальными данными с сервера
                    if (result.athlete) {
                        this.currentAthlete = { ...this.currentAthlete, ...result.athlete };
                        console.log('Обновлен currentAthlete актуальными данными:', this.currentAthlete);
                        
                        // Обновляем спортсмена в общем списке
                        const athleteIndex = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                        if (athleteIndex !== -1) {
                            this.athletes[athleteIndex] = { ...this.athletes[athleteIndex], ...result.athlete };
                            console.log('Обновлен спортсмен в списке актуальными данными:', this.athletes[athleteIndex]);
                        }
                    }
                } else {
                    console.error('Ошибка загрузки измерений:', result.message);
                    this.measurements = [];
                }
            } catch (error) {
                console.error('Ошибка загрузки измерений:', error);
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
        
        editMeasurement(measurement) {
            this.currentView = 'editMeasurement';
            this.currentMeasurement = measurement;
            this.measurementDate = measurement.measurement_date;
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
        
        // Метки статусов
        getSportLevelLabel(level) {
            const labels = {
                'beginner': 'Новичок',
                'intermediate': 'Любитель', 
                'advanced': 'Профи'
            };
            return labels[level] || level;
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
                weight: this.formWeight,
                height: this.formHeight,
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
                            title: this.currentAthlete && this.currentAthlete.id ? 'Спортсмен обновлен' : 'Спортсмен добавлен',
                            message: this.currentAthlete && this.currentAthlete.id ? 
                                'Данные спортсмена успешно обновлены' : 
                                'Спортсмен успешно добавлен'
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
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении спортсмена'
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
                        message: 'Произошла ошибка при сохранении спортсмена'
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
                    weight: this.formWeight ? parseFloat(this.formWeight) : null,
                    height: this.formHeight ? parseFloat(this.formHeight) : null,
                    sport_level: this.formSportLevel,
                    goals: this.formGoals,
                    is_active: this.formIsActive === '1',
                    trainer_id: {{ auth()->id() }}
                };
                
                const response = await fetch('{{ route("crm.trainer.store-athlete") }}', {
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
                            title: 'Успех',
                            message: 'Спортсмен успешно создан'
                        }
                    }));
                    
                    // Возвращаемся к списку
                    this.currentView = 'list';
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка',
                            message: result.message || 'Ошибка создания спортсмена'
                        }
                    }));
                }
            } catch (error) {
                console.error('Ошибка создания спортсмена:', error);
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        type: 'error',
                        title: 'Ошибка',
                        message: 'Ошибка создания спортсмена'
                    }
                }));
            }
        },
        
        updateAthlete() {
            // Здесь будет логика обновления спортсмена
            console.log('Обновление спортсмена:', this.currentAthlete);
            
            // Показываем уведомление об успехе
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    type: 'success',
                    title: 'Успех',
                    message: 'Данные спортсмена обновлены'
                }
            }));
            
            // Возвращаемся к карточке спортсмена
            this.currentView = 'view';
            this.activeTab = 'overview';
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
                console.log('Ответ сервера:', result);
                console.log('Данные измерения:', result.measurement);
                
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
                        console.log('Обновлен вес в профиле:', this.currentAthlete.weight);
                    }
                    if (result.measurement.height) {
                        this.currentAthlete.height = result.measurement.height;
                        console.log('Обновлен рост в профиле:', this.currentAthlete.height);
                    }
                    
                    // Обновляем спортсмена в общем списке
                    const athleteIndex = this.athletes.findIndex(a => a.id === this.currentAthlete.id);
                    if (athleteIndex !== -1) {
                        this.athletes[athleteIndex].weight = this.currentAthlete.weight;
                        this.athletes[athleteIndex].height = this.currentAthlete.height;
                        console.log('Обновлен спортсмен в списке:', this.athletes[athleteIndex]);
                    }
                    
                    // Показываем уведомление об успехе
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'success',
                            title: 'Успех',
                            message: isEdit ? 'Измерение успешно обновлено' : 'Измерения успешно сохранены'
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
                            title: 'Ошибка сохранения',
                            message: result.message || 'Произошла ошибка при сохранении измерения'
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
                        message: 'Произошла ошибка при сохранении измерения'
                    }
                }));
            }
        },
        
        // Удаление измерения
        async deleteMeasurement(measurementId) {
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: 'Удалить измерение',
                    message: 'Вы уверены, что хотите удалить это измерение?',
                    confirmText: 'Удалить',
                    cancelText: 'Отмена',
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
                            title: 'Измерение удалено',
                            message: 'Измерение успешно удалено'
                        }
                    }));
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении измерения'
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
                        message: 'Произошла ошибка при удалении измерения'
                    }
                }));
            }
        },
        
        // Удаление
        deleteAthlete(id) {
            const athlete = this.athletes.find(a => a.id === id);
            const athleteName = athlete ? athlete.name : 'спортсмена';
            
            // Используем глобальное модальное окно подтверждения
            window.dispatchEvent(new CustomEvent('show-confirm', {
                detail: {
                    title: 'Удалить спортсмена',
                    message: `Вы уверены, что хотите удалить спортсмена "${athleteName}"?`,
                    confirmText: 'Удалить',
                    cancelText: 'Отмена',
                    onConfirm: () => this.performDelete(id)
                }
            }));
        },
        
        async performDelete(id) {
            try {
                const response = await fetch(`/trainer/athletes/${id}`, {
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
                            title: 'Спортсмен удален',
                            message: 'Спортсмен успешно удален'
                        }
                    }));
                    
                    // Удаляем из списка
                    this.athletes = this.athletes.filter(a => a.id !== id);
                    
                    // Если удалили всех спортсменов на текущей странице, переходим на предыдущую
                    if (this.paginatedAthletes.length === 0 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                } else {
                    // Показываем уведомление об ошибке
                    window.dispatchEvent(new CustomEvent('show-notification', {
                        detail: {
                            type: 'error',
                            title: 'Ошибка удаления',
                            message: result.message || 'Произошла ошибка при удалении спортсмена'
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
                        message: 'Произошла ошибка при удалении спортсмена'
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
        </svg>
        Дашборд
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Календарь
    </a>
    <a href="{{ route("crm.workouts.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        Тренировки
    </a>
    <a href="{{ route("crm.exercises.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        Упражнения
    </a>
    <a href="{{ route("crm.workout-templates.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Шаблоны тренировок
    </a>
    <a href="{{ route("crm.progress.index") }}" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Прогресс
    </a>
    <a href="{{ route("crm.trainer.athletes") }}" class="nav-link active flex items-center px-4 py-3 rounded-xl mb-2">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Спортсмены
    </a>
    <a href="#" class="nav-link flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Настройки
    </a>
@endsection

@section("content")
<div x-data="athletesApp()" x-cloak class="space-y-6">
    
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
                           placeholder="Поиск спортсменов..." 
                           class="w-full px-3 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                
                <!-- Фильтр уровня -->
                <div class="status-container">
                    <select x-model="sportLevel" 
                            class="w-full px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors appearance-none cursor-pointer">
                        <option value="">Все уровни</option>
                        <option value="beginner">Новичок</option>
                        <option value="intermediate">Любитель</option>
                        <option value="advanced">Профи</option>
                    </select>
                </div>
                
                <!-- Кнопки -->
                <div class="buttons-container">
                    <button @click="showCreate()" 
                            class="px-4 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors whitespace-nowrap">
                        Добавить спортсмена
                    </button>
                </div>
                </div>
            </div>
            
        <!-- Активные фильтры -->
        <div x-show="search || sportLevel" class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500">Активные фильтры:</span>
                
                <span x-show="search" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Поиск: "<span x-text="search"></span>"
                    <button @click="search = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
                
                <span x-show="sportLevel" 
                      class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    Уровень: <span x-text="getSportLevelLabel(sportLevel)"></span>
                    <button @click="sportLevel = ''" class="ml-2 text-indigo-600 hover:text-indigo-800">×</button>
                </span>
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
                                    <span x-show="athlete.age" class="text-sm text-gray-500" x-text="'Возраст: ' + athlete.age + ' лет'"></span>
                                    <span x-show="athlete.weight" class="text-sm text-gray-500" x-text="'Вес: ' + formatNumber(athlete.weight, ' кг')"></span>
                                    <span x-show="athlete.height" class="text-sm text-gray-500" x-text="'Рост: ' + formatNumber(athlete.height, ' см')"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Центральная часть: кнопки -->
                        <div class="flex items-center space-x-2 mx-4">
                            <button @click="showView(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                Просмотр
                            </button>
                            <button @click="showEdit(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button @click="deleteAthlete(athlete.id)" 
                                    class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
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
                                        <span x-show="athlete.age" x-text="'Возраст: ' + athlete.age + ' лет'"></span>
                                        <span x-show="athlete.weight" x-text="'Вес: ' + formatNumber(athlete.weight, ' кг')"></span>
                                        <span x-show="athlete.height" x-text="'Рост: ' + formatNumber(athlete.height, ' см')"></span>
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
                                Просмотр
                            </button>
                            <button @click="showEdit(athlete.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                                Редактировать
                            </button>
                            <button @click="deleteAthlete(athlete.id)" 
                                    class="flex-1 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 transition-colors">
                                Удалить
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
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Нет спортсменов</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">У вас пока нет спортсменов. Добавьте первого спортсмена для начала работы.</p>
            <button @click="showCreate()" 
                    class="px-6 py-3 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Добавить первого спортсмена
            </button>
        </div>
    </div>

    <!-- ПРОСМОТР СПОРТСМЕНА -->
    <div x-show="currentView === 'view'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div x-show="currentAthlete">
            <!-- Заголовок карточки -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="showList()" 
                                class="text-gray-500 hover:text-gray-700 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Профиль спортсмена</h1>
                        </div>
                    </div>
                    <!-- Кнопки действий -->
                    <div class="profile-buttons">
                        <button @click="showEdit(currentAthlete.id)" 
                                class="profile-btn profile-btn-edit">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Редактировать
                        </button>
                        <button @click="deleteAthlete(currentAthlete.id)" 
                                class="profile-btn profile-btn-delete">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Удалить
                        </button>
                    </div>
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
                                <div class="flex gap-6 text-sm text-gray-500 mt-2">
                                    <span x-text="'Возраст: ' + (currentAthlete?.age || '—')"></span>
                                    <span x-text="'Вес: ' + formatNumber(currentAthlete?.weight, ' кг')"></span>
                                    <span x-text="'Рост: ' + formatNumber(currentAthlete?.height, ' см')"></span>
                                </div>
                            </div>

                            <!-- Быстрая статистика -->
                            <div class="text-right">
                                <div class="text-3xl font-bold text-indigo-600" x-text="currentAthlete?.weight && currentAthlete?.height ? formatNumber(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)) : '—'"></div>
                                <div class="text-sm text-gray-500 flex items-center justify-end gap-1">
                                    <span>ИМТ</span>
                                    <!-- Иконка знака вопроса с подсказкой -->
                                    <div class="relative group">
                                        <svg class="w-4 h-4 text-gray-400 hover:text-gray-600 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3 3 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                        </svg>
                                        <!-- Всплывающая подсказка -->
                                        <div class="absolute bottom-full right-0 mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                            <div class="font-semibold mb-2">Индекс массы тела (ИМТ)</div>
                                            <div class="space-y-1">
                                                <div class="flex justify-between"><span class="text-blue-300">Менее 18.5:</span> <span>Недостаточный вес</span></div>
                                                <div class="flex justify-between"><span class="text-green-300">18.5 - 24.9:</span> <span>Нормальный вес</span></div>
                                                <div class="flex justify-between"><span class="text-yellow-300">25 - 29.9:</span> <span>Избыточный вес</span></div>
                                                <div class="flex justify-between"><span class="text-red-300">30 и более:</span> <span>Ожирение</span></div>
                                            </div>
                                            <div class="mt-2 pt-2 border-t border-gray-700 text-gray-300">
                                                <div x-text="currentAthlete?.weight && currentAthlete?.height ? 'Ваш ИМТ: ' + formatNumber(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)) + ' (' + getBMICategory(currentAthlete.weight / Math.pow(currentAthlete.height/100, 2)).text + ')' : 'ИМТ не рассчитан'"></div>
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
                                Обзор
                                </button>
                            <button @click="activeTab = 'workouts'" 
                                    :class="activeTab === 'workouts' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Тренировки
                            </button>
                            <button @click="activeTab = 'progress'" 
                                    :class="activeTab === 'progress' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Прогресс
                            </button>
                            <button @click="activeTab = 'measurements'" 
                                    :class="activeTab === 'measurements' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Измерения
                            </button>
                            <button @click="activeTab = 'nutrition'" 
                                    :class="activeTab === 'nutrition' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Питание
                            </button>
                            <button @click="activeTab = 'medical'" 
                                    :class="activeTab === 'medical' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Медицинские данные
                            </button>
                            <button @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap">
                                Общие
                            </button>
                        </nav>
                    </div>

                    <!-- Содержимое вкладок -->
                    <div class="p-6">
                        <!-- Вкладка "Обзор" -->
                        <div x-show="activeTab === 'overview'" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600" x-text="currentAthlete?.workouts?.length || 0"></div>
                                    <div class="text-sm text-blue-800">Тренировок</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600" x-text="currentAthlete?.progress?.length || 0"></div>
                                    <div class="text-sm text-green-800">Записей прогресса</div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-purple-600" x-text="currentAthlete?.is_active ? 'Да' : 'Нет'"></div>
                                    <div class="text-sm text-purple-800">Активен</div>
                                </div>
                                </div>
                            </div>

                        <!-- Вкладка "Общие данные" -->
                        <div x-show="activeTab === 'general'" class="space-y-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div class="lg:col-span-2">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Личная информация</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Полное имя</label>
                                            <div class="text-gray-900 font-medium" x-text="currentAthlete?.name"></div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Телефон</label>
                                            <div class="text-gray-900" x-text="currentAthlete?.phone || '—'"></div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Возраст</label>
                                            <div class="text-gray-900 font-semibold" x-text="(currentAthlete?.age || '—') + ' лет'"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Спортивная информация</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Спортивный уровень</label>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800" 
                                                      x-text="currentAthlete?.sport_level || 'Не указан'"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-500">Статус</label>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                      :class="currentAthlete?.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                      x-text="currentAthlete?.is_active ? 'Активен' : 'Неактивен'"></span>
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
                            <p class="mb-4">Здесь будут отображаться медицинские данные спортсмена</p>
                        </div>

                        <div x-show="activeTab === 'measurements'" class="space-y-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Измерения тела</h3>
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
                                <div class="text-sm text-gray-600 mb-4">
                                    Всего измерений: <span x-text="measurements.length"></span>
                                </div>
                                
                                <div class="space-y-3">
                                    <template x-for="measurement in measurements" :key="measurement.id">
                                        <div class="bg-white rounded-lg p-4 border-2 border-gray-300 shadow-md hover:shadow-lg transition-shadow">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="font-semibold text-gray-900" x-text="new Date(measurement.measurement_date).toLocaleDateString('ru-RU')"></h4>
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex space-x-1">
                                                        <button @click="editMeasurement(measurement)" 
                                                                class="p-1 text-gray-400 hover:text-indigo-600 transition-colors"
                                                                title="Редактировать">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                                        <button @click="deleteMeasurement(measurement.id)" 
                                                                class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                                                title="Удалить">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                <div x-show="measurement.weight" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Вес</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.weight, ' кг')"></span>
                                                </div>
                                                <div x-show="measurement.height" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Рост</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.height, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.body_fat_percentage" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">% жира</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.body_fat_percentage, '%')"></span>
                                                </div>
                                                <div x-show="measurement.muscle_mass" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Мышцы</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.muscle_mass, ' кг')"></span>
                                                </div>
                                                <div x-show="measurement.chest" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Грудь</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.chest, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.waist" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Талия</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.waist, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.hips" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Бедра</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.hips, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.bicep" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Бицепс</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.bicep, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.thigh" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Бедро</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.thigh, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.neck" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Шея</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.neck, ' см')"></span>
                                                </div>
                                                <div x-show="measurement.resting_heart_rate" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Пульс</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.resting_heart_rate, ' уд/мин')"></span>
                                                </div>
                                                <div x-show="measurement.blood_pressure_systolic" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">Давление</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.blood_pressure_systolic) + '/' + formatNumber(measurement.blood_pressure_diastolic)"></span>
                                                </div>
                                                <div x-show="measurement.water_percentage" class="flex flex-col bg-white border border-gray-300 p-3 rounded-lg shadow-sm">
                                                    <span class="text-gray-600 text-xs uppercase tracking-wide">% воды</span>
                                                    <span class="font-semibold text-gray-900 text-lg" x-text="formatNumber(measurement.water_percentage, '%')"></span>
                                                </div>
                                            </div>
                                            
                                            <div x-show="measurement.notes" class="mt-3 pt-3 border-t border-gray-200">
                                                <p class="text-sm text-gray-600" x-text="measurement.notes"></p>
                            </div>
                        </div>
                    </template>
                                </div>
                </div>
                
                <!-- Пустое состояние -->
                            <div x-show="measurements.length === 0" class="text-center py-12 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Нет измерений</h3>
                                <p class="mb-4">Добавьте первое измерение для отслеживания прогресса</p>
                            </div>
                        </div>

                        <div x-show="activeTab === 'progress'" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Прогресс</h3>
                            <p class="mb-4">Здесь будут отображаться графики и аналитика прогресса</p>
                        </div>

                        <div x-show="activeTab === 'workouts'" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Тренировки</h3>
                            <p class="mb-4">Здесь будут отображаться тренировки спортсмена</p>
                </div>
                
                        <div x-show="activeTab === 'nutrition'" class="text-center py-12 text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Питание</h3>
                            <p class="mb-4">Здесь будет информация о питании и диете</p>
                        </div>
                    </div>
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
                Назад
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                        <input type="number" step="0.1" x-model="formWeight"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                        <input type="number" step="0.1" x-model="formHeight"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Спортивный уровень</label>
                        <select x-model="formSportLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Выберите уровень</option>
                            <option value="beginner">Новичок</option>
                            <option value="intermediate">Любитель</option>
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
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Создать спортсмена
                </button>
                <button type="button" @click="showList()" 
                        class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Отмена
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
                Назад
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                            <input type="number" step="0.1" x-model="formWeight"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                            <input type="number" step="0.1" x-model="formHeight"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Спортивный уровень</label>
                            <select x-model="formSportLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Выберите уровень</option>
                                <option value="beginner">Новичок</option>
                                <option value="intermediate">Любитель</option>
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
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="submit" 
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Сохранить изменения
                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'overview'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Отмена
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
                Назад
            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="saveMeasurement" class="space-y-6">
                <!-- Основная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Основная информация</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата измерения</label>
                            <input type="date" x-model="measurementDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                            <input type="number" step="0.1" x-model="measurementWeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                            <input type="number" step="0.1" x-model="measurementHeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Состав тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Состав тела</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% жира</label>
                            <input type="number" step="0.1" x-model="measurementBodyFat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Мышечная масса (кг)</label>
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
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Объемы тела (см)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пульс в покое (уд/мин)</label>
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
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Заметки</h4>
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
                        Сохранить измерение
                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'measurements'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- РЕДАКТИРОВАНИЕ ИЗМЕРЕНИЙ -->
    <div x-show="currentView === 'editMeasurement'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Редактировать измерение</h3>
            <button @click="currentView = 'view'; activeTab = 'measurements'" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                Назад
            </button>
        </div>
        
        <div x-show="currentAthlete" class="space-y-6">
            <form @submit.prevent="saveMeasurement" class="space-y-6">
                <!-- Основная информация -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Основная информация</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Дата измерения</label>
                            <input type="date" x-model="measurementDate" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Вес (кг)</label>
                            <input type="number" step="0.1" x-model="measurementWeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Рост (см)</label>
                            <input type="number" step="0.1" x-model="measurementHeight" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Состав тела -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Состав тела</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">% жира</label>
                            <input type="number" step="0.1" x-model="measurementBodyFat" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Мышечная масса (кг)</label>
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
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Объемы тела (см)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пульс в покое (уд/мин)</label>
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
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Заметки</h4>
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
                        Сохранить изменения
                    </button>
                    <button type="button" @click="currentView = 'view'; activeTab = 'measurements'" 
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
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

@endsection

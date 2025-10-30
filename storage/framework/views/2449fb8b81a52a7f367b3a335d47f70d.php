<?php $__env->startSection('title', 'Упражнения'); ?>
<?php $__env->startSection('page-title', 'Управление упражнениями'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Фильтры -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Поиск</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       class="filter-input block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Название упражнения">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Категория</label>
                <select name="category" class="filter-select block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    <?php $__currentLoopData = \App\Models\Trainer\Exercise::CATEGORIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('category') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Оборудование</label>
                <select name="equipment" class="filter-select block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    <?php $__currentLoopData = \App\Models\Trainer\Exercise::EQUIPMENT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(request('equipment') == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Тип</label>
                <select name="is_system" class="filter-select block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Все</option>
                    <option value="1" <?php echo e(request('is_system') == '1' ? 'selected' : ''); ?>>Системные</option>
                    <option value="0" <?php echo e(request('is_system') == '0' ? 'selected' : ''); ?>>Пользовательские</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Кнопка создания -->
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-600" id="exercisesCount">Всего: <span class="font-semibold"><?php echo e($exercises->total()); ?></span></div>
        <button onclick="openCreateModal()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
            ➕ Создать упражнение
        </button>
    </div>

    <!-- Таблица -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Фото</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Название (RU/UK)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Оборудование</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тип</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $exercises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exercise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <?php if($exercise->image_url): ?>
                                <img src="<?php echo e(asset('storage/' . $exercise->image_url)); ?>" 
                                     alt="<?php echo e($exercise->getTranslated('name', 'ru')); ?>" 
                                     class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 text-xs">
                                    Нет фото
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-sm">🇷🇺 <?php echo e($exercise->getTranslated('name', 'ru')); ?></div>
                            <?php if(isset($exercise->translations['ua']['name']) && !empty($exercise->translations['ua']['name'])): ?>
                                <div class="text-gray-600 text-sm mt-1">🇺🇦 <?php echo e($exercise->translations['ua']['name']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs"><?php echo e($exercise->category); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-teal-100 text-teal-800 rounded-full text-xs"><?php echo e($exercise->equipment ?? '—'); ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($exercise->is_system): ?>
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Системное</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Польз.</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-3">
                                <button onclick="openEditModal(<?php echo e($exercise->id); ?>)" 
                                        class="text-blue-600 hover:text-blue-900 text-xl transition-transform hover:scale-110" 
                                        title="Редактировать">
                                    ✏️
                                </button>
                                <button onclick="deleteExercise(<?php echo e($exercise->id); ?>)" 
                                        class="text-red-600 hover:text-red-900 text-xl transition-transform hover:scale-110" 
                                        title="Удалить">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="text-4xl mb-3">🏋️</div>
                            <p>Упражнения не найдены</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if($exercises->hasPages()): ?>
            <div class="px-6 py-4 border-t" id="paginationContainer"><?php echo e($exercises->links()); ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно создания/редактирования -->
<div id="exerciseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center">
            <h2 id="modalTitle" class="text-2xl font-bold">Создать упражнение</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">✕</button>
        </div>
        
        <form id="exerciseForm" class="p-6 space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="exerciseId" name="id">
            
            <!-- Основная информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название (RU) *</label>
                    <input type="text" name="name_ru" id="name_ru" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Название (UK) *</label>
                    <input type="text" name="name_uk" id="name_uk" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <!-- Изображения (основные) -->
            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Изображения (основные)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Главное изображение</label>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <div id="imagePreview" class="mt-2 hidden">
                            <p class="text-xs text-gray-600 mb-1">Текущее изображение:</p>
                            <img src="" alt="Preview" class="w-40 h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Второе изображение</label>
                        <input type="file" name="image_2" id="image_2" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <div id="imagePreview2" class="mt-2 hidden">
                            <p class="text-xs text-gray-600 mb-1">Текущее изображение:</p>
                            <img src="" alt="Preview 2" class="w-40 h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Изображения для девушек -->
            <div>
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Изображения для девушек (опционально)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Главное изображение (девушки)</label>
                        <input type="file" name="image_female" id="image_female" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <div id="imagePreviewFemale" class="mt-2 hidden">
                            <p class="text-xs text-gray-600 mb-1">Текущее изображение:</p>
                            <img src="" alt="Preview Female" class="w-40 h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Второе изображение (девушки)</label>
                        <input type="file" name="image_female_2" id="image_female_2" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <div id="imagePreviewFemale2" class="mt-2 hidden">
                            <p class="text-xs text-gray-600 mb-1">Текущее изображение:</p>
                            <img src="" alt="Preview Female 2" class="w-40 h-40 object-cover rounded-lg border border-gray-200">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Видео URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ссылка на видео</label>
                <input type="url" name="video_url" id="video_url" placeholder="https://youtube.com/watch?v=..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Категория, оборудование, сложность -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Категория *</label>
                    <select name="category" id="category" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Выберите категорию</option>
                        <?php $__currentLoopData = \App\Models\Trainer\Exercise::CATEGORIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Оборудование</label>
                    <select name="equipment" id="equipment" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Выберите оборудование</option>
                        <?php $__currentLoopData = \App\Models\Trainer\Exercise::EQUIPMENT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            
            <!-- Группы мышц -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Группы мышц (RU, через запятую)</label>
                    <input type="text" name="muscle_groups_ru" id="muscle_groups_ru" 
                           placeholder="Например: грудь, плечи, трицепс"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Укажите группы мышц по-русски</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Группы мышц (UK, через запятую)</label>
                    <input type="text" name="muscle_groups_uk" id="muscle_groups_uk" 
                           placeholder="Наприклад: груди, плечі, трицепс"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Вкажіть групи м'язів українською</p>
                </div>
            </div>

            <!-- Описание -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание (RU)</label>
                    <textarea name="description_ru" id="description_ru" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Описание (UK)</label>
                    <textarea name="description_uk" id="description_uk" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>

            <!-- Инструкции -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Инструкции (RU)</label>
                    <textarea name="instructions_ru" id="instructions_ru" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Инструкции (UK)</label>
                    <textarea name="instructions_uk" id="instructions_uk" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm"></textarea>
                </div>
            </div>

            <!-- Поля для ввода данных -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Поля для ввода данных</label>
                <p class="text-xs text-gray-500 mb-3">Выберите какие поля будут доступны при добавлении этого упражнения в тренировку</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <?php $__currentLoopData = \App\Models\Trainer\Exercise::AVAILABLE_FIELDS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center space-x-2 p-2 border rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="fields_config[]" value="<?php echo e($key); ?>" class="field-config-checkbox">
                            <span class="text-sm"><?php echo e($label); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Чекбоксы -->
            <div class="flex space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" checked class="mr-2">
                    <span class="text-sm">Активно</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_system" id="is_system" checked class="mr-2">
                    <span class="text-sm">Системное</span>
                </label>
            </div>

            <!-- Кнопки -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Отмена
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Динамическая фильтрация и пагинация без перезагрузки
let searchTimeout;

function loadExercises(url) {
    const tableContainer = document.querySelector('.bg-white.rounded-xl.shadow-sm.overflow-hidden');
    const paginationContainer = document.getElementById('paginationContainer');
    const countContainer = document.getElementById('exercisesCount');
    
    if (!tableContainer) return;
    
    // Показываем индикатор загрузки
    tableContainer.style.opacity = '0.5';
    tableContainer.style.pointerEvents = 'none';
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Обновляем таблицу
        const newTable = doc.querySelector('.bg-white.rounded-xl.shadow-sm.overflow-hidden');
        if (newTable) {
            const currentTable = document.querySelector('.bg-white.rounded-xl.shadow-sm.overflow-hidden');
            if (currentTable && currentTable.parentNode) {
                currentTable.parentNode.replaceChild(newTable, currentTable);
            }
        }
        
        // Обновляем пагинацию
        const newPagination = doc.querySelector('#paginationContainer');
        if (newPagination && paginationContainer) {
            paginationContainer.innerHTML = newPagination.innerHTML;
            // Привязываем обработчики к новым ссылкам пагинации
            attachPaginationHandlers();
        }
        
        // Обновляем счетчик
        const newCount = doc.querySelector('#exercisesCount');
        if (newCount && countContainer) {
            countContainer.innerHTML = newCount.innerHTML;
        }
        
        // Обновляем URL без перезагрузки
        window.history.pushState({}, '', url);
        
        // Убираем индикатор загрузки
        const updatedTable = document.querySelector('.bg-white.rounded-xl.shadow-sm.overflow-hidden');
        if (updatedTable) {
            updatedTable.style.opacity = '1';
            updatedTable.style.pointerEvents = 'auto';
        }
    })
    .catch(error => {
        console.error('Ошибка загрузки:', error);
        const currentTable = document.querySelector('.bg-white.rounded-xl.shadow-sm.overflow-hidden');
        if (currentTable) {
            currentTable.style.opacity = '1';
            currentTable.style.pointerEvents = 'auto';
        }
    });
}

function attachPaginationHandlers() {
    // Перехватываем клики по ссылкам пагинации
    const paginationLinks = document.querySelectorAll('#paginationContainer a');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            if (url) {
                loadExercises(url);
            }
        });
    });
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    const url = '<?php echo e(route('admin.exercises.index')); ?>?' + params.toString();
    loadExercises(url);
}

// Для селектов - мгновенная фильтрация
document.querySelectorAll('.filter-select').forEach(select => {
    select.addEventListener('change', applyFilters);
});

// Для поиска - с задержкой 500мс
const searchInput = document.querySelector('.filter-input');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
}

// Привязываем обработчики к пагинации при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    attachPaginationHandlers();
});

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Создать упражнение';
    document.getElementById('exerciseForm').reset();
    document.getElementById('exerciseId').value = '';
    
    // Устанавливаем чекбоксы по умолчанию (как в CRM)
    const defaultFields = ['weight', 'reps', 'sets', 'rest'];
    document.querySelectorAll('.field-config-checkbox').forEach(cb => {
        cb.checked = defaultFields.includes(cb.value);
    });
    
    // Скрываем превью изображений
    document.getElementById('imagePreview').classList.add('hidden');
    document.getElementById('imagePreview2').classList.add('hidden');
    document.getElementById('imagePreviewFemale').classList.add('hidden');
    document.getElementById('imagePreviewFemale2').classList.add('hidden');
    
    document.getElementById('exerciseModal').classList.remove('hidden');
}

function openEditModal(id) {
    document.getElementById('modalTitle').textContent = 'Редактировать упражнение';
    
    const editUrl = `<?php echo e(url('/exercises')); ?>/${id}/edit`;
    
    fetch(editUrl, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            console.log('Exercise data:', data);
            console.log('Translations:', data.translations);
            
            document.getElementById('exerciseId').value = data.id;
            
            // Если есть переводы - используем их, иначе берем оригинальные поля
            document.getElementById('name_ru').value = data.translations?.ru?.name || data.name || '';
            document.getElementById('name_uk').value = data.translations?.ua?.name || '';
            document.getElementById('description_ru').value = data.translations?.ru?.description || data.description || '';
            document.getElementById('description_uk').value = data.translations?.ua?.description || '';
            document.getElementById('instructions_ru').value = data.translations?.ru?.instructions || data.instructions || '';
            document.getElementById('instructions_uk').value = data.translations?.ua?.instructions || '';
            
            document.getElementById('category').value = data.category;
            document.getElementById('equipment').value = data.equipment;
            document.getElementById('video_url').value = data.video_url || '';
            
            // Muscle groups - используем переводы или оригинальные данные
            if (data.translations?.ru?.muscle_groups) {
                document.getElementById('muscle_groups_ru').value = Array.isArray(data.translations.ru.muscle_groups) 
                    ? data.translations.ru.muscle_groups.join(', ') 
                    : data.translations.ru.muscle_groups;
            } else {
                document.getElementById('muscle_groups_ru').value = data.muscle_groups ? data.muscle_groups.join(', ') : '';
            }
            
            if (data.translations?.ua?.muscle_groups) {
                document.getElementById('muscle_groups_uk').value = Array.isArray(data.translations.ua.muscle_groups)
                    ? data.translations.ua.muscle_groups.join(', ')
                    : data.translations.ua.muscle_groups;
            } else {
                document.getElementById('muscle_groups_uk').value = '';
            }
            
            document.getElementById('is_active').checked = data.is_active;
            document.getElementById('is_system').checked = data.is_system;
            
            // Fields config checkboxes
            document.querySelectorAll('.field-config-checkbox').forEach(cb => cb.checked = false);
            if (data.fields_config) {
                data.fields_config.forEach(field => {
                    const cb = document.querySelector(`.field-config-checkbox[value="${field}"]`);
                    if (cb) cb.checked = true;
                });
            }
            
            // Показываем превью текущих изображений
            const imagePreview = document.getElementById('imagePreview');
            const imagePreview2 = document.getElementById('imagePreview2');
            const imagePreviewFemale = document.getElementById('imagePreviewFemale');
            const imagePreviewFemale2 = document.getElementById('imagePreviewFemale2');
            
            if (data.image_url) {
                imagePreview.querySelector('img').src = '/storage/' + data.image_url;
                imagePreview.classList.remove('hidden');
            } else {
                imagePreview.classList.add('hidden');
            }
            
            if (data.image_url_2) {
                imagePreview2.querySelector('img').src = '/storage/' + data.image_url_2;
                imagePreview2.classList.remove('hidden');
            } else {
                imagePreview2.classList.add('hidden');
            }
            
            if (data.image_url_female) {
                imagePreviewFemale.querySelector('img').src = '/storage/' + data.image_url_female;
                imagePreviewFemale.classList.remove('hidden');
            } else {
                imagePreviewFemale.classList.add('hidden');
            }
            
            if (data.image_url_female_2) {
                imagePreviewFemale2.querySelector('img').src = '/storage/' + data.image_url_female_2;
                imagePreviewFemale2.classList.remove('hidden');
            } else {
                imagePreviewFemale2.classList.add('hidden');
            }
            
            document.getElementById('exerciseModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading exercise:', error);
            alert('Ошибка загрузки упражнения: ' + error.message);
        });
}

function closeModal() {
    document.getElementById('exerciseModal').classList.add('hidden');
}

document.getElementById('exerciseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const id = document.getElementById('exerciseId').value;
    const baseUrl = '<?php echo e(url('/exercises')); ?>';
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    
    // Собираем muscle_groups для RU и UK
    const muscleGroupsRuText = formData.get('muscle_groups_ru') || '';
    const muscleGroupsUkText = formData.get('muscle_groups_uk') || '';
    const muscleGroupsRu = muscleGroupsRuText.split(',').map(g => g.trim()).filter(g => g);
    const muscleGroupsUk = muscleGroupsUkText.split(',').map(g => g.trim()).filter(g => g);
    
    // Собираем fields_config
    const fieldsConfig = Array.from(document.querySelectorAll('.field-config-checkbox:checked')).map(cb => cb.value);
    
    // Убираем старые значения чекбоксов
    formData.delete('is_active');
    formData.delete('is_system');
    
    // Добавляем boolean значения для чекбоксов
    formData.append('is_active', document.getElementById('is_active').checked ? '1' : '0');
    formData.append('is_system', document.getElementById('is_system').checked ? '1' : '0');
    
    // Добавляем _method для PUT запроса
    if (id) {
        formData.append('_method', 'PUT');
    }
    
    // Добавляем muscle_groups и fields_config
    muscleGroupsRu.forEach(g => formData.append('muscle_groups_ru[]', g));
    muscleGroupsUk.forEach(g => formData.append('muscle_groups_uk[]', g));
    fieldsConfig.forEach(f => formData.append('fields_config[]', f));
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            closeModal();
            // Обновляем список упражнений через AJAX
            const currentUrl = window.location.href;
            loadExercises(currentUrl);
        } else {
            alert('Ошибка: ' + (data.message || 'Неизвестная ошибка'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Ошибка сохранения');
    });
});

// Preview для изображений
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_2').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview2');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_female').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreviewFemale');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_female_2').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreviewFemale2');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

function deleteExercise(id) {
    if(!confirm('Удалить упражнение?')) return;
    
    const deleteUrl = `<?php echo e(url('/exercises')); ?>/${id}`;
    
    fetch(deleteUrl, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            // Обновляем список упражнений через AJAX
            const currentUrl = window.location.href;
            loadExercises(currentUrl);
        } else {
            alert('Ошибка: ' + data.message);
        }
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/admin/exercises/index.blade.php ENDPATH**/ ?>
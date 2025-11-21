

<?php $__env->startSection('title', 'Пользователь: ' . $user->name); ?>
<?php $__env->startSection('page-title', 'Просмотр пользователя'); ?>

<?php
    $roleLabels = [
        'admin' => __('common.role_admin'),
        'trainer' => __('common.role_trainer'),
        'athlete' => __('common.role_athlete'),
        'self-athlete' => __('common.role_self_athlete'),
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Заголовок и действия -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><?php echo e($user->name); ?></h2>
            <p class="text-gray-600"><?php echo e($user->email); ?></p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i>Редактировать
            </a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Назад к списку
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Основная информация -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Базовая информация -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user mr-2"></i>Базовая информация
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Имя</label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->name); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->email); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Телефон</label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->phone ?? 'Не указан'); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Статус</label>
                        <div class="mt-1">
                            <?php if($user->is_active): ?>
                                <span class="status-active">Активен</span>
                            <?php else: ?>
                                <span class="status-inactive">Неактивен</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Дата регистрации</label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->created_at->format('d.m.Y H:i')); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500"><?php echo e(__('common.last_activity')); ?></label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->last_activity_at ? $user->last_activity_at->format('d.m.Y H:i') : '—'); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Последнее обновление</label>
                        <p class="mt-1 text-sm text-gray-900"><?php echo e($user->updated_at->format('d.m.Y H:i')); ?></p>
                    </div>
                </div>
            </div>

            <!-- Роли и права -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-tag mr-2"></i>Роли и права
                </h3>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    <?php if($role->name === 'admin'): ?> bg-purple-100 text-purple-800
                                    <?php elseif($role->name === 'trainer'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($role->name === 'athlete'): ?> bg-green-100 text-green-800
                                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                    <?php echo e($roleLabels[$role->name] ?? ucfirst($role->name)); ?>

                                </span>
                                <span class="ml-3 text-sm text-gray-600"><?php echo e($role->description ?? 'Роль пользователя'); ?></span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Назначена: <?php echo e($role->pivot->created_at ? $role->pivot->created_at->format('d.m.Y') : '—'); ?>

                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-center py-4">У пользователя нет назначенных ролей</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($user->hasRole('athlete')): ?>
                <!-- Информация о спортсмене -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-running mr-2"></i>Информация о спортсмене
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Возраст</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->age ?? 'Не указан'); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Пол</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->gender ?? 'Не указан'); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Вес</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->current_weight ?? $user->weight ?? 'Не указан'); ?> кг</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Рост</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->current_height ?? $user->height ?? 'Не указан'); ?> см</p>
                        </div>
                        <?php if($user->bmi): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">ИМТ</label>
                                <p class="mt-1 text-sm text-gray-900"><?php echo e($user->bmi); ?></p>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Уровень подготовки</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->sport_level ?? 'Не указан'); ?></p>
                        </div>
                    </div>
                    
                    <?php if($user->goals): ?>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500">Цели</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <?php $__currentLoopData = $user->goals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e($goal); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if($user->hasRole('trainer')): ?>
                <!-- Информация о тренере -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user-tie mr-2"></i>Информация о тренере
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Специализация</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->specialization ?? 'Не указана'); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Опыт работы</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->experience_years ?? 'Не указан'); ?> лет</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Количество спортсменов</label>
                            <p class="mt-1 text-sm text-gray-900"><?php echo e($user->athletes->count()); ?> спортсменов</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Статистика активности -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line mr-2"></i>Статистика
                </h3>
                <div class="space-y-4">
                    <?php if($user->hasRole('trainer')): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Спортсмены</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($user->athletes->count()); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Тренировки</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($user->trainerWorkouts->count()); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($user->hasRole('athlete')): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Тренировки</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($user->workouts->count()); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Завершено</span>
                            <span class="text-sm font-medium text-gray-900"><?php echo e($user->workouts->where('status', 'completed')->count()); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">В системе</span>
                        <span class="text-sm font-medium text-gray-900"><?php echo e($user->created_at->diffForHumans()); ?></span>
                    </div>
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2"></i>Быстрые действия
                </h3>
                <div class="space-y-3">
                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Редактировать
                    </a>
                    
                    <?php if($user->id !== auth()->id()): ?>
                        <button onclick="toggleStatus(<?php echo e($user->id); ?>, <?php echo e($user->is_active ? 'false' : 'true'); ?>)" 
                                class="block w-full text-center px-4 py-2 bg-<?php echo e($user->is_active ? 'yellow' : 'green'); ?>-600 text-white rounded-lg hover:bg-<?php echo e($user->is_active ? 'yellow' : 'green'); ?>-700">
                            <i class="fas fa-<?php echo e($user->is_active ? 'pause' : 'play'); ?> mr-2"></i>
                            <?php echo e($user->is_active ? 'Деактивировать' : 'Активировать'); ?>

                        </button>
                        
                        <button onclick="deleteUser(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')" 
                                class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Удалить
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Контактная информация -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-address-book mr-2"></i>Контакты
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 mr-3"></i>
                        <a href="mailto:<?php echo e($user->email); ?>" class="text-sm text-blue-600 hover:text-blue-800"><?php echo e($user->email); ?></a>
                    </div>
                    <?php if($user->phone): ?>
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-3"></i>
                            <a href="tel:<?php echo e($user->phone); ?>" class="text-sm text-blue-600 hover:text-blue-800"><?php echo e($user->phone); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($user->hasRole('trainer') && isset($activities) && $activities->count() > 0): ?>
        <!-- Блок активности -->
        <div class="mt-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history mr-2"></i>Активность
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center
                                    <?php if($activity['color'] === 'blue'): ?> bg-blue-100 text-blue-600
                                    <?php elseif($activity['color'] === 'green'): ?> bg-green-100 text-green-600
                                    <?php elseif($activity['color'] === 'red'): ?> bg-red-100 text-red-600
                                    <?php else: ?> bg-yellow-100 text-yellow-600
                                    <?php endif; ?>">
                                    <i class="fas <?php echo e($activity['icon']); ?> text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo e($activity['message']); ?>

                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo e($activity['date']->format('d.m.Y H:i')); ?>

                                </p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Модальное окно подтверждения удаления -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Подтверждение удаления</h3>
                </div>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-500">
                    Вы действительно хотите удалить пользователя <span id="userName" class="font-medium"></span>?
                    Это действие нельзя отменить.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Отмена
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function toggleStatus(userId, newStatus) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_active: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка');
        });
    }

    function deleteUser(userId, userName) {
        document.getElementById('userName').textContent = userName;
        document.getElementById('deleteForm').action = `/admin/users/${userId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/admin/users/show.blade.php ENDPATH**/ ?>
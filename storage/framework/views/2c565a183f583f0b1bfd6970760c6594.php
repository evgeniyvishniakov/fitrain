

<?php $__env->startSection('title', 'Дашборд'); ?>
<?php $__env->startSection('page-title', 'Дашборд'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Статистические карточки -->
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
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($usersStats['total']); ?></p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+<?php echo e($usersStats['active']); ?> активных</span>
                <span class="text-gray-300 mx-2">•</span>
                <span class="text-gray-500"><?php echo e($usersStats['inactive']); ?> неактивных</span>
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
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($usersStats['trainers']); ?></p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+<?php echo e($usersStats['trainers']); ?> активных</span>
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
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($usersStats['athletes']); ?></p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+<?php echo e($usersStats['athletes']); ?> активных</span>
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
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($workoutsStats['total']); ?></p>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 font-medium">+<?php echo e($workoutsStats['completed']); ?> завершено</span>
                <span class="text-gray-300 mx-2">•</span>
                <span class="text-blue-600"><?php echo e($workoutsStats['in_progress']); ?> в процессе</span>
            </div>
        </div>
    </div>

    <!-- Графики и аналитика -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Активность по дням -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Активность за 7 дней</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-500">Тренировки</span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Распределение ролей -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Распределение пользователей</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-gray-700">Тренеры</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-900 font-semibold"><?php echo e($usersStats['trainers']); ?></span>
                        <span class="text-gray-500 ml-2">(<?php echo e($usersStats['total'] > 0 ? round(($usersStats['trainers'] / $usersStats['total']) * 100, 1) : 0); ?>%)</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($usersStats['total'] > 0 ? ($usersStats['trainers'] / $usersStats['total']) * 100 : 0); ?>%"></div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-gray-700">Спортсмены</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-900 font-semibold"><?php echo e($usersStats['athletes']); ?></span>
                        <span class="text-gray-500 ml-2">(<?php echo e($usersStats['total'] > 0 ? round(($usersStats['athletes'] / $usersStats['total']) * 100, 1) : 0); ?>%)</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo e($usersStats['total'] > 0 ? ($usersStats['athletes'] / $usersStats['total']) * 100 : 0); ?>%"></div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-purple-500 rounded-full mr-3"></div>
                        <span class="text-gray-700">Администраторы</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-900 font-semibold"><?php echo e($usersStats['admins']); ?></span>
                        <span class="text-gray-500 ml-2">(<?php echo e($usersStats['total'] > 0 ? round(($usersStats['admins'] / $usersStats['total']) * 100, 1) : 0); ?>%)</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-500 h-2 rounded-full" style="width: <?php echo e($usersStats['total'] > 0 ? ($usersStats['admins'] / $usersStats['total']) * 100 : 0); ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Последние пользователи -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Последние регистрации</h3>
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Показать всех
                </a>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($user->name); ?></p>
                            <p class="text-sm text-gray-500"><?php echo e($user->email); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex flex-col items-end">
                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php if($role->name === 'admin'): ?> bg-purple-100 text-purple-800
                                    <?php elseif($role->name === 'trainer'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($role->name === 'athlete'): ?> bg-green-100 text-green-800
                                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                    <?php echo e(ucfirst($role->name)); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <span class="text-xs text-gray-500 mt-1"><?php echo e($user->created_at->diffForHumans()); ?></span>
                        </div>
                        <div class="flex items-center">
                            <?php if($user->is_active): ?>
                                <span class="status-active">Активен</span>
                            <?php else: ?>
                                <span class="status-inactive">Неактивен</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-6 py-8 text-center">
                    <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">Нет пользователей</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // График активности
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityData = <?php echo json_encode($dailyActivity, 15, 512) ?>;
    
    // Подготавливаем данные для графика
    const labels = activityData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit' });
    });
    
    const data = activityData.map(item => item.count);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Тренировки',
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
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>
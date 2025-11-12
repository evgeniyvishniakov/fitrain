

<?php $__env->startSection('title', 'Настройки сайта'); ?>
<?php $__env->startSection('page-title', 'Настройки сайта'); ?>

<?php $__env->startSection('content'); ?>
    <div class="max-w-5xl mx-auto space-y-6">
        <form action="<?php echo e(route('admin.site.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Основные данные</h3>
                        <p class="text-sm text-gray-500 mt-1">Используются в заголовках, письмах и публичных разделах.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Название сайта *</label>
                        <input type="text"
                               name="site_name"
                               value="<?php echo e(old('site_name', $settings['site_name'])); ?>"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['site_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                        <textarea name="site_description"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old('site_description', $settings['site_description'])); ?></textarea>
                        <?php $__errorArgs = ['site_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">SEO-настройки</h3>
                        <p class="text-sm text-gray-500 mt-1">Заполняются для мета-тегов и улучшения поисковой выдачи.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                        <input type="text"
                               name="meta_title"
                               value="<?php echo e(old('meta_title', $settings['meta_title'])); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['meta_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                        <input type="text"
                               name="meta_keywords"
                               value="<?php echo e(old('meta_keywords', $settings['meta_keywords'])); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['meta_keywords'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                    <textarea name="meta_description"
                              rows="3"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old('meta_description', $settings['meta_description'])); ?></textarea>
                    <?php $__errorArgs = ['meta_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-sm text-red-600 mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Медиа</h3>
                        <p class="text-sm text-gray-500 mt-1">Загруженные файлы используются в админке и CRM.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип (админка)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo" accept="image/*" class="hidden">
                        </label>
                        <?php if(!empty($settings['logo'])): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['logo'])); ?>" alt="Текущий логотип" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Favicon</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Загрузить favicon</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/ICO, 32×32 или 64×64, до 1 МБ</span>
                            </div>
                            <input type="file" name="favicon" accept=".ico,.png,.jpg,.jpeg" class="hidden">
                        </label>
                        <?php if(!empty($settings['favicon'])): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['favicon'])); ?>" alt="Текущий favicon" class="h-10 w-10 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['favicon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип CRM (светлая тема)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo_light" accept="image/*" class="hidden">
                        </label>
                        <?php if(!empty($settings['logo_light'])): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['logo_light'])); ?>" alt="Логотип для светлой темы" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['logo_light'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Логотип CRM (тёмная тема)</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите логотип</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                            </div>
                            <input type="file" name="logo_dark" accept="image/*" class="hidden">
                        </label>
                        <?php if(!empty($settings['logo_dark'])): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['logo_dark'])); ?>" alt="Логотип для тёмной темы" class="h-12 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['logo_dark'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/admin/site/index.blade.php ENDPATH**/ ?>
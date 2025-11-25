<?php $__env->startSection('title', 'Настройки сайта'); ?>
<?php $__env->startSection('page-title', 'Настройки сайта'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">
        <form action="<?php echo e(route('admin.site.update')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>

        <!-- Вкладки -->
        <div class="bg-white rounded-xl shadow-sm border-b border-gray-200">
            <nav class="flex space-x-1 overflow-x-auto scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
                <button type="button" 
                        onclick="switchTab('basic')"
                        id="tab-btn-basic"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600 transition whitespace-nowrap">
                    Основное
                </button>
                <button type="button" 
                        onclick="switchTab('slider')"
                        id="tab-btn-slider"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition whitespace-nowrap">
                    Слайдер
                </button>
                <button type="button" 
                        onclick="switchTab('features')"
                        id="tab-btn-features"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition whitespace-nowrap">
                    Возможности
                </button>
                <button type="button" 
                        onclick="switchTab('how-it-works')"
                        id="tab-btn-how-it-works"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition whitespace-nowrap">
                    Как это работает
                </button>
                <button type="button" 
                        onclick="switchTab('trainers')"
                        id="tab-btn-trainers"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition whitespace-nowrap">
                    Для тренера
                </button>
                <button type="button" 
                        onclick="switchTab('athletes')"
                        id="tab-btn-athletes"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition whitespace-nowrap">
                    Для спортсмена
                </button>
            </nav>
        </div>

        <!-- Вкладка: Основное -->
        <div id="tab-content-basic" class="tab-content space-y-6">
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
                               value="<?php echo e(old('site_name', $settings['site_name'] ?? '')); ?>"
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
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old('site_description', $settings['site_description'] ?? '')); ?></textarea>
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
                               value="<?php echo e(old('meta_title', $settings['meta_title'] ?? '')); ?>"
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
                               value="<?php echo e(old('meta_keywords', $settings['meta_keywords'] ?? '')); ?>"
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
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old('meta_description', $settings['meta_description'] ?? '')); ?></textarea>
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
                        <?php if(!empty($settings['logo'] ?? '')): ?>
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
                        <?php if(!empty($settings['favicon'] ?? '')): ?>
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
                        <?php if(!empty($settings['logo_light'] ?? '')): ?>
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
                        <?php if(!empty($settings['logo_dark'] ?? '')): ?>
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
        </div>

        <!-- Вкладка: Слайдер -->
        <div id="tab-content-slider" class="tab-content space-y-6" style="display: none;">
            <?php
                $slideNames = [1 => 'Календарь', 2 => 'Тренировка', 3 => 'Питание'];
            ?>
            
            <?php for($slide = 1; $slide <= 3; $slide++): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Слайд <?php echo e($slide); ?> - <?php echo e($slideNames[$slide]); ?></h3>
                        <p class="text-sm text-gray-500 mt-1">Настройки <?php echo e(strtolower($slideNames[$slide])); ?> слайда.</p>
                    </div>
                </div>

                <!-- Изображение слайда -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Изображение для слайда <?php echo e($slide); ?></label>
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                        <div class="text-center px-4">
                            <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                            <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                        </div>
                        <input type="file" name="landing_slider_<?php echo e($slide); ?>_image" accept="image/*" class="hidden">
                    </label>
                    <?php
                        $sliderImageKey = "landing_slider_{$slide}_image";
                        $sliderImageValue = $settings[$sliderImageKey] ?? '';
                    ?>
                    <?php if(!empty($sliderImageValue)): ?>
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <img src="<?php echo e(asset('storage/' . $sliderImageValue)); ?>" alt="Текущее изображение" class="h-20 object-contain">
                            <span class="text-xs text-gray-500">Текущий файл</span>
                        </div>
                    <?php endif; ?>
                    <?php
                        $errorKey = "landing_slider_{$slide}_image";
                    ?>
                    <?php $__errorArgs = [$errorKey];
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

                <!-- Мультиязычные поля -->
                <?php $__currentLoopData = ['ru' => 'Русский', 'ua' => 'Українська']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $langName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($langName); ?></span>
                    </div>
                    
                    <?php
                        $slideTitlesRu = [
                            1 => 'Профессиональная CRM для фитнес-тренеров',
                            2 => 'Детальное планирование тренировок',
                            3 => 'Персональные планы питания'
                        ];
                        $slideSubtitlesRu = [
                            1 => 'Управляйте спортсменами, создавайте тренировки, отслеживайте прогресс и многое другое в одной удобной системе.',
                            2 => 'Создавайте индивидуальные программы тренировок с подробным описанием упражнений, подходов и весов. Отслеживайте выполнение в реальном времени.',
                            3 => 'Разрабатывайте индивидуальные программы питания с расчетом калорий, БЖУ и приемов пищи. Помогайте спортсменам достигать своих целей.'
                        ];
                        $slideTitleKey = "landing_slider_{$slide}_title_{$lang}";
                        $slideSubtitleKey = "landing_slider_{$slide}_subtitle_{$lang}";
                        $defaultTitle = ($lang === 'ru' && isset($slideTitlesRu[$slide])) ? $slideTitlesRu[$slide] : '';
                        $defaultSubtitle = ($lang === 'ru' && isset($slideSubtitlesRu[$slide])) ? $slideSubtitlesRu[$slide] : '';
                        $slideTitleValue = old($slideTitleKey, $settings[$slideTitleKey] ?? $defaultTitle);
                        $slideSubtitleValue = old($slideSubtitleKey, $settings[$slideSubtitleKey] ?? $defaultSubtitle);
                    ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Заголовок</label>
                        <input type="text"
                               name="landing_slider_<?php echo e($slide); ?>_title_<?php echo e($lang); ?>"
                               value="<?php echo e($slideTitleValue); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подзаголовок</label>
                        <textarea name="landing_slider_<?php echo e($slide); ?>_subtitle_<?php echo e($lang); ?>"
                                  rows="3"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e($slideSubtitleValue); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Текст кнопки 1</label>
                        <?php
                            $button1Key = "landing_slider_{$slide}_button_1_{$lang}";
                            $button1Value = $settings[$button1Key] ?? '';
                            if (empty($button1Value) && $lang === 'ru') {
                                $button1Value = 'Попробовать бесплатно';
                            }
                        ?>
                        <input type="text"
                               name="landing_slider_<?php echo e($slide); ?>_button_1_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_slider_{$slide}_button_1_{$lang}", $button1Value)); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Текст кнопки 2</label>
                        <?php
                            $button2Key = "landing_slider_{$slide}_button_2_{$lang}";
                            $button2Value = $settings[$button2Key] ?? '';
                            if (empty($button2Value) && $lang === 'ru') {
                                $button2Value = 'Узнать больше';
                            }
                        ?>
                        <input type="text"
                               name="landing_slider_<?php echo e($slide); ?>_button_2_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_slider_{$slide}_button_2_{$lang}", $button2Value)); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endfor; ?>
            
        </div>

        <!-- Остальные вкладки будут похожими по структуре -->
        <!-- Для краткости добавлю их базовую структуру -->
        
        <!-- Вкладка: Возможности -->
        <div id="tab-content-features" class="tab-content space-y-6" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Секция Возможности</h3>
                        <p class="text-sm text-gray-500 mt-1">Настройки секции возможностей на лендинге.</p>
                    </div>
                </div>

                <!-- Изображение -->
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Изображение для секции</label>
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                        <div class="text-center px-4">
                            <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                            <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                        </div>
                        <input type="file" name="landing_features_image" accept="image/*" class="hidden">
                    </label>
                    <?php if(!empty($settings['landing_features_image'] ?? '')): ?>
                        <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                            <img src="<?php echo e(asset('storage/' . $settings['landing_features_image'])); ?>" alt="Текущее изображение" class="h-20 object-contain">
                            <span class="text-xs text-gray-500">Текущий файл</span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php $__currentLoopData = ['ru' => 'Русский', 'ua' => 'Українська']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $langName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 space-y-4 mb-6">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($langName); ?></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Заголовок секции</label>
                        <input type="text"
                               name="landing_features_title_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_features_title_{$lang}", $settings['landing_features_title_' . $lang] ?? ($lang === 'ru' ? 'Возможности системы' : ''))); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подзаголовок секции</label>
                        <textarea name="landing_features_subtitle_<?php echo e($lang); ?>"
                                  rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old("landing_features_subtitle_{$lang}", $settings['landing_features_subtitle_' . $lang] ?? ($lang === 'ru' ? 'Все необходимое для управления тренировочным процессом' : ''))); ?></textarea>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Возможности (9 карточек)</h4>
                        
                        <?php
                            $features_ru = [
                                1 => ['title' => 'Календарь тренировок', 'description' => 'Удобное планирование и управление тренировками с визуальным календарем'],
                                2 => ['title' => 'Управление спортсменами', 'description' => 'Полный профиль каждого спортсмена с историей тренировок и прогрессом'],
                                3 => ['title' => 'Отслеживание прогресса', 'description' => 'Визуализация прогресса спортсменов с графиками и статистикой'],
                                4 => ['title' => 'Шаблоны упражнений', 'description' => 'Библиотека упражнений и создание шаблонов упражнений'],
                                5 => ['title' => 'Планы питания', 'description' => 'Создание и управление планами питания для спортсменов'],
                                6 => ['title' => 'Финансовый учет', 'description' => 'Запись платежей, просмотр истории оплат и финансовой статистики с графиками'],
                                7 => ['title' => 'Более 500 упражнений с анимацией', 'description' => 'Обширная библиотека упражнений с подробными анимированными инструкциями'],
                                8 => ['title' => 'История выполнения упражнений', 'description' => 'Просмотр истории выполнения упражнений за все время тренировок'],
                                9 => ['title' => 'Отслеживание выполнения упражнений', 'description' => 'Отслеживание статуса выполнения: полностью или частично выполнено упражнение спортсменом']
                            ];
                        ?>
                        
                        <?php for($i = 1; $i <= 9; $i++): ?>
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Возможность <?php echo e($i); ?> - Заголовок</label>
                            <input type="text"
                                   name="landing_feature_<?php echo e($i); ?>_title_<?php echo e($lang); ?>"
                                   value="<?php echo e(old("landing_feature_{$i}_title_{$lang}", $settings['landing_feature_' . $i . '_title_' . $lang] ?? ($lang === 'ru' ? ($features_ru[$i]['title'] ?? '') : ''))); ?>"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-2">
                            
                            <label class="block text-sm font-medium text-gray-700 mb-2">Возможность <?php echo e($i); ?> - Описание</label>
                            <textarea name="landing_feature_<?php echo e($i); ?>_description_<?php echo e($lang); ?>"
                                      rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old("landing_feature_{$i}_description_{$lang}", $settings['landing_feature_' . $i . '_description_' . $lang] ?? ($lang === 'ru' ? ($features_ru[$i]['description'] ?? '') : ''))); ?></textarea>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Вкладка: Как это работает -->
        <div id="tab-content-how-it-works" class="tab-content space-y-6" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Секция "Как это работает"</h3>
                        <p class="text-sm text-gray-500 mt-1">Настройки секции с мобильными экранами тренера и спортсмена.</p>
                    </div>
                </div>

                <!-- Изображения мобильных экранов -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Изображение экрана тренера</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ (мобильный экран)</span>
                            </div>
                            <input type="file" name="landing_how_it_works_trainer_image" accept="image/*" class="hidden">
                        </label>
                        <?php if(!empty($settings['landing_how_it_works_trainer_image'] ?? '')): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['landing_how_it_works_trainer_image'])); ?>" alt="Экран тренера" class="h-20 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['landing_how_it_works_trainer_image'];
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
                        <label class="block text-sm font-medium text-gray-700">Изображение экрана спортсмена</label>
                        <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                            <div class="text-center px-4">
                                <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                                <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ (мобильный экран)</span>
                            </div>
                            <input type="file" name="landing_how_it_works_athlete_image" accept="image/*" class="hidden">
                        </label>
                        <?php if(!empty($settings['landing_how_it_works_athlete_image'] ?? '')): ?>
                            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                <img src="<?php echo e(asset('storage/' . $settings['landing_how_it_works_athlete_image'])); ?>" alt="Экран спортсмена" class="h-20 object-contain">
                                <span class="text-xs text-gray-500">Текущий файл</span>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['landing_how_it_works_athlete_image'];
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

                <?php $__currentLoopData = ['ru' => 'Русский', 'ua' => 'Українська']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $langName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 space-y-4 mb-6">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($langName); ?></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Заголовок секции</label>
                        <input type="text"
                               name="landing_how_it_works_title_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_how_it_works_title_{$lang}", $settings['landing_how_it_works_title_' . $lang] ?? ($lang === 'ru' ? 'Как это работает' : ''))); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подзаголовок секции</label>
                        <textarea name="landing_how_it_works_subtitle_<?php echo e($lang); ?>"
                                  rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old("landing_how_it_works_subtitle_{$lang}", $settings['landing_how_it_works_subtitle_' . $lang] ?? ($lang === 'ru' ? 'Простой процесс работы с системой' : ''))); ?></textarea>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6 mt-6 pt-4 border-t border-gray-200">
                        <!-- Преимущества для тренера -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Преимущества для тренера (до 5 шт.)</h4>
                            <?php
                                $trainer_benefits_ru = [
                                    1 => 'Быстрое создание аккаунтов спортсменов',
                                    2 => 'Удобное управление всеми спортсменами',
                                    3 => 'Планирование тренировок в календаре',
                                    4 => 'Отслеживание прогресса каждого спортсмена',
                                    5 => 'Финансовый учет и статистика'
                                ];
                            ?>
                            
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Пункт <?php echo e($i); ?></label>
                                <input type="text"
                                       name="landing_how_it_works_trainer_benefit_<?php echo e($i); ?>_<?php echo e($lang); ?>"
                                       value="<?php echo e(old("landing_how_it_works_trainer_benefit_{$i}_{$lang}", $settings['landing_how_it_works_trainer_benefit_' . $i . '_' . $lang] ?? ($lang === 'ru' ? ($trainer_benefits_ru[$i] ?? '') : ''))); ?>"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <?php endfor; ?>
                        </div>
                        
                        <!-- Преимущества для спортсмена -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-4">Преимущества для спортсмена (до 5 шт.)</h4>
                            <?php
                                $athlete_benefits_ru = [
                                    1 => 'Доступ к тренировкам в любое время',
                                    2 => 'Просмотр планов питания от тренера',
                                    3 => 'Отслеживание личного прогресса',
                                    4 => 'История всех тренировок',
                                    5 => 'Связь с тренером через систему'
                                ];
                            ?>
                            
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Пункт <?php echo e($i); ?></label>
                                <input type="text"
                                       name="landing_how_it_works_athlete_benefit_<?php echo e($i); ?>_<?php echo e($lang); ?>"
                                       value="<?php echo e(old("landing_how_it_works_athlete_benefit_{$i}_{$lang}", $settings['landing_how_it_works_athlete_benefit_' . $i . '_' . $lang] ?? ($lang === 'ru' ? ($athlete_benefits_ru[$i] ?? '') : ''))); ?>"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Вкладка: Для тренера -->
        <div id="tab-content-trainers" class="tab-content space-y-6" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Секция Для тренера</h3>
                        <p class="text-sm text-gray-500 mt-1">Настройки секции для тренера на лендинге.</p>
                    </div>
                </div>

                <!-- Изображения для слайдера (до 5 шт.) -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700">Изображения для слайдера (до 5 шт.)</label>
                    <p class="text-xs text-gray-500 mb-4">Загрузите несколько изображений для создания слайдера. Изображения будут автоматически переключаться каждые 5 секунд.</p>
                    
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Изображение <?php echo e($i); ?></label>
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                                <div class="text-center px-4">
                                    <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                                    <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                                </div>
                                <input type="file" name="landing_trainers_image_<?php echo e($i); ?>" accept="image/*" class="hidden" data-image-number="<?php echo e($i); ?>">
                            </label>
                            <?php
                                $trainerImageKey = "landing_trainers_image_{$i}";
                                $trainerImageValue = $settings[$trainerImageKey] ?? '';
                            ?>
                            <?php if(!empty($trainerImageValue)): ?>
                                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <img src="<?php echo e(asset('storage/' . $trainerImageValue)); ?>" alt="Изображение <?php echo e($i); ?>" class="h-20 object-contain">
                                    <div class="flex-1">
                                        <span class="text-xs text-gray-500 block">Текущий файл <?php echo e($i); ?></span>
                                        <button type="button" onclick="removeTrainerImage(<?php echo e($i); ?>)" class="mt-2 text-xs text-red-600 hover:text-red-800">Удалить изображение</button>
                                    </div>
                                    <input type="hidden" name="landing_trainers_existing_image_<?php echo e($i); ?>" value="<?php echo e($trainerImageValue); ?>" id="trainer_existing_<?php echo e($i); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                    <?php $__errorArgs = ['landing_trainers_images.*'];
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

                <?php $__currentLoopData = ['ru' => 'Русский', 'ua' => 'Українська']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $langName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 space-y-4 mb-6">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($langName); ?></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Заголовок</label>
                        <input type="text"
                               name="landing_trainers_title_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_trainers_title_{$lang}", $settings['landing_trainers_title_' . $lang] ?? ($lang === 'ru' ? 'Для тренеров' : ''))); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подзаголовок</label>
                        <textarea name="landing_trainers_subtitle_<?php echo e($lang); ?>"
                                  rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old("landing_trainers_subtitle_{$lang}", $settings['landing_trainers_subtitle_' . $lang] ?? ($lang === 'ru' ? 'Управляйте всеми аспектами вашего тренировочного бизнеса в одном месте' : ''))); ?></textarea>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Список пунктов (5 шт.)</h4>
                        
                        <?php
                            $trainer_items_ru = [
                                1 => 'Управление базой спортсменов с полными профилями',
                                2 => 'Создание и планирование тренировок в календаре',
                                3 => 'Отслеживание прогресса каждого спортсмена',
                                4 => 'Библиотека упражнений с видео и описаниями',
                                5 => 'Финансовый учет и статистика платежей'
                            ];
                        ?>
                        
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пункт <?php echo e($i); ?></label>
                            <input type="text"
                                   name="landing_trainer_item_<?php echo e($i); ?>_<?php echo e($lang); ?>"
                                   value="<?php echo e(old("landing_trainer_item_{$i}_{$lang}", $settings['landing_trainer_item_' . $i . '_' . $lang] ?? ($lang === 'ru' ? ($trainer_items_ru[$i] ?? '') : ''))); ?>"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Вкладка: Для спортсмена -->
        <div id="tab-content-athletes" class="tab-content space-y-6" style="display: none;">
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Секция Для спортсмена</h3>
                        <p class="text-sm text-gray-500 mt-1">Настройки секции для спортсмена на лендинге.</p>
                    </div>
                </div>

                <!-- Изображения для слайдера (до 5 шт.) -->
                <div class="space-y-4">
                    <label class="block text-sm font-medium text-gray-700">Изображения для слайдера (до 5 шт.)</label>
                    <p class="text-xs text-gray-500 mb-4">Загрузите несколько изображений для создания слайдера. Изображения будут автоматически переключаться каждые 5 секунд.</p>
                    
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="space-y-3 p-4 border border-gray-200 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700">Изображение <?php echo e($i); ?></label>
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 hover:border-blue-300 transition cursor-pointer">
                                <div class="text-center px-4">
                                    <span class="block text-base font-medium text-gray-700">Выберите изображение</span>
                                    <span class="block text-xs text-gray-500 mt-1">PNG/JPG до 2 МБ</span>
                                </div>
                                <input type="file" name="landing_athletes_image_<?php echo e($i); ?>" accept="image/*" class="hidden" data-image-number="<?php echo e($i); ?>">
                            </label>
                            <?php
                                $athleteImageKey = "landing_athletes_image_{$i}";
                                $athleteImageValue = $settings[$athleteImageKey] ?? '';
                            ?>
                            <?php if(!empty($athleteImageValue)): ?>
                                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <img src="<?php echo e(asset('storage/' . $athleteImageValue)); ?>" alt="Изображение <?php echo e($i); ?>" class="h-20 object-contain">
                                    <div class="flex-1">
                                        <span class="text-xs text-gray-500 block">Текущий файл <?php echo e($i); ?></span>
                                        <button type="button" onclick="removeAthleteImage(<?php echo e($i); ?>)" class="mt-2 text-xs text-red-600 hover:text-red-800">Удалить изображение</button>
                                    </div>
                                    <input type="hidden" name="landing_athletes_existing_image_<?php echo e($i); ?>" value="<?php echo e($athleteImageValue); ?>" id="athlete_existing_<?php echo e($i); ?>">
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                    <?php $__errorArgs = ['landing_athletes_images.*'];
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

                <?php $__currentLoopData = ['ru' => 'Русский', 'ua' => 'Українська']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang => $langName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-gray-200 rounded-lg p-4 space-y-4 mb-6">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-900"><?php echo e($langName); ?></span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Заголовок</label>
                        <input type="text"
                               name="landing_athletes_title_<?php echo e($lang); ?>"
                               value="<?php echo e(old("landing_athletes_title_{$lang}", $settings['landing_athletes_title_' . $lang] ?? ($lang === 'ru' ? 'Для спортсменов' : ''))); ?>"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Подзаголовок</label>
                        <textarea name="landing_athletes_subtitle_<?php echo e($lang); ?>"
                                  rows="2"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?php echo e(old("landing_athletes_subtitle_{$lang}", $settings['landing_athletes_subtitle_' . $lang] ?? ($lang === 'ru' ? 'Следите за своими тренировками, прогрессом и планами питания' : ''))); ?></textarea>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Список пунктов (5 шт.)</h4>
                        
                        <?php
                            $athlete_items_ru = [
                                1 => 'Просмотр запланированных тренировок',
                                2 => 'Отслеживание личного прогресса и результатов',
                                3 => 'Планы питания от вашего тренера',
                                4 => 'История измерений тела и веса',
                                5 => 'Связь с тренером через систему'
                            ];
                        ?>
                        
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Пункт <?php echo e($i); ?></label>
                            <input type="text"
                                   name="landing_athlete_item_<?php echo e($i); ?>_<?php echo e($lang); ?>"
                                   value="<?php echo e(old("landing_athlete_item_{$i}_{$lang}", $settings['landing_athlete_item_' . $i . '_' . $lang] ?? ($lang === 'ru' ? ($athlete_items_ru[$i] ?? '') : ''))); ?>"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Кнопка сохранения -->
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>


<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>

<script>
function switchTab(tabName) {
    // Скрываем все вкладки
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Убираем активный класс со всех кнопок
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Показываем выбранную вкладку
    const content = document.getElementById('tab-content-' + tabName);
    if (content) {
        content.style.display = 'block';
    }
    
    // Активируем кнопку
    const button = document.getElementById('tab-btn-' + tabName);
    if (button) {
        button.classList.remove('border-transparent', 'text-gray-500');
        button.classList.add('border-blue-500', 'text-blue-600');
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    switchTab('basic');
});

// Функции для удаления изображений
function removeTrainerImage(number) {
    const existingInput = document.getElementById('trainer_existing_' + number);
    if (!existingInput) return;
    
    const fileInput = document.querySelector('input[name="landing_trainers_image_' + number + '"]');
    const container = existingInput.closest('.p-4');
    
    if (confirm('Вы уверены, что хотите удалить это изображение?')) {
        existingInput.value = '';
        if (fileInput) fileInput.value = '';
        const existingDiv = container.querySelector('.bg-gray-50');
        if (existingDiv) existingDiv.style.display = 'none';
    }
}

function removeAthleteImage(number) {
    const existingInput = document.getElementById('athlete_existing_' + number);
    if (!existingInput) return;
    
    const fileInput = document.querySelector('input[name="landing_athletes_image_' + number + '"]');
    const container = existingInput.closest('.p-4');
    
    if (confirm('Вы уверены, что хотите удалить это изображение?')) {
        existingInput.value = '';
        if (fileInput) fileInput.value = '';
        const existingDiv = container.querySelector('.bg-gray-50');
        if (existingDiv) existingDiv.style.display = 'none';
    }
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/admin/site/index.blade.php ENDPATH**/ ?>
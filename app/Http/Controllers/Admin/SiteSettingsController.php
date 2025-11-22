<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends BaseController
{
    /**
     * Показ страницы настроек сайта.
     */
    public function index()
    {
        $languages = ['ru', 'ua'];
        
        $settings = [
            // Основные настройки
            'site_name'        => SystemSetting::get('site.name', ''),
            'site_description' => SystemSetting::get('site.description', ''),
            'meta_title'       => SystemSetting::get('site.meta_title', ''),
            'meta_description' => SystemSetting::get('site.meta_description', ''),
            'meta_keywords'    => SystemSetting::get('site.meta_keywords', ''),
            'logo'             => SystemSetting::get('site.logo', ''),
            'logo_light'       => SystemSetting::get('site.logo_light', ''),
            'logo_dark'        => SystemSetting::get('site.logo_dark', ''),
            'favicon'          => SystemSetting::get('site.favicon', ''),
        ];
        
        // Дефолтные значения для русского языка
        $defaultsRu = [
            // Слайдер - 3 слайда
            // Слайд 1 - Календарь
            'landing.slider.1.title.ru' => 'Профессиональная CRM для фитнес-тренеров',
            'landing.slider.1.subtitle.ru' => 'Управляйте спортсменами, создавайте тренировки, отслеживайте прогресс и многое другое в одной удобной системе.',
            'landing.slider.1.button_1.ru' => 'Попробовать бесплатно',
            'landing.slider.1.button_2.ru' => 'Узнать больше',
            
            // Слайд 2 - Тренировка
            'landing.slider.2.title.ru' => 'Детальное планирование тренировок',
            'landing.slider.2.subtitle.ru' => 'Создавайте индивидуальные программы тренировок с подробным описанием упражнений, подходов и весов. Отслеживайте выполнение в реальном времени.',
            'landing.slider.2.button_1.ru' => 'Попробовать бесплатно',
            'landing.slider.2.button_2.ru' => 'Узнать больше',
            
            // Слайд 3 - Питание
            'landing.slider.3.title.ru' => 'Персональные планы питания',
            'landing.slider.3.subtitle.ru' => 'Разрабатывайте индивидуальные программы питания с расчетом калорий, БЖУ и приемов пищи. Помогайте спортсменам достигать своих целей.',
            'landing.slider.3.button_1.ru' => 'Попробовать бесплатно',
            'landing.slider.3.button_2.ru' => 'Узнать больше',
            
            // Возможности
            'landing.features.title.ru' => 'Возможности системы',
            'landing.features.subtitle.ru' => 'Все необходимое для управления тренировочным процессом',
            
            // 9 возможностей
            'landing.feature.1.title.ru' => 'Календарь тренировок',
            'landing.feature.1.description.ru' => 'Удобное планирование и управление тренировками с визуальным календарем',
            'landing.feature.2.title.ru' => 'Управление спортсменами',
            'landing.feature.2.description.ru' => 'Полный профиль каждого спортсмена с историей тренировок и прогрессом',
            'landing.feature.3.title.ru' => 'Отслеживание прогресса',
            'landing.feature.3.description.ru' => 'Визуализация прогресса спортсменов с графиками и статистикой',
            'landing.feature.4.title.ru' => 'Шаблоны упражнений',
            'landing.feature.4.description.ru' => 'Библиотека упражнений и создание шаблонов упражнений',
            'landing.feature.5.title.ru' => 'Планы питания',
            'landing.feature.5.description.ru' => 'Создание и управление планами питания для спортсменов',
            'landing.feature.6.title.ru' => 'Финансовый учет',
            'landing.feature.6.description.ru' => 'Запись платежей, просмотр истории оплат и финансовой статистики с графиками',
            'landing.feature.7.title.ru' => 'Более 500 упражнений с анимацией',
            'landing.feature.7.description.ru' => 'Обширная библиотека упражнений с подробными анимированными инструкциями',
            'landing.feature.8.title.ru' => 'История выполнения упражнений',
            'landing.feature.8.description.ru' => 'Просмотр истории выполнения упражнений за все время тренировок',
            'landing.feature.9.title.ru' => 'Отслеживание выполнения упражнений',
            'landing.feature.9.description.ru' => 'Отслеживание статуса выполнения: полностью или частично выполнено упражнение спортсменом',
            
            // Для тренера
            'landing.trainers.title.ru' => 'Для тренеров',
            'landing.trainers.subtitle.ru' => 'Управляйте всеми аспектами вашего тренировочного бизнеса в одном месте',
            'landing.trainer.item.1.ru' => 'Управление базой спортсменов с полными профилями',
            'landing.trainer.item.2.ru' => 'Создание и планирование тренировок в календаре',
            'landing.trainer.item.3.ru' => 'Отслеживание прогресса каждого спортсмена',
            'landing.trainer.item.4.ru' => 'Библиотека упражнений с видео и описаниями',
            'landing.trainer.item.5.ru' => 'Финансовый учет и статистика платежей',
            
            // Для спортсмена
            'landing.athletes.title.ru' => 'Для спортсменов',
            'landing.athletes.subtitle.ru' => 'Следите за своими тренировками, прогрессом и планами питания',
            'landing.athlete.item.1.ru' => 'Просмотр запланированных тренировок',
            'landing.athlete.item.2.ru' => 'Отслеживание личного прогресса и результатов',
            'landing.athlete.item.3.ru' => 'Планы питания от вашего тренера',
            'landing.athlete.item.4.ru' => 'История измерений тела и веса',
            'landing.athlete.item.5.ru' => 'Связь с тренером через систему',
        ];
        
        // Заполняем дефолтные значения для русского языка, если их нет
        foreach ($defaultsRu as $key => $value) {
            $current = SystemSetting::get($key, '');
            if (empty($current)) {
                SystemSetting::set($key, $value, 'string', 'Настройка лендинга', true);
            }
        }
        
        // Загружаем настройки лендинга для каждого языка
        foreach ($languages as $lang) {
            // Слайдер - 3 слайда
            for ($slide = 1; $slide <= 3; $slide++) {
                $settings["landing_slider_{$slide}_title_{$lang}"] = SystemSetting::get("landing.slider.{$slide}.title.{$lang}", '');
                $settings["landing_slider_{$slide}_subtitle_{$lang}"] = SystemSetting::get("landing.slider.{$slide}.subtitle.{$lang}", '');
                $settings["landing_slider_{$slide}_button_1_{$lang}"] = SystemSetting::get("landing.slider.{$slide}.button_1.{$lang}", '');
                $settings["landing_slider_{$slide}_button_2_{$lang}"] = SystemSetting::get("landing.slider.{$slide}.button_2.{$lang}", '');
            }
            
            // Возможности (Features)
            $settings["landing_features_title_{$lang}"] = SystemSetting::get("landing.features.title.{$lang}", '');
            $settings["landing_features_subtitle_{$lang}"] = SystemSetting::get("landing.features.subtitle.{$lang}", '');
            
            // 9 возможностей
            for ($i = 1; $i <= 9; $i++) {
                $settings["landing_feature_{$i}_title_{$lang}"] = SystemSetting::get("landing.feature.{$i}.title.{$lang}", '');
                $settings["landing_feature_{$i}_description_{$lang}"] = SystemSetting::get("landing.feature.{$i}.description.{$lang}", '');
            }
            
            // Для тренера
            $settings["landing_trainers_title_{$lang}"] = SystemSetting::get("landing.trainers.title.{$lang}", '');
            $settings["landing_trainers_subtitle_{$lang}"] = SystemSetting::get("landing.trainers.subtitle.{$lang}", '');
            // 5 пунктов для тренера
            for ($i = 1; $i <= 5; $i++) {
                $settings["landing_trainer_item_{$i}_{$lang}"] = SystemSetting::get("landing.trainer.item.{$i}.{$lang}", '');
            }
            
            // Для спортсмена
            $settings["landing_athletes_title_{$lang}"] = SystemSetting::get("landing.athletes.title.{$lang}", '');
            $settings["landing_athletes_subtitle_{$lang}"] = SystemSetting::get("landing.athletes.subtitle.{$lang}", '');
            // 5 пунктов для спортсмена
            for ($i = 1; $i <= 5; $i++) {
                $settings["landing_athlete_item_{$i}_{$lang}"] = SystemSetting::get("landing.athlete.item.{$i}.{$lang}", '');
            }
        }
        
        // Медиа
        $settings['landing_hero_image'] = SystemSetting::get('landing.hero_image', '');
        $settings['landing_features_image'] = SystemSetting::get('landing.features_image', '');
        
        // Изображения для тренера (до 5 шт.)
        for ($i = 1; $i <= 5; $i++) {
            $settings["landing_trainers_image_{$i}"] = SystemSetting::get("landing.trainers.image.{$i}", '');
        }
        
        // Изображения для спортсмена (до 5 шт.)
        for ($i = 1; $i <= 5; $i++) {
            $settings["landing_athletes_image_{$i}"] = SystemSetting::get("landing.athletes.image.{$i}", '');
        }
        
        // Изображения для слайдов
        $settings['landing_slider_1_image'] = SystemSetting::get('landing.slider.1.image', '');
        $settings['landing_slider_2_image'] = SystemSetting::get('landing.slider.2.image', '');
        $settings['landing_slider_3_image'] = SystemSetting::get('landing.slider.3.image', '');

        return view('admin.site.index', compact('settings', 'languages'));
    }

    /**
     * Сохранение настроек сайта.
     */
    public function update(Request $request)
    {
        $languages = ['ru', 'ua'];
        
        $rules = [
            'site_name'        => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
            'logo'             => ['nullable', 'image', 'max:2048'],
            'logo_light'       => ['nullable', 'image', 'max:2048'],
            'logo_dark'        => ['nullable', 'image', 'max:2048'],
            'favicon'          => ['nullable', 'file', 'mimetypes:image/png,image/x-png,image/apng,image/jpeg,image/jpg,image/pjpeg,image/x-icon,image/vnd.microsoft.icon', 'max:1024'],
            'landing_hero_image' => ['nullable', 'image', 'max:2048'],
            'landing_features_image' => ['nullable', 'image', 'max:2048'],
            'landing_slider_1_image' => ['nullable', 'image', 'max:2048'],
            'landing_slider_2_image' => ['nullable', 'image', 'max:2048'],
            'landing_slider_3_image' => ['nullable', 'image', 'max:2048'],
        ];
        
        // Добавляем правила валидации для изображений тренера и спортсмена
        for ($i = 1; $i <= 5; $i++) {
            $rules["landing_trainers_image_{$i}"] = ['nullable', 'image', 'max:2048'];
            $rules["landing_athletes_image_{$i}"] = ['nullable', 'image', 'max:2048'];
        }
        
        // Добавляем правила валидации для мультиязычных полей
        foreach ($languages as $lang) {
            // Слайдер - 3 слайда
            for ($slide = 1; $slide <= 3; $slide++) {
                $rules["landing_slider_{$slide}_title_{$lang}"] = ['nullable', 'string', 'max:255'];
                $rules["landing_slider_{$slide}_subtitle_{$lang}"] = ['nullable', 'string', 'max:500'];
                $rules["landing_slider_{$slide}_button_1_{$lang}"] = ['nullable', 'string', 'max:100'];
                $rules["landing_slider_{$slide}_button_2_{$lang}"] = ['nullable', 'string', 'max:100'];
            }
            
            // Возможности
            $rules["landing_features_title_{$lang}"] = ['nullable', 'string', 'max:255'];
            $rules["landing_features_subtitle_{$lang}"] = ['nullable', 'string', 'max:500'];
            
            // 9 возможностей
            for ($i = 1; $i <= 9; $i++) {
                $rules["landing_feature_{$i}_title_{$lang}"] = ['nullable', 'string', 'max:255'];
                $rules["landing_feature_{$i}_description_{$lang}"] = ['nullable', 'string', 'max:500'];
            }
            
            // Для тренера
            $rules["landing_trainers_title_{$lang}"] = ['nullable', 'string', 'max:255'];
            $rules["landing_trainers_subtitle_{$lang}"] = ['nullable', 'string', 'max:500'];
            for ($i = 1; $i <= 5; $i++) {
                $rules["landing_trainer_item_{$i}_{$lang}"] = ['nullable', 'string', 'max:500'];
            }
            
            // Для спортсмена
            $rules["landing_athletes_title_{$lang}"] = ['nullable', 'string', 'max:255'];
            $rules["landing_athletes_subtitle_{$lang}"] = ['nullable', 'string', 'max:500'];
            for ($i = 1; $i <= 5; $i++) {
                $rules["landing_athlete_item_{$i}_{$lang}"] = ['nullable', 'string', 'max:500'];
            }
        }
        
        $data = $request->validate($rules);

        // Основные настройки
        SystemSetting::set('site.name', $data['site_name'], 'string', 'Название сайта', true);
        SystemSetting::set('site.description', $data['site_description'] ?? '', 'string', 'Описание сайта', true);
        SystemSetting::set('site.meta_title', $data['meta_title'] ?? '', 'string', 'SEO meta title', true);
        SystemSetting::set('site.meta_description', $data['meta_description'] ?? '', 'string', 'SEO meta description', true);
        SystemSetting::set('site.meta_keywords', $data['meta_keywords'] ?? '', 'string', 'SEO meta keywords', true);

        // Сохраняем мультиязычные настройки лендинга
        foreach ($languages as $lang) {
            // Слайдер - 3 слайда
            for ($slide = 1; $slide <= 3; $slide++) {
                SystemSetting::set("landing.slider.{$slide}.title.{$lang}", $data["landing_slider_{$slide}_title_{$lang}"] ?? '', 'string', "Заголовок слайда {$slide} ({$lang})", true);
                SystemSetting::set("landing.slider.{$slide}.subtitle.{$lang}", $data["landing_slider_{$slide}_subtitle_{$lang}"] ?? '', 'string', "Подзаголовок слайда {$slide} ({$lang})", true);
                SystemSetting::set("landing.slider.{$slide}.button_1.{$lang}", $data["landing_slider_{$slide}_button_1_{$lang}"] ?? '', 'string', "Текст кнопки 1 слайда {$slide} ({$lang})", true);
                SystemSetting::set("landing.slider.{$slide}.button_2.{$lang}", $data["landing_slider_{$slide}_button_2_{$lang}"] ?? '', 'string', "Текст кнопки 2 слайда {$slide} ({$lang})", true);
            }
            
            // Возможности
            SystemSetting::set("landing.features.title.{$lang}", $data["landing_features_title_{$lang}"] ?? '', 'string', "Заголовок секции возможностей ({$lang})", true);
            SystemSetting::set("landing.features.subtitle.{$lang}", $data["landing_features_subtitle_{$lang}"] ?? '', 'string', "Подзаголовок секции возможностей ({$lang})", true);
            
            // 9 возможностей
            for ($i = 1; $i <= 9; $i++) {
                SystemSetting::set("landing.feature.{$i}.title.{$lang}", $data["landing_feature_{$i}_title_{$lang}"] ?? '', 'string', "Заголовок возможности {$i} ({$lang})", true);
                SystemSetting::set("landing.feature.{$i}.description.{$lang}", $data["landing_feature_{$i}_description_{$lang}"] ?? '', 'string', "Описание возможности {$i} ({$lang})", true);
            }
            
            // Для тренера
            SystemSetting::set("landing.trainers.title.{$lang}", $data["landing_trainers_title_{$lang}"] ?? '', 'string', "Заголовок секции для тренера ({$lang})", true);
            SystemSetting::set("landing.trainers.subtitle.{$lang}", $data["landing_trainers_subtitle_{$lang}"] ?? '', 'string', "Подзаголовок секции для тренера ({$lang})", true);
            for ($i = 1; $i <= 5; $i++) {
                SystemSetting::set("landing.trainer.item.{$i}.{$lang}", $data["landing_trainer_item_{$i}_{$lang}"] ?? '', 'string', "Пункт {$i} для тренера ({$lang})", true);
            }
            
            // Для спортсмена
            SystemSetting::set("landing.athletes.title.{$lang}", $data["landing_athletes_title_{$lang}"] ?? '', 'string', "Заголовок секции для спортсмена ({$lang})", true);
            SystemSetting::set("landing.athletes.subtitle.{$lang}", $data["landing_athletes_subtitle_{$lang}"] ?? '', 'string', "Подзаголовок секции для спортсмена ({$lang})", true);
            for ($i = 1; $i <= 5; $i++) {
                SystemSetting::set("landing.athlete.item.{$i}.{$lang}", $data["landing_athlete_item_{$i}_{$lang}"] ?? '', 'string', "Пункт {$i} для спортсмена ({$lang})", true);
            }
        }

        // Медиа файлы
        if ($request->hasFile('logo')) {
            $this->storeImageSetting($request->file('logo'), 'site.logo', 'Логотип сайта');
        }

        if ($request->hasFile('logo_light')) {
            $this->storeImageSetting($request->file('logo_light'), 'site.logo_light', 'Логотип CRM (светлая тема)');
        }

        if ($request->hasFile('logo_dark')) {
            $this->storeImageSetting($request->file('logo_dark'), 'site.logo_dark', 'Логотип CRM (тёмная тема)');
        }

        if ($request->hasFile('favicon')) {
            $this->storeFaviconSetting($request->file('favicon'), 'site.favicon', 'Favicon сайта');
        }
        
        if ($request->hasFile('landing_hero_image')) {
            $this->storeImageSetting($request->file('landing_hero_image'), 'landing.hero_image', 'Изображение hero секции');
        }
        
        if ($request->hasFile('landing_features_image')) {
            $this->storeImageSetting($request->file('landing_features_image'), 'landing.features_image', 'Изображение секции возможностей');
        }
        
        // Обработка изображений для тренера (до 5 шт.)
        for ($i = 1; $i <= 5; $i++) {
            $imageKey = "landing_trainers_image_{$i}";
            $existingImageKey = "landing_trainers_existing_image_{$i}";
            
            if ($request->hasFile($imageKey)) {
                // Загружено новое изображение - сохраняем его
                $oldImage = SystemSetting::get("landing.trainers.image.{$i}", '');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
                $this->storeImageSetting($request->file($imageKey), "landing.trainers.image.{$i}", "Изображение тренера {$i}");
            } else {
                // Новое изображение не загружено
                $existingImage = $request->input($existingImageKey, '');
                if (!empty($existingImage)) {
                    // Сохраняем существующее изображение
                    SystemSetting::set("landing.trainers.image.{$i}", $existingImage, 'string', "Изображение тренера {$i}", true);
                } else {
                    // Удаляем изображение, если поле существующего пустое
                    $oldImage = SystemSetting::get("landing.trainers.image.{$i}", '');
                    if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                    SystemSetting::set("landing.trainers.image.{$i}", '', 'string', "Изображение тренера {$i}", true);
                }
            }
        }
        
        // Обработка изображений для спортсмена (до 5 шт.)
        for ($i = 1; $i <= 5; $i++) {
            $imageKey = "landing_athletes_image_{$i}";
            $existingImageKey = "landing_athletes_existing_image_{$i}";
            
            if ($request->hasFile($imageKey)) {
                // Загружено новое изображение - сохраняем его
                $oldImage = SystemSetting::get("landing.athletes.image.{$i}", '');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
                $this->storeImageSetting($request->file($imageKey), "landing.athletes.image.{$i}", "Изображение спортсмена {$i}");
            } else {
                // Новое изображение не загружено
                $existingImage = $request->input($existingImageKey, '');
                if (!empty($existingImage)) {
                    // Сохраняем существующее изображение
                    SystemSetting::set("landing.athletes.image.{$i}", $existingImage, 'string', "Изображение спортсмена {$i}", true);
                } else {
                    // Удаляем изображение, если поле существующего пустое
                    $oldImage = SystemSetting::get("landing.athletes.image.{$i}", '');
                    if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                    SystemSetting::set("landing.athletes.image.{$i}", '', 'string', "Изображение спортсмена {$i}", true);
                }
            }
        }
        
        if ($request->hasFile('landing_slider_1_image')) {
            $this->storeImageSetting($request->file('landing_slider_1_image'), 'landing.slider.1.image', 'Изображение слайда 1 (календарь)');
        }
        
        if ($request->hasFile('landing_slider_2_image')) {
            $this->storeImageSetting($request->file('landing_slider_2_image'), 'landing.slider.2.image', 'Изображение слайда 2 (тренировка)');
        }
        
        if ($request->hasFile('landing_slider_3_image')) {
            $this->storeImageSetting($request->file('landing_slider_3_image'), 'landing.slider.3.image', 'Изображение слайда 3 (питание)');
        }

        return redirect()
            ->route('admin.site.index')
            ->with('success', 'Настройки сайта обновлены');
    }

    private function storeImageSetting($file, string $key, string $description): void
    {
        $previous = SystemSetting::get($key);
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        $path = $file->store('site', 'public');
        SystemSetting::set($key, $path, 'string', $description, true);
    }

    private function storeFaviconSetting($file, string $key, string $description): void
    {
        $previous = SystemSetting::get($key);
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        // Всегда сохраняем фавикон как favicon.ico
        $path = 'site/favicon.ico';
        
        // Если загружен не .ico файл, копируем содержимое
        $contents = file_get_contents($file->getRealPath());
        Storage::disk('public')->put($path, $contents);
        
        SystemSetting::set($key, $path, 'string', $description, true);
    }
}


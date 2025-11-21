<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Landing\BaseController;
use App\Models\SystemSetting;
use App\Models\Language;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    /**
     * Главная страница лендинга
     */
    public function index(Request $request)
    {
        // Загружаем активные языки
        $languages = Language::getActive();
        
        // Определяем язык: из параметра URL, сессии или локали приложения
        $lang = 'ru'; // по умолчанию
        if ($request->has('lang')) {
            $requestedLang = $request->get('lang');
            if ($languages->pluck('code')->contains($requestedLang)) {
                $lang = $requestedLang;
                session(['locale' => $lang]);
            }
        } elseif (session()->has('locale')) {
            $sessionLang = session('locale');
            if ($languages->pluck('code')->contains($sessionLang)) {
                $lang = $sessionLang;
            }
        } elseif (app()->getLocale() === 'uk' || app()->getLocale() === 'ua') {
            $lang = 'ua';
        } else {
            $lang = app()->getLocale() === 'ru' ? 'ru' : 'ru';
        }
        
        // Устанавливаем локаль для текущего запроса
        app()->setLocale($lang);
        
        // Загружаем данные для лендинга
        $data = [
            // Слайдер - загружаем все 3 слайда
            'sliders' => [],
            
            // Возможности
            'features_title' => SystemSetting::get("landing.features.title.{$lang}", 'Возможности системы'),
            'features_subtitle' => SystemSetting::get("landing.features.subtitle.{$lang}", 'Все необходимое для управления тренировочным процессом'),
            'features' => [],
            
            // Для тренера
            'trainers_title' => SystemSetting::get("landing.trainers.title.{$lang}", 'Для тренеров'),
            'trainers_subtitle' => SystemSetting::get("landing.trainers.subtitle.{$lang}", 'Управляйте всеми аспектами вашего тренировочного бизнеса в одном месте'),
            'trainer_items' => [],
            
            // Для спортсмена
            'athletes_title' => SystemSetting::get("landing.athletes.title.{$lang}", 'Для спортсменов'),
            'athletes_subtitle' => SystemSetting::get("landing.athletes.subtitle.{$lang}", 'Следите за своими тренировками, прогрессом и планами питания'),
            'athlete_items' => [],
        ];
        
        // Загружаем данные для всех слайдов (3 слайда)
        for ($i = 1; $i <= 3; $i++) {
            $sliderImage = SystemSetting::get("landing.slider.{$i}.image", '');
            $heroImage = SystemSetting::get('landing.hero_image', '');
            
            // Если первый слайд и нет изображения для слайда, используем hero_image
            if ($i === 1 && empty($sliderImage) && !empty($heroImage)) {
                $sliderImage = $heroImage;
            }
            
            $data['sliders'][] = [
                'title' => SystemSetting::get("landing.slider.{$i}.title.{$lang}", ''),
                'subtitle' => SystemSetting::get("landing.slider.{$i}.subtitle.{$lang}", ''),
                'button_1' => SystemSetting::get("landing.slider.{$i}.button_1.{$lang}", ''),
                'button_2' => SystemSetting::get("landing.slider.{$i}.button_2.{$lang}", ''),
                'image' => $sliderImage,
            ];
        }
        
        // Для обратной совместимости оставляем старые переменные (берем первый слайд)
        if (!empty($data['sliders'][0])) {
            $data['hero_title'] = $data['sliders'][0]['title'] ?: SystemSetting::get("landing.slider.1.title.{$lang}", 'Профессиональная CRM для фитнес-тренеров');
            $data['hero_subtitle'] = $data['sliders'][0]['subtitle'] ?: SystemSetting::get("landing.slider.1.subtitle.{$lang}", 'Управляйте спортсменами, создавайте тренировки, отслеживайте прогресс и многое другое в одной удобной системе.');
            $data['hero_button_1'] = $data['sliders'][0]['button_1'] ?: SystemSetting::get("landing.slider.1.button_1.{$lang}", 'Попробовать бесплатно');
            $data['hero_button_2'] = $data['sliders'][0]['button_2'] ?: SystemSetting::get("landing.slider.1.button_2.{$lang}", 'Узнать больше');
        }
        
        // Загружаем 9 возможностей
        for ($i = 1; $i <= 9; $i++) {
            $data['features'][] = [
                'title' => SystemSetting::get("landing.feature.{$i}.title.{$lang}", ''),
                'description' => SystemSetting::get("landing.feature.{$i}.description.{$lang}", ''),
            ];
        }
        
        // Загружаем 5 пунктов для тренера
        for ($i = 1; $i <= 5; $i++) {
            $data['trainer_items'][] = SystemSetting::get("landing.trainer.item.{$i}.{$lang}", '');
        }
        
        // Загружаем 5 пунктов для спортсмена
        for ($i = 1; $i <= 5; $i++) {
            $data['athlete_items'][] = SystemSetting::get("landing.athlete.item.{$i}.{$lang}", '');
        }
        
        // Добавляем языки для переключателя
        $data['languages'] = $languages;
        $data['current_lang'] = $lang;
        
        // Добавляем изображения для лендинга
        $heroImage = SystemSetting::get('landing.hero_image', '');
        if (empty($heroImage) && !empty($data['sliders'][0]['image'])) {
            $heroImage = $data['sliders'][0]['image'];
        }
        $data['landing_hero_image'] = $heroImage;
        
        $data['landing_features_image'] = SystemSetting::get('landing.features_image', '');
        $data['landing_slider_1_image'] = SystemSetting::get('landing.slider.1.image', '');
        $data['landing_slider_2_image'] = SystemSetting::get('landing.slider.2.image', '');
        $data['landing_slider_3_image'] = SystemSetting::get('landing.slider.3.image', '');
        
        return view('landing.home', $data);
    }

    /**
     * Страница с ценами
     */
    public function pricing()
    {
        return view('landing.pricing');
    }

    /**
     * Страница с функциями
     */
    public function features()
    {
        return view('landing.features');
    }

    /**
     * О нас
     */
    public function about()
    {
        return view('landing.about');
    }

    /**
     * Контакты
     */
    public function contact()
    {
        return view('landing.contact');
    }
}

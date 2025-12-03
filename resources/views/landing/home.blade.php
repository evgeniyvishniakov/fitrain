<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteName = \App\Models\SystemSetting::get('site.name', 'Fitrain');
        $siteLogoDefault = \App\Models\SystemSetting::get('site.logo', '');
        $siteLogoLight = \App\Models\SystemSetting::get('site.logo_light');
        $siteLogoDark = \App\Models\SystemSetting::get('site.logo_dark');
        $landingHeroImage = $landing_hero_image ?? \App\Models\SystemSetting::get('landing.hero_image');
        $landingFeaturesImage = $landing_features_image ?? \App\Models\SystemSetting::get('landing.features_image');
        $landingTestimonialImage = \App\Models\SystemSetting::get('landing.testimonial_image');
        $siteFavicon = \App\Models\SystemSetting::get('site.favicon');
        
        $faviconUrl = null;
        if(!empty($siteFavicon)) {
            try {
                $faviconExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($siteFavicon);
                if ($faviconExists) {
                    $faviconVersion = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($siteFavicon);
                    $faviconUrl = url('storage/' . $siteFavicon) . '?v=' . $faviconVersion;
                }
            } catch (\Exception $e) {}
        }
        if (!$faviconUrl) {
            $faviconUrl = asset('favicon.ico') . '?v=' . time();
        }
    @endphp
    
    @php
        $currentLang = app()->getLocale();
        // Если язык не поддерживается, используем русский
        if (!in_array($currentLang, ['ru', 'ua', 'en'])) {
            $currentLang = 'ru';
        }
        
        // Получаем SEO настройки для текущего языка
        $metaTitle = \App\Models\SystemSetting::get("site.meta_title.{$currentLang}", '');
        $metaDescription = \App\Models\SystemSetting::get("site.meta_description.{$currentLang}", '');
        $metaKeywords = \App\Models\SystemSetting::get("site.meta_keywords.{$currentLang}", '');
        
        // Если нет настроек для текущего языка, используем русский как fallback
        if (empty($metaTitle)) {
            $metaTitle = \App\Models\SystemSetting::get("site.meta_title.ru", $siteName . ' - CRM система для фитнес-тренеров и спортсменов');
        }
        if (empty($metaDescription)) {
            $metaDescription = \App\Models\SystemSetting::get("site.meta_description.ru", 'Профессиональная CRM система для управления тренировками, спортсменами и прогрессом. Удобный календарь, отслеживание прогресса, планы питания и многое другое.');
        }
        if (empty($metaKeywords)) {
            $metaKeywords = \App\Models\SystemSetting::get("site.meta_keywords.ru", '');
        }
    @endphp
    
    <title>{{ $metaTitle }}</title>
    @if(!empty($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if(!empty($metaKeywords))
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        * {
            box-sizing: border-box;
        }
        
        @media (max-width: 767px) {
            body {
                overflow-x: hidden;
            }
            
            html {
                overflow-x: hidden;
            }
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        
        .btn-secondary {
            background: white;
            border: 2px solid #10b981;
            color: #10b981;
        }
        
        .btn-secondary:hover {
            background: #10b981;
            color: white;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero-bg {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        .hero-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: visible;
        }
        
        .hero-image-container img {
            max-width: 110%;
            width: 110%;
        }
        
        @media (max-width: 767px) {
            .hero-text-container {
                height: 350px !important;
                min-height: 350px !important;
                position: relative !important;
                width: 100%;
                overflow: hidden;
            }
            
            .hero-text-container > div {
                position: relative !important;
                width: 100%;
            }
            
            .hero-image-container {
                height: 300px !important;
                min-height: 300px !important;
                position: relative !important;
                margin-top: 2rem;
                width: 100%;
                overflow: hidden;
            }
            
            .hero-image-container > div {
                position: relative !important;
                height: 100% !important;
                width: 100%;
            }
            
            .hero-image-container img {
                max-width: 100% !important;
                width: 100% !important;
                height: auto;
                max-height: 100%;
                object-fit: contain;
                display: block;
                position: relative;
            }
            
            section.hero-bg {
                padding-bottom: 2rem !important;
            }
            
            section.hero-bg .grid {
                display: flex !important;
                flex-direction: column !important;
            }
            
            /* Слайдер для тренеров и спортсменов на мобильных */
            #for-trainers .relative.rounded-2xl.shadow-2xl,
            #for-athletes .relative.rounded-2xl.shadow-2xl {
                min-height: 300px !important;
                height: 300px !important;
                position: relative !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            #for-trainers .relative.rounded-2xl.shadow-2xl > div[x-show],
            #for-athletes .relative.rounded-2xl.shadow-2xl > div[x-show] {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100% !important;
                height: 100% !important;
                display: flex !important;
                visibility: visible !important;
            }
            
            #for-trainers .relative.rounded-2xl.shadow-2xl > div[x-show] img,
            #for-athletes .relative.rounded-2xl.shadow-2xl > div[x-show] img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                display: block !important;
                visibility: visible !important;
            }
            
            /* Убеждаемся, что контейнер слайдера для тренеров виден на мобильных */
            #for-trainers > div > div > div.relative {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin-top: 2rem !important;
            }
            
            /* Скрываем статическое fallback изображение на мобильных */
            #for-trainers .trainer-static-image {
                display: none !important;
                visibility: hidden !important;
            }
            
            /* Убеждаемся, что слайдер для тренеров виден на мобильных */
            #for-trainers .grid > div.relative {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin-top: 2rem !important;
                min-height: 300px !important;
            }
            
            #for-trainers .relative.rounded-2xl.shadow-2xl {
                margin-top: 0 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Убеждаемся, что изображения слайдера для тренеров видны */
            #for-trainers .relative.rounded-2xl.shadow-2xl img {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Убеждаемся, что все элементы слайдера тренеров видны на мобильных */
            #for-trainers .relative.rounded-2xl.shadow-2xl template {
                display: block !important;
            }
            
            #for-trainers .relative.rounded-2xl.shadow-2xl template > div {
                display: flex !important;
                visibility: visible !important;
            }
            
            /* Дополнительная проверка видимости слайдера тренеров на мобильных */
            #for-trainers .grid > div.relative.order-2 {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin-top: 2rem !important;
                position: relative !important;
            }
            
            /* Десктоп - возвращаем исходные стили */
            @media (min-width: 768px) {
                #for-trainers .relative.rounded-2xl.shadow-2xl > div[x-show] img,
                #for-athletes .relative.rounded-2xl.shadow-2xl > div[x-show] img {
                    height: auto !important;
                    object-fit: contain !important;
                }
            }
        }
        
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div x-data="{ mobileMenuOpen: false }">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm fixed w-full z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        @if($siteLogoLight || $siteLogoDefault)
                            <img src="{{ $siteLogoLight ? url('storage/' . $siteLogoLight) : url('storage/' . $siteLogoDefault) }}" alt="{{ $siteName }}" class="h-10">
                        @else
                            <span class="text-2xl font-bold gradient-primary bg-clip-text text-transparent">{{ $siteName }}</span>
                        @endif
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="#features" class="text-gray-700 hover:text-green-600 transition">Возможности</a>
                        <a href="#how-it-works" class="text-gray-700 hover:text-green-600 transition">Как это работает</a>
                        <a href="#for-trainers" class="text-gray-700 hover:text-green-600 transition">Для тренеров</a>
                        <a href="#for-athletes" class="text-gray-700 hover:text-green-600 transition">Для спортсменов</a>
                        <a href="#pricing" class="text-gray-700 hover:text-green-600 transition">Тарифы</a>
                        <a href="{{ route('crm.login') }}" class="text-gray-700 hover:text-green-600 transition">Войти</a>
                        <a href="{{ route('crm.trainer.register') }}" class="btn-primary text-white px-6 py-2 rounded-lg font-medium">Начать</a>
                        <!-- Переключатель языков -->
                        <select id="language_select" 
                                onchange="window.location.href='?lang='+this.value"
                                class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer"
                                title="{{ $languages->firstWhere('code', $current_lang)->native_name ?? $languages->firstWhere('code', $current_lang)->name ?? '' }}">
                            @foreach($languages as $language)
                                <option value="{{ $language->code }}" {{ $current_lang === $language->code ? 'selected' : '' }}>
                                    {{ $language->flag ?? strtoupper($language->code) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white border-t">
                <div class="px-4 py-4 space-y-4">
                    <a href="#features" @click="mobileMenuOpen = false" class="block text-gray-700 hover:text-green-600">Возможности</a>
                    <a href="#how-it-works" @click="mobileMenuOpen = false" class="block text-gray-700 hover:text-green-600">Как это работает</a>
                    <a href="#for-trainers" @click="mobileMenuOpen = false" class="block text-gray-700 hover:text-green-600">Для тренеров</a>
                    <a href="#for-athletes" @click="mobileMenuOpen = false" class="block text-gray-700 hover:text-green-600">Для спортсменов</a>
                    <a href="#pricing" @click="mobileMenuOpen = false" class="block text-gray-700 hover:text-green-600">Тарифы</a>
                    <a href="{{ route('crm.login') }}" class="block text-gray-700 hover:text-green-600">Войти</a>
                    <a href="{{ route('crm.trainer.register') }}" class="btn-primary text-white px-6 py-2 rounded-lg font-medium inline-block text-center">Начать</a>
                    <!-- Переключатель языков -->
                    <select id="language_select_mobile" 
                            onchange="window.location.href='?lang='+this.value"
                            class="w-full px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors cursor-pointer"
                            title="{{ $languages->firstWhere('code', $current_lang)->native_name ?? $languages->firstWhere('code', $current_lang)->name ?? '' }}">
                        @foreach($languages as $language)
                            <option value="{{ $language->code }}" {{ $current_lang === $language->code ? 'selected' : '' }}>
                                {{ $language->flag ?? strtoupper($language->code) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section -->
        <section class="hero-bg pt-32 pb-20 px-4 sm:px-6 lg:px-8" 
                 x-data="sliderData()"
                 @mouseenter.stop="stopAutoplay()"
                 @mouseleave.stop="startAutoplay()">
            <div class="max-w-7xl mx-auto relative">
                <div class="grid md:grid-cols-2 items-center">
                    <div class="fade-in relative hero-text-container" style="min-height: 400px;">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div x-show="currentSlide === index" 
                                 x-transition:enter="transition ease-in-out duration-700"
                                 x-transition:enter-start="opacity-0 transform translate-x-8"
                                 x-transition:enter-end="opacity-100 transform translate-x-0"
                                 x-transition:leave="transition ease-in-out duration-700"
                                 x-transition:leave-start="opacity-100 transform translate-x-0"
                                 x-transition:leave-end="opacity-0 transform -translate-x-8"
                                 class="absolute inset-0">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-6" x-text="slide.title || ''"></h1>
                                <p class="text-xl text-gray-600 mb-8" x-text="slide.subtitle || ''"></p>
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <a x-show="slide.button_1" 
                                       x-cloak
                                       href="{{ route('crm.trainer.register') }}" 
                                       class="btn-primary text-white px-8 py-4 rounded-lg font-semibold text-center">
                                        <span x-text="slide.button_1"></span>
                                    </a>
                                    <a x-show="slide.button_2" 
                                       x-cloak
                                       href="#features" 
                                       class="btn-secondary px-8 py-4 rounded-lg font-semibold text-center">
                                        <span x-text="slide.button_2"></span>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="fade-in hero-image-container relative" style="min-height: 400px;">
                        <template x-for="(slide, index) in slides" :key="index">
                            <div x-show="currentSlide === index" 
                                 x-transition:enter="transition ease-in-out duration-700"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in-out duration-700"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute inset-0 flex items-center justify-center">
                                <img x-show="slide.image" 
                                     x-cloak
                                     :src="slide.image ? '{{ asset('storage/') }}/' + slide.image : ''" 
                                     alt="Fitrain CRM" 
                                     class="rounded-2xl shadow-2xl w-full h-auto object-contain max-h-full">
                                <div x-show="!slide.image" 
                                     x-cloak
                                     class="gradient-primary rounded-2xl shadow-2xl p-12 text-center w-full flex flex-col items-center justify-center" style="min-height: 300px;">
                                    <svg class="w-full h-64 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <p class="text-white text-lg mt-4">Загрузите изображение через настройки системы</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div x-show="slides.length > 1" x-cloak class="absolute -bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="goToSlide(index)" 
                                :class="currentSlide === index ? 'bg-green-600' : 'bg-gray-300'"
                                class="w-3 h-3 rounded-full transition-all duration-300 hover:bg-green-500"></button>
                    </template>
                </div>
            </div>
        </section>
        
        <script>
        function sliderData() {
            const slides = @json($sliders ?? []);
            const filteredSlides = slides.filter(slide => {
                return (slide.title && slide.title.trim()) || 
                       (slide.subtitle && slide.subtitle.trim()) || 
                       (slide.image && slide.image.trim());
            });
            
            let finalSlides = filteredSlides.length > 0 ? filteredSlides : [{
                title: '{{ $hero_title ?? 'Профессиональная CRM для фитнес-тренеров' }}',
                subtitle: '{{ $hero_subtitle ?? '' }}',
                button_1: '{{ $hero_button_1 ?? 'Попробовать бесплатно' }}',
                button_2: '{{ $hero_button_2 ?? 'Узнать больше' }}',
                image: '{{ $landing_hero_image ?? '' }}'
            }];
            
            return {
                currentSlide: 0,
                slides: finalSlides,
                autoplayInterval: null,
                autoplayDelay: 5000,
                init() {
                    if (this.slides.length > 1) {
                        this.startAutoplay();
                    }
                },
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.slides.length > 1) {
                        this.autoplayInterval = setInterval(() => {
                            this.nextSlide();
                        }, this.autoplayDelay);
                    }
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                        this.autoplayInterval = null;
                    }
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                },
                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                },
                goToSlide(index) {
                    if (index === this.currentSlide) return;
                    this.stopAutoplay();
                    this.currentSlide = index;
                    this.startAutoplay();
                }
            };
        }
        </script>
        
        <!-- How It Works Section -->
        <section id="how-it-works" class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-green-50 via-white to-blue-50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $how_it_works_title }}</h2>
                    <p class="text-xl text-gray-600">{{ $how_it_works_subtitle }}</p>
                </div>
                
                <div class="grid lg:grid-cols-4 gap-8 items-center">
                    <!-- Левая колонка: Преимущества для тренера -->
                    <div class="lg:col-span-1 space-y-4 order-1 lg:order-1">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center lg:text-left">
                            <span class="inline-flex items-center">
                                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Для тренера
                            </span>
                        </h3>
                        <ul class="space-y-3">
                            @foreach($trainer_benefits as $benefit)
                                @if(!empty($benefit))
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $benefit }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Центральная часть: Два мобильных экрана -->
                    <div class="lg:col-span-2 grid md:grid-cols-2 gap-6 order-2 lg:order-2">
                        <!-- Мобильный экран тренера -->
                        <div class="flex flex-col items-center">
                            <div class="relative w-64 max-w-full">
                                <!-- Рамка телефона -->
                                <div class="bg-gray-900 rounded-[2.5rem] p-2 shadow-2xl">
                                    <div class="bg-white rounded-[2rem] overflow-hidden">
                                        <!-- Заголовок экрана -->
                                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 text-white text-center">
                                            <p class="text-sm font-semibold">Тренер</p>
                                        </div>
                                        <!-- Контент экрана -->
                                        <div class="h-[420px] overflow-y-auto bg-gray-50">
                                            @php
                                                $trainerImageExists = false;
                                                if(!empty($landing_how_it_works_trainer_image ?? '')) {
                                                    try {
                                                        $trainerImageExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($landing_how_it_works_trainer_image);
                                                    } catch (\Exception $e) {
                                                        $trainerImageExists = false;
                                                    }
                                                }
                                            @endphp
                                            @if($trainerImageExists)
                                                <img src="{{ asset('storage/' . $landing_how_it_works_trainer_image) }}" 
                                                     alt="Экран тренера" 
                                                     class="w-full h-auto object-cover">
                                            @else
                                                <div class="p-4 space-y-3">
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Кнопка Home (внизу телефона) -->
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-gray-800 rounded-full"></div>
                            </div>
                        </div>
                        
                        <!-- Мобильный экран спортсмена -->
                        <div class="flex flex-col items-center">
                            <div class="relative w-64 max-w-full">
                                <!-- Рамка телефона -->
                                <div class="bg-gray-900 rounded-[2.5rem] p-2 shadow-2xl">
                                    <div class="bg-white rounded-[2rem] overflow-hidden">
                                        <!-- Заголовок экрана -->
                                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3 text-white text-center">
                                            <p class="text-sm font-semibold">Спортсмен</p>
                                        </div>
                                        <!-- Контент экрана -->
                                        <div class="h-[420px] overflow-y-auto bg-gray-50">
                                            @php
                                                $athleteImageExists = false;
                                                if(!empty($landing_how_it_works_athlete_image ?? '')) {
                                                    try {
                                                        $athleteImageExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($landing_how_it_works_athlete_image);
                                                    } catch (\Exception $e) {
                                                        $athleteImageExists = false;
                                                    }
                                                }
                                            @endphp
                                            @if($athleteImageExists)
                                                <img src="{{ asset('storage/' . $landing_how_it_works_athlete_image) }}" 
                                                     alt="Экран спортсмена" 
                                                     class="w-full h-auto object-cover">
                                            @else
                                                <div class="p-4 space-y-3">
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                    <div class="bg-white p-3 rounded-lg shadow">
                                                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                                                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- Кнопка Home (внизу телефона) -->
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-gray-800 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Правая колонка: Преимущества для спортсмена -->
                    <div class="lg:col-span-1 space-y-4 order-3">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 text-center lg:text-right">
                            <span class="inline-flex items-center">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Для спортсмена
                            </span>
                        </h3>
                        <ul class="space-y-3">
                            @foreach($athlete_benefits as $benefit)
                                @if(!empty($benefit))
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $benefit }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Features Section -->
        <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $features_title }}</h2>
                    <p class="text-xl text-gray-600">{{ $features_subtitle }}</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($features as $index => $feature)
                        @if(!empty($feature['title']) || !empty($feature['description']))
                            <div class="bg-white p-8 rounded-xl shadow-lg card-hover">
                                @php
                                    $gradients = ['gradient-green', 'gradient-blue', 'gradient-primary'];
                                    $gradient = $gradients[$index % 3];
                                    
                                    // Разные иконки для разных возможностей
                                    $icons = [
                                        // Иконка календаря
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                                        // Иконка пользователей
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                                        // Иконка графика
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                                        // Иконка книги/упражнений
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                                        // Иконка весов
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>',
                                        // Иконка видео
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>',
                                        // Иконка истории
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        // Иконка галочки/отслеживания
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                                        // Иконка молнии
                                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>'
                                    ];
                                    $iconPath = $icons[$index % count($icons)];
                                @endphp
                                <div class="w-16 h-16 {{ $gradient }} rounded-lg flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $iconPath !!}
                                    </svg>
                                </div>
                                @if(!empty($feature['title']))
                                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                                @endif
                                @if(!empty($feature['description']))
                                    <p class="text-gray-600">{{ $feature['description'] }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
        
        <!-- For Trainers Section -->
        <section id="for-trainers" class="py-20 px-4 sm:px-6 lg:px-8 hero-bg">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-2 md:gap-8 items-center">
                    <div class="relative order-2 md:order-1" 
                         x-data="trainerImageSlider()"
                         @mouseenter.stop="stopAutoplay()"
                         @mouseleave.stop="startAutoplay()">
                        @php
                            $trainerImages = $landing_trainers_images ?? [];
                        @endphp
                        @if(count($trainerImages) > 0)
                            <div class="relative rounded-2xl shadow-2xl overflow-hidden" style="min-height: 400px;">
                                <template x-for="(image, index) in images" :key="index">
                                    <div x-show="currentImage === index" 
                                         x-transition:enter="transition ease-in-out duration-700"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in-out duration-700"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute inset-0"
                                         style="display: flex; align-items: center; justify-content: center; z-index: 1;">
                                        <img :src="'{{ asset('storage/') }}/' + image" 
                                             alt="Для тренеров" 
                                             class="w-full h-full object-cover rounded-2xl"
                                             style="max-width: 100%; max-height: 100%; display: block;">
                                    </div>
                                </template>
                                
                                <!-- Статическое первое изображение как fallback -->
                                <div class="absolute inset-0 trainer-static-image" style="display: block; z-index: 0;" x-show="false">
                                    <img src="{{ asset('storage/' . $trainerImages[0]) }}" 
                                         alt="Для тренеров" 
                                         class="w-full h-full object-cover rounded-2xl"
                                         style="max-width: 100%; max-height: 100%; display: block;">
                                </div>
                                
                                @if(count($trainerImages) > 1)
                                    <!-- Навигация точками -->
                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                                        <template x-for="(image, index) in images" :key="index">
                                            <button @click="goToImage(index)" 
                                                    :class="currentImage === index ? 'bg-green-600 w-8' : 'bg-white/50 w-3'"
                                                    class="h-3 rounded-full transition-all duration-300 hover:bg-green-500"></button>
                                        </template>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="gradient-blue rounded-2xl shadow-2xl p-12 text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <svg class="w-full h-64 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="order-1 md:order-2">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $trainers_title }}</h2>
                        <p class="text-xl text-gray-600 mb-8">
                            {{ $trainers_subtitle }}
                        </p>
                        <ul class="space-y-4">
                            @foreach($trainer_items as $item)
                                @if(!empty($item))
                                    <li class="flex items-start">
                                        <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $item }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="mt-8">
                            <a href="{{ route('crm.trainer.register') }}" class="btn-primary text-white px-8 py-4 rounded-lg font-semibold inline-block">
                                Начать как тренер
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- For Athletes Section -->
        <section id="for-athletes" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-2 items-center">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $athletes_title }}</h2>
                        <p class="text-xl text-gray-600 mb-8">
                            {{ $athletes_subtitle }}
                        </p>
                        <ul class="space-y-4">
                            @foreach($athlete_items as $item)
                                @if(!empty($item))
                                    <li class="flex items-start">
                                        <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="text-gray-700">{{ $item }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="relative" 
                         x-data="athleteImageSlider()"
                         @mouseenter.stop="stopAutoplay()"
                         @mouseleave.stop="startAutoplay()">
                        @php
                            $athleteImages = $landing_athletes_images ?? [];
                        @endphp
                        @if(count($athleteImages) > 0)
                            <div class="relative rounded-2xl shadow-2xl overflow-hidden" style="min-height: 400px;">
                                <template x-for="(image, index) in images" :key="index">
                                    <div x-show="currentImage === index" 
                                         x-transition:enter="transition ease-in-out duration-700"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in-out duration-700"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute inset-0"
                                         style="display: flex; align-items: center; justify-content: center;">
                                        <img :src="'{{ asset('storage/') }}/' + image" 
                                             alt="Для спортсменов" 
                                             class="w-full h-full object-cover rounded-2xl"
                                             style="max-width: 100%; max-height: 100%;">
                                    </div>
                                </template>
                                
                                @if(count($athleteImages) > 1)
                                    <!-- Навигация точками -->
                                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                                        <template x-for="(image, index) in images" :key="index">
                                            <button @click="goToImage(index)" 
                                                    :class="currentImage === index ? 'bg-green-600 w-8' : 'bg-white/50 w-3'"
                                                    class="h-3 rounded-full transition-all duration-300 hover:bg-green-500"></button>
                                        </template>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="gradient-green rounded-2xl shadow-2xl p-12 text-center" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                                <div>
                                    <svg class="w-full h-64 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 gradient-primary">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Готовы начать?</h2>
                <p class="text-xl text-white/90 mb-8">
                    Зарегистрируйтесь и начните использовать {{ $siteName }} уже сегодня
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('crm.trainer.register') }}" class="bg-white text-green-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Регистрация для тренера
                    </a>
                    <a href="{{ route('crm.login') }}" class="bg-white/10 text-white border-2 border-white px-8 py-4 rounded-lg font-semibold hover:bg-white/20 transition">
                        Войти в систему
                    </a>
                </div>
            </div>
        </section>
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        @if($siteLogoLight || $siteLogoDefault)
                            <img src="{{ $siteLogoLight ? url('storage/' . $siteLogoLight) : url('storage/' . $siteLogoDefault) }}" alt="{{ $siteName }}" class="h-10 mb-4">
                        @else
                            <h3 class="text-xl font-bold mb-4">{{ $siteName }}</h3>
                        @endif
                        <p class="text-gray-400">Профессиональная CRM система для фитнес-тренеров и спортсменов</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Навигация</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#features" class="hover:text-white transition">Возможности</a></li>
                            <li><a href="#how-it-works" class="hover:text-white transition">Как это работает</a></li>
                            <li><a href="#for-trainers" class="hover:text-white transition">Для тренеров</a></li>
                            <li><a href="#for-athletes" class="hover:text-white transition">Для спортсменов</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Ресурсы</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="{{ route('crm.login') }}" class="hover:text-white transition">Войти</a></li>
                            <li><a href="{{ route('crm.trainer.register') }}" class="hover:text-white transition">Регистрация</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Контакты</h4>
                        <p class="text-gray-400">По вопросам обращайтесь через систему</p>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ $siteName }}. Все права защищены.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Слайдер для изображений тренера
        function trainerImageSlider() {
            const images = @json($landing_trainers_images ?? []);
            
            return {
                currentImage: 0,
                images: images,
                autoplayInterval: null,
                autoplayDelay: 5000, // 5 секунд
                init() {
                    // Скрываем статическое изображение когда Alpine.js загрузился
                    const staticImg = document.querySelector('.trainer-static-image');
                    if (staticImg) {
                        staticImg.style.display = 'none';
                    }
                    if (this.images.length > 1) {
                        this.startAutoplay();
                    }
                },
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.images.length > 1) {
                        this.autoplayInterval = setInterval(() => {
                            this.nextImage();
                        }, this.autoplayDelay);
                    }
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                        this.autoplayInterval = null;
                    }
                },
                nextImage() {
                    this.currentImage = (this.currentImage + 1) % this.images.length;
                },
                prevImage() {
                    this.currentImage = (this.currentImage - 1 + this.images.length) % this.images.length;
                },
                goToImage(index) {
                    this.stopAutoplay();
                    this.currentImage = index;
                    this.startAutoplay();
                }
            };
        }

        // Слайдер для изображений спортсмена
        function athleteImageSlider() {
            const images = @json($landing_athletes_images ?? []);
            
            return {
                currentImage: 0,
                images: images,
                autoplayInterval: null,
                autoplayDelay: 5000, // 5 секунд
                init() {
                    if (this.images.length > 1) {
                        this.startAutoplay();
                    }
                },
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.images.length > 1) {
                        this.autoplayInterval = setInterval(() => {
                            this.nextImage();
                        }, this.autoplayDelay);
                    }
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                        this.autoplayInterval = null;
                    }
                },
                nextImage() {
                    this.currentImage = (this.currentImage + 1) % this.images.length;
                },
                prevImage() {
                    this.currentImage = (this.currentImage - 1 + this.images.length) % this.images.length;
                },
                goToImage(index) {
                    this.stopAutoplay();
                    this.currentImage = index;
                    this.startAutoplay();
                }
            };
        }
    </script>
</body>
</html>

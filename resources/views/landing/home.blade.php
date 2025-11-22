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
    
    <title>{{ $siteName }} - CRM система для фитнес-тренеров и спортсменов</title>
    <meta name="description" content="Профессиональная CRM система для управления тренировками, спортсменами и прогрессом. Удобный календарь, отслеживание прогресса, планы питания и многое другое.">
    
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="shortcut icon" href="{{ $faviconUrl }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
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
            .hero-image-container img {
                max-width: 100%;
                width: 100%;
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
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="fade-in relative" style="min-height: 400px;">
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
                                 class="absolute inset-0">
                                <img x-show="slide.image" 
                                     x-cloak
                                     :src="slide.image ? '{{ asset('storage/') }}/' + slide.image : ''" 
                                     alt="Fitrain CRM" 
                                     class="rounded-2xl shadow-2xl w-full h-auto object-contain">
                                <div x-show="!slide.image" 
                                     x-cloak
                                     class="gradient-primary rounded-2xl shadow-2xl p-12 text-center h-full flex flex-col items-center justify-center">
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
                                @endphp
                                <div class="w-16 h-16 {{ $gradient }} rounded-lg flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
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
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="relative" 
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
                                         class="absolute inset-0">
                                        <img :src="'{{ asset('storage/') }}/' + image" 
                                             alt="Для тренеров" 
                                             class="w-full h-auto object-cover rounded-2xl">
                                    </div>
                                </template>
                                
                                @if(count($trainerImages) > 1)
                                    <!-- Навигация точками -->
                                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
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
                    <div>
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
                <div class="grid md:grid-cols-2 gap-12 items-center">
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
                                         class="absolute inset-0">
                                        <img :src="'{{ asset('storage/') }}/' + image" 
                                             alt="Для спортсменов" 
                                             class="w-full h-auto object-cover rounded-2xl">
                                    </div>
                                </template>
                                
                                @if(count($athleteImages) > 1)
                                    <!-- Навигация точками -->
                                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
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

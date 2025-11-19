<!DOCTYPE html>
<html lang="ru">
<head>
    <?php
        $siteFavicon = \App\Models\SystemSetting::get('site.favicon');
        $siteName = \App\Models\SystemSetting::get('site.name', 'Fitrain');
        $siteLogoDefault = \App\Models\SystemSetting::get('site.logo', '');
        $siteLogoLight = \App\Models\SystemSetting::get('site.logo_light');
        $siteLogoDark = \App\Models\SystemSetting::get('site.logo_dark');
        $crmLogoLight = $siteLogoLight ?: $siteLogoDefault;
        $crmLogoDark = $siteLogoDark ?: ($siteLogoLight ?: $siteLogoDefault);
    ?>
    <?php if(!empty($siteFavicon)): ?>
        <?php
            $faviconExtension = strtolower(pathinfo($siteFavicon, PATHINFO_EXTENSION));
            // Всегда используем image/x-icon для .ico файлов
            $faviconMime = ($faviconExtension === 'ico' || $faviconExtension === 'x-icon') ? 'image/x-icon' : 'image/png';
            $faviconExists = false;
            $faviconVersion = time();
            
            try {
                $faviconExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($siteFavicon);
                if ($faviconExists) {
                    $faviconVersion = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($siteFavicon);
                }
            } catch (\Exception $e) {
                // Ignore
            }
            $faviconFullUrl = url('storage/' . $siteFavicon) . '?v=' . $faviconVersion . '&nocache=' . time();
        ?>
        <link rel="icon" type="<?php echo e($faviconMime); ?>" href="<?php echo e($faviconFullUrl); ?>" sizes="32x32">
        <link rel="icon" type="<?php echo e($faviconMime); ?>" href="<?php echo e($faviconFullUrl); ?>" sizes="16x16">
        <link rel="shortcut icon" href="<?php echo e($faviconFullUrl); ?>">
        <link rel="apple-touch-icon" href="<?php echo e($faviconFullUrl); ?>">
    <?php endif; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent("title", "Fitrain CRM"); ?></title>
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?php echo e(asset('js/notifications.js')); ?>"></script>
    <style>
        [x-cloak] { display: none !important; }
        
        .crm-logo {
            height: 72px;
            max-width: 260px;
            width: auto;
            display: block;
            object-fit: contain;
        }
        .theme-dark .crm-logo {
            height: 60px;
            max-width: 220px;
        }
        .crm-logo-light { display: block; }
        .crm-logo-dark { display: none; }
        .theme-dark .crm-logo-light { display: none; }
        .theme-dark .crm-logo-dark { display: block; }
        .theme-light .crm-logo-dark { display: none; }
        
        /* Логотипы в мобильном меню */
        .crm-logo-mobile-light { display: block; }
        .crm-logo-mobile-dark { display: none; }
        .theme-dark .crm-logo-mobile-light { display: none; }
        .theme-dark .crm-logo-mobile-dark { display: block; }
        .theme-light .crm-logo-mobile-dark { display: none; }
        
        /* Индикатор загрузки */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        /* Предотвращение мигания темы */
        .theme-dark {
            background-color: #1f2937 !important;
            color: #f9fafb !important;
        }
        
        .theme-light {
            background-color: #ffffff !important;
            color: #111827 !important;
        }
        
        /* Кнопка мобильного меню на светлой теме */
        .theme-light .mobile-menu-btn {
            color: #111827 !important;
        }
        
        .theme-light .mobile-menu-btn:hover {
            color: #374151 !important;
        }
        
        /* Стили для статистических карточек */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .stat-icon-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stat-icon-green {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-icon-purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }
        
        .stat-icon-orange {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }
        
        .stat-icon-red {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-icon-yellow {
            background: linear-gradient(135deg, #fad961 0%, #f76b1c 100%);
        }
        
        .stat-svg {
            width: 1.5rem;
            height: 1.5rem;
            color: white;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .stat-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        /* Темная тема для статистических карточек */
        .theme-dark .stat-card {
            background: #374151;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }
        
        .theme-dark .stat-label {
            color: #9ca3af;
        }
        
        .theme-dark .stat-value {
            color: #f9fafb;
        }
        
        /* Стили для карточек */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .card-header {
            padding: 1.5rem 1.5rem 0 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Темная тема для карточек */
        .theme-dark .card {
            background: #374151;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        }
        
        .theme-dark .card-header {
            border-bottom-color: #4b5563;
        }
        
        .theme-dark .card-title {
            color: #f9fafb;
        }
        
        /* Стили для таблиц */
        .theme-dark table {
            color: #f9fafb;
        }
        
        .theme-dark .bg-gray-50 {
            background-color: #4b5563 !important;
        }
        
        /* Базовый цвет для серого текста */
        .theme-dark .text-gray-500 {
            color: #9ca3af !important;
        }
        
        /* Белые блоки в темной теме должны стать темными */
        .theme-dark .bg-white {
            background-color: #374151 !important;
        }
        
        /* Заголовки на темных блоках должны быть светлыми */
        .theme-dark .bg-white .text-gray-900,
        .theme-dark .bg-white h1,
        .theme-dark .bg-white h2,
        .theme-dark .bg-white h3,
        .theme-dark .bg-white h4,
        .theme-dark .bg-white h5,
        .theme-dark .bg-white h6 {
            color: #f9fafb !important;
        }
        
        /* Текст на темных блоках - более светлый для лучшей видимости */
        .theme-dark .bg-white .text-gray-600 {
            color: #d1d5db !important;
        }
        
        .theme-dark .bg-white .text-gray-500 {
            color: #d1d5db !important;
        }
        
        /* Поля ввода в темной теме */
        .theme-dark input[type="text"],
        .theme-dark input[type="email"],
        .theme-dark input[type="password"],
        .theme-dark input[type="number"],
        .theme-dark input[type="date"],
        .theme-dark input[type="time"],
        .theme-dark input[type="tel"],
        .theme-dark textarea {
            background-color: #374151 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
        
        .theme-dark input[type="text"]:focus,
        .theme-dark input[type="email"]:focus,
        .theme-dark input[type="password"]:focus,
        .theme-dark input[type="number"]:focus,
        .theme-dark input[type="date"]:focus,
        .theme-dark input[type="time"]:focus,
        .theme-dark input[type="tel"]:focus,
        .theme-dark textarea:focus {
            border-color: #6366f1 !important;
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        
        /* Селекты в темной теме */
        .theme-dark select {
            background-color: #374151 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
        
        .theme-dark select:focus {
            border-color: #6366f1 !important;
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        
        .theme-dark select option {
            background-color: #374151 !important;
            color: #f9fafb !important;
        }
        
        /* Кнопки в темной теме */
        .theme-dark .bg-indigo-600,
        .theme-dark .bg-indigo-500 {
            background-color: #4f46e5 !important;
        }
        
        .theme-dark .bg-indigo-600:hover,
        .theme-dark .bg-indigo-500:hover {
            background-color: #4338ca !important;
        }
        
        .theme-dark .text-gray-900 {
            color: #f9fafb !important;
        }
        
        .theme-dark .divide-gray-200 {
            border-color: #4b5563 !important;
        }
        
        .theme-dark .hover\:bg-gray-50:hover {
            background-color: #4b5563 !important;
        }
        
        /* Границы в темной теме */
        .theme-dark .border-gray-100,
        .theme-dark .border-gray-200,
        .theme-dark .border-gray-300 {
            border-color: #4b5563 !important;
        }
        
        /* Фон серых элементов */
        .theme-dark .bg-gray-50 {
            background-color: #374151 !important;
        }
        
        .theme-dark .bg-gray-100 {
            background-color: #4b5563 !important;
        }
        
        /* Текст в карточках спортсменов - должен быть виден в темной теме */
        .theme-dark .text-gray-500 {
            color: #d1d5db !important;
        }
        
        /* Данные профиля спортсмена (возраст, вес, рост) */
        .theme-dark .athlete-profile-data,
        .theme-dark .athlete-profile-data .text-gray-500,
        .theme-dark .athlete-profile-value {
            color: #d1d5db !important;
        }
        
        /* Информация в карточках спортсменов */
        .theme-dark .bg-white .text-gray-500,
        .theme-dark .bg-white span.text-gray-500 {
            color: #d1d5db !important;
        }
        
        /* Кнопки "Назад" и "Редактировать" в темной теме */
        .theme-dark button.bg-gray-50,
        .theme-dark button.bg-white {
            background-color: #4b5563 !important;
            color: #f9fafb !important;
            border-color: #6b7280 !important;
        }
        
        .theme-dark button.bg-gray-50:hover,
        .theme-dark button.bg-white:hover {
            background-color: #6b7280 !important;
        }
        
        .theme-dark button.text-gray-700 {
            color: #f9fafb !important;
        }
        
        /* Кнопки с border-gray-300 в темной теме */
        .theme-dark button.border-gray-300 {
            border-color: #6b7280 !important;
        }
        
        /* Labels (названия полей) в темной теме */
        .theme-dark label.text-gray-700,
        .theme-dark .text-gray-700.label,
        .theme-dark label {
            color: #d1d5db !important;
        }
        
        /* Поля просмотра (bg-gray-50 с text-gray-900) в темной теме */
        .theme-dark .bg-gray-50.text-gray-900,
        .theme-dark .bg-gray-50[data-field] {
            background-color: #374151 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
        
        /* Выбранные элементы языка/валюты в темной теме */
        .theme-dark .bg-blue-50 {
            background-color: #1e3a5f !important;
        }
        
        .theme-dark .bg-blue-50 .text-gray-900 {
            color: #f9fafb !important;
        }
        
        .theme-dark .bg-blue-50 .text-gray-500 {
            color: #d1d5db !important;
        }
        
        .theme-dark .border-blue-500 {
            border-color: #3b82f6 !important;
        }
        
        /* Заголовок месяца в календаре в темной теме */
        .theme-dark .calendar-header h2.text-gray-900,
        .theme-dark .bg-white h2.text-gray-900,
        .theme-dark .bg-white span.text-gray-700[x-text*="currentMonthYear"],
        .theme-dark .bg-white .text-gray-700[x-text*="currentMonthYear"] {
            color: #f9fafb !important;
        }
        
        /* Заголовки "Календар" и "Найближчі тренування" в темной теме */
        .theme-dark .bg-white h3.text-gray-900 {
            color: #f9fafb !important;
        }
        
        /* Поля упражнений в тренировке - более яркие цвета в темной теме */
        .theme-dark .text-indigo-900,
        .theme-dark .text-green-900,
        .theme-dark .text-purple-900,
        .theme-dark .text-orange-900 {
            color: #ffffff !important;
        }
        
        .theme-dark .text-indigo-800 {
            color: #c7d2fe !important;
        }
        
        .theme-dark .text-green-800 {
            color: #6ee7b7 !important;
        }
        
        /* Статус "выполнено" в темной теме - более яркий текст */
        .theme-dark .bg-green-100.text-green-800,
        .theme-dark .bg-green-50.text-green-700 {
            color: #34d399 !important;
            background-color: #064e3b !important;
        }
        
        .theme-dark .bg-green-100 {
            background-color: #064e3b !important;
        }
        
        /* Все элементы с bg-green-100 и text-green-800 в темной теме */
        .theme-dark [class*="bg-green-100"][class*="text-green-800"] {
            color: #34d399 !important;
            background-color: #064e3b !important;
        }
        
        .theme-dark .text-purple-800 {
            color: #d8b4fe !important;
        }
        
        .theme-dark .text-orange-800 {
            color: #fdba74 !important;
        }
        
        /* Фон полей упражнений в темной теме - используем селекторы по классам градиента */
        .theme-dark [class*="from-indigo-50"][class*="to-blue-50"] {
            background: transparent !important;
        }
        
        .theme-dark [class*="from-green-50"][class*="to-emerald-50"] {
            background: transparent !important;
        }
        
        .theme-dark [class*="from-purple-50"][class*="to-violet-50"] {
            background: transparent !important;
        }
        
        .theme-dark [class*="from-orange-50"][class*="to-amber-50"] {
            background: transparent !important;
        }
        
        /* Поля в развернутых подходах (sets-fields-grid) - убираем заливку и делаем текст светлее */
        .theme-dark .sets-fields-grid [class*="from-purple-50"][class*="to-violet-50"],
        .theme-dark .sets-fields-grid [class*="from-green-50"][class*="to-emerald-50"],
        .theme-dark .sets-fields-grid [class*="from-orange-50"][class*="to-amber-50"] {
            background: transparent !important;
        }
        
        .theme-dark .sets-fields-grid .text-purple-900,
        .theme-dark .sets-fields-grid .text-green-900,
        .theme-dark .sets-fields-grid .text-orange-900 {
            color: #ffffff !important;
        }
        
        .theme-dark .sets-fields-grid .text-purple-800,
        .theme-dark .sets-fields-grid .text-green-800,
        .theme-dark .sets-fields-grid .text-orange-800 {
            color: #e0e7ff !important;
        }
        
        /* Текст "Згорнуты", "Вкажіть, що саме виконав" и "Підхід 1 з 3" - делаем светлее */
        .theme-dark .text-yellow-700 {
            color: #fbbf24 !important;
        }
        
        .theme-dark .text-yellow-800 {
            color: #fcd34d !important;
        }
        
        /* Убираем заливку для желтых блоков подходов */
        .theme-dark [class*="from-yellow-50"][class*="to-amber-50"] {
            background: transparent !important;
        }
        
        /* Границы полей упражнений в темной теме */
        .theme-dark .border-indigo-200 {
            border-color: #4f46e5 !important;
        }
        
        .theme-dark .border-green-200 {
            border-color: #10b981 !important;
        }
        
        .theme-dark .border-purple-200 {
            border-color: #a855f7 !important;
        }
        
        .theme-dark .border-orange-200 {
            border-color: #f97316 !important;
        }
        
        .theme-dark .border-yellow-200 {
            border-color: #fbbf24 !important;
        }
        
        /* Иконки в полях упражнений в темной теме */
        .theme-dark .text-indigo-600 {
            color: #818cf8 !important;
        }
        
        .theme-dark .text-green-600 {
            color: #34d399 !important;
        }
        
        .theme-dark .text-purple-600 {
            color: #c084fc !important;
        }
        
        .theme-dark .text-orange-600 {
            color: #fb923c !important;
        }
        
        .theme-dark .text-yellow-600 {
            color: #fbbf24 !important;
        }
        
        /* Группы мышц в карточках упражнений - делаем светлее */
        .theme-dark .text-black {
            color: #f9fafb !important;
        }
        
        .theme-dark .text-gray-500 {
            color: #d1d5db !important;
        }
        
        /* Текст инструкций и описания упражнений в темной теме */
        .theme-dark .text-gray-600,
        .theme-dark .text-gray-700 {
            color: #e5e7eb !important;
        }
        
        .theme-dark .bg-gray-50 .text-gray-700 {
            color: #f9fafb !important;
        }
        
        /* Поля ввода в профиле - более темный фон */
        .theme-dark .bg-white input,
        .theme-dark .bg-white textarea,
        .theme-dark .bg-white select {
            background-color: #374151 !important;
            color: #f9fafb !important;
            border-color: #4b5563 !important;
        }
        
        /* Стили для модального окна */
        #measurementModal {
            z-index: 9999;
        }
        
        .theme-dark #measurementModal .bg-white {
            background-color: #374151 !important;
        }
        
        .theme-dark #measurementModal .text-gray-900 {
            color: #f9fafb !important;
        }
        
        .theme-dark #measurementModal .text-gray-500 {
            color: #9ca3af !important;
        }
        
        .theme-dark #measurementModal .border-gray-100 {
            border-color: #4b5563 !important;
        }
    </style>
    
    <script>
        // Устанавливаем тему до загрузки страницы
        (function() {
            const applyTheme = () => {
                const body = document.body;
                if (!body) {
                    return;
                }
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                body.classList.add('theme-dark');
                body.classList.remove('theme-light');
            } else {
                body.classList.add('theme-light');
                body.classList.remove('theme-dark');
                }
            };

            if (document.body) {
                applyTheme();
            } else {
                document.addEventListener('DOMContentLoaded', applyTheme, { once: true });
            }
        })();
    </script>
    <?php echo $__env->yieldPushContent("styles"); ?>
</head>
<body class="theme-light">
    <!-- Мобильное меню -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="mobile-overlay" onclick="toggleMobileMenu()"></div>
        <div class="mobile-menu-content">
            <!-- Шапка мобильного меню -->
            <div class="mobile-menu-header">
                <div class="flex items-center gap-3">
                    <?php if($crmLogoLight || $crmLogoDark): ?>
                        <div class="flex items-center">
                            <?php if($crmLogoLight): ?>
                                <img src="<?php echo e(asset('storage/' . $crmLogoLight)); ?>" alt="<?php echo e($siteName); ?> logo" class="crm-logo-mobile crm-logo-mobile-light h-8 block">
                            <?php endif; ?>
                            <?php if($crmLogoDark): ?>
                                <img src="<?php echo e(asset('storage/' . $crmLogoDark)); ?>" alt="<?php echo e($siteName); ?> logo" class="crm-logo-mobile crm-logo-mobile-dark h-8 hidden">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-8 h-8 bg-indigo-600/10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <button onclick="toggleMobileMenu()" class="mobile-menu-close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="mobile-menu-nav">
                <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.dashboard*') ? 'active' : ''); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <?php echo e(__('common.dashboard')); ?>

                </a>
                <a href="<?php echo e(route('crm.calendar')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Календарь
                </a>
                <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.workouts.index') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.workouts') : route('crm.athlete.workouts'))); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.workouts.*') || request()->routeIs('crm.athlete.workouts*') || request()->routeIs('crm.self-athlete.workouts*') ? 'active' : ''); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <?php echo e(__('common.workouts')); ?>

                </a>
                <?php if(auth()->user()->hasRole('trainer')): ?>
                    <a href="<?php echo e(route("crm.exercises.index")); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.exercises.*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <?php echo e(__('common.exercises')); ?>

                    </a>
                    <a href="<?php echo e(route("crm.workout-templates.index")); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.workout-templates.*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <?php echo e(__('common.workout_templates')); ?>

                    </a>
                    <a href="<?php echo e(route('crm.trainer.athletes')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.trainer.athletes*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <?php echo e(__('common.athletes')); ?>

                    </a>
                    <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.trainer.subscription*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        <?php echo e(__('common.subscription')); ?>

                    </a>
                <?php else: ?>
                    <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.exercises') : route('crm.athlete.exercises')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.athlete.exercises*') || request()->routeIs('crm.self-athlete.exercises*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <?php echo e(__('common.exercises')); ?>

                    </a>
                    <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.progress') : route('crm.athlete.progress')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.athlete.progress*') || request()->routeIs('crm.self-athlete.progress*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <?php echo e(__('common.progress')); ?>

                    </a>
                    <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.measurements') : route('crm.athlete.measurements')); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.athlete.measurements*') || request()->routeIs('crm.self-athlete.measurements*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <?php echo e(__('common.measurements')); ?>

                    </a>
                    <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.nutrition.index') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.nutrition') : route('crm.athlete.nutrition'))); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.nutrition.*') || request()->routeIs('crm.athlete.nutrition*') || request()->routeIs('crm.self-athlete.nutrition*') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <?php echo e(__('common.nutrition')); ?>

                    </a>
                <?php endif; ?>
                <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.trainer.settings') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.settings') : route('crm.athlete.settings'))); ?>" class="mobile-nav-link <?php echo e(request()->routeIs('crm.trainer.settings*') || request()->routeIs('crm.athlete.settings*') || request()->routeIs('crm.self-athlete.settings*') ? 'active' : ''); ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <?php echo e(__('common.settings')); ?>

                </a>
            </nav>
            
            <!-- Выход -->
            <div class="mobile-menu-footer">
                <form method="POST" action="<?php echo e(route('crm.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="mobile-logout-btn">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="flex min-h-screen pt-6">
        <!-- Боковая панель (планшет и десктоп) -->
        <div style="display: none;" class="desktop-sidebar">
            <div class="sidebar flex flex-col flex-grow backdrop-blur-md">
                <!-- Логотип -->
                <div class="flex items-center px-6 py-6 gap-3">
                    <?php if($crmLogoLight || $crmLogoDark): ?>
                        <div class="flex items-center">
                            <?php if($crmLogoLight): ?>
                                <img src="<?php echo e(asset('storage/' . $crmLogoLight)); ?>" alt="<?php echo e($siteName); ?> logo" class="crm-logo crm-logo-light">
                            <?php endif; ?>
                            <?php if($crmLogoDark): ?>
                                <img src="<?php echo e(asset('storage/' . $crmLogoDark)); ?>" alt="<?php echo e($siteName); ?> logo" class="crm-logo crm-logo-dark <?php echo e($crmLogoLight ? '' : 'block'); ?>">
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white"><?php echo e($siteName); ?> CRM</span>
                    <?php endif; ?>
                </div>
                
                <!-- Навигация -->
                <nav class="flex-1 px-4 space-y-2">
                    <a href="<?php echo e(route("crm.dashboard.main")); ?>" class="nav-link <?php echo e(request()->routeIs('crm.dashboard*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <?php echo e(__('common.dashboard')); ?>

                    </a>
                    <a href="<?php echo e(route('crm.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.calendar') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?php echo e(__('common.calendar')); ?>

                    </a>
                    <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.workouts.index') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.workouts') : route('crm.athlete.workouts'))); ?>" class="nav-link <?php echo e(request()->routeIs('crm.workouts.*') || request()->routeIs('crm.athlete.workouts*') || request()->routeIs('crm.self-athlete.workouts*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <?php echo e(__('common.workouts')); ?>

                    </a>
                    <?php if(auth()->user()->hasRole('trainer')): ?>
                        <a href="<?php echo e(route("crm.exercises.index")); ?>" class="nav-link <?php echo e(request()->routeIs('crm.exercises.*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <?php echo e(__('common.exercises')); ?>

                        </a>
                        <a href="<?php echo e(route("crm.workout-templates.index")); ?>" class="nav-link <?php echo e(request()->routeIs('crm.workout-templates.*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <?php echo e(__('common.workout_templates')); ?>

                        </a>
                        <a href="<?php echo e(route('crm.trainer.athletes')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.trainer.athletes*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <?php echo e(__('common.athletes')); ?>

                        </a>
                        <a href="<?php echo e(route('crm.trainer.subscription')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.trainer.subscription*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <?php echo e(__('common.subscription')); ?>

                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.exercises') : route('crm.athlete.exercises')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.athlete.exercises*') || request()->routeIs('crm.self-athlete.exercises*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            <?php echo e(__('common.exercises')); ?>

                        </a>
                        <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.progress') : route('crm.athlete.progress')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.athlete.progress*') || request()->routeIs('crm.self-athlete.progress*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <?php echo e(__('common.progress')); ?>

                        </a>
                        <a href="<?php echo e(auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.measurements') : route('crm.athlete.measurements')); ?>" class="nav-link <?php echo e(request()->routeIs('crm.athlete.measurements*') || request()->routeIs('crm.self-athlete.measurements*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <?php echo e(__('common.measurements')); ?>

                        </a>
                        <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.nutrition.index') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.nutrition') : route('crm.athlete.nutrition'))); ?>" class="nav-link <?php echo e(request()->routeIs('crm.nutrition.*') || request()->routeIs('crm.athlete.nutrition*') || request()->routeIs('crm.self-athlete.nutrition*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <?php echo e(__('common.nutrition')); ?>

                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.trainer.settings') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.settings') : route('crm.athlete.settings'))); ?>" class="nav-link <?php echo e(request()->routeIs('crm.trainer.settings*') || request()->routeIs('crm.athlete.settings*') || request()->routeIs('crm.self-athlete.settings*') ? 'active' : ''); ?> flex items-center px-4 py-3 rounded-xl mb-2 transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <?php echo e(__('common.settings')); ?>

                    </a>
                </nav>
                
                <!-- Пользователь -->
                <div class="px-4 py-6 border-t border-white/20">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-white"><?php echo e(auth()->user()->name ?? 'Пользователь'); ?></p>
                            <p class="text-xs text-white/70">
                                <?php if(auth()->user()->hasRole('trainer')): ?>
                                    Тренер
                                <?php elseif(auth()->user()->hasRole('self-athlete')): ?>
                                    Self-Athlete
                                <?php elseif(auth()->user()->hasRole('athlete')): ?>
                                    Спортсмен
                                <?php else: ?>
                                    Пользователь
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Кнопка выхода -->
                    <form method="POST" action="<?php echo e(route('crm.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-red-300 hover:bg-red-500/20 rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Выйти
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Основная область -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Верхняя панель -->
            <header class="header backdrop-blur-md px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Кнопка мобильного меню -->
                    <button onclick="toggleMobileMenu()" class="mobile-menu-btn text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Заголовок страницы -->
                    <h1 class="page-title text-2xl font-bold"><?php echo $__env->yieldContent("page-title", __('common.workouts')); ?></h1>
                    
                    <!-- Действия -->
                    <div class="flex items-center space-x-4">
                        
                        <!-- Переключатель темы -->
                        <button onclick="toggleTheme()" class="header-icon transition-colors">
                            <svg id="theme-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        
                        <!-- Аккаунт с выпадающим меню -->
                        <div class="relative" x-data="{ open: false }" x-cloak>
                            <button @click="open = !open" class="header-icon transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </button>
                            
                            <!-- Выпадающее меню -->
                            <div x-show="open" @click.away="open = false" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 account-dropdown py-1 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900"><?php echo e(auth()->user()->name ?? 'Пользователь'); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e(auth()->user()->email ?? ''); ?></p>
                                </div>
                                <a href="<?php echo e(route('crm.profile')); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <?php echo e(__('common.profile')); ?>

                                </a>
                                <a href="<?php echo e(auth()->user()->hasRole('trainer') ? route('crm.trainer.settings') : (auth()->user()->hasRole('self-athlete') ? route('crm.self-athlete.settings') : route('crm.athlete.settings'))); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <?php echo e(__('common.settings')); ?>

                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="<?php echo e(route('crm.logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <?php echo e(__('common.logout')); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </header>

            <!-- Контент -->
            <main class="flex-1 overflow-y-auto p-3">
                <div class="max-w-7xl mx-auto">
                    <?php echo $__env->yieldContent("content"); ?>
                </div>
            </main>
        </div>
    </div>

    <?php echo $__env->yieldPushContent("scripts"); ?>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('open');
        }
        
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            
            if (body.classList.contains('theme-dark')) {
                // Переключаем на светлую тему
                body.classList.remove('theme-dark');
                body.classList.add('theme-light');
                themeIcon.innerHTML = '<path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>';
                localStorage.setItem('theme', 'light');
            } else {
                // Переключаем на темную тему
                body.classList.remove('theme-light');
                body.classList.add('theme-dark');
                themeIcon.innerHTML = '<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Загружаем сохраненную тему
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            
            if (savedTheme === 'dark') {
                body.classList.remove('theme-light');
                body.classList.add('theme-dark');
                themeIcon.innerHTML = '<path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>';
            }
        });
    </script>
    
    <!-- Компоненты уведомлений и подтверждений -->
    <?php echo $__env->make('components.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.confirm-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html><?php /**PATH D:\OSPanel\domains\fitrain\resources\views/crm/layouts/app.blade.php ENDPATH**/ ?>
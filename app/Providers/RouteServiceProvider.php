<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Получаем значения доменов из конфига
            $mainDomain = config('app.main_domain', 'fitrain.local');
            $crmDomain = config('app.crm_domain', 'crm.fitrain.local');
            $panelDomain = config('app.panel_domain', 'panel.fitrain.local');

            // API маршруты
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Маршруты для основного домена - лендинг
            Route::middleware('web')
                ->domain($mainDomain)
                ->group(base_path('routes/landing.php'));

            // Маршруты для CRM поддомена
            Route::middleware('web')
                ->domain($crmDomain)
                ->group(base_path('routes/crm.php'));

            // Маршруты для админ панели
            Route::middleware('web')
                ->domain($panelDomain)
                ->group(base_path('routes/admin.php'));

            // Fallback маршруты для localhost (локальная разработка)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

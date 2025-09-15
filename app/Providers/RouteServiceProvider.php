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
            // API маршруты
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Маршруты для основного домена (fitrain.local) - лендинг
            Route::middleware('web')
                ->domain('fitrain.local')
                ->group(base_path('routes/landing.php'));

            // Маршруты для CRM поддомена (crm.fitrain.local)
            Route::middleware('web')
                ->domain('crm.fitrain.local')
                ->group(base_path('routes/crm.php'));

            // Маршруты для админ панели (panel.fitrain.local)
            Route::middleware('web')
                ->domain('panel.fitrain.local')
                ->group(base_path('routes/admin.php'));

            // Fallback маршруты для localhost (локальная разработка)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

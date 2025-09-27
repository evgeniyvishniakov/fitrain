<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\CurrencyController;

/*
|--------------------------------------------------------------------------
| Admin Routes (panel.fitrain.local)
|--------------------------------------------------------------------------
|
| Маршруты для админ панели
|
*/

// Маршруты аутентификации
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Защищенные маршруты
Route::middleware(['auth', 'admin'])->group(function () {
    
    // Главная страница
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard.main');
    
    // Управление пользователями
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    
    // Статистика и аналитика
    Route::prefix('statistics')->name('admin.statistics.')->group(function () {
        Route::get('/', [StatisticsController::class, 'index'])->name('index');
        Route::get('/users', [StatisticsController::class, 'users'])->name('users');
        Route::get('/workouts', [StatisticsController::class, 'workouts'])->name('workouts');
        Route::get('/export/{type}', [StatisticsController::class, 'export'])->name('export');
    });
    
    // Управление языками
    Route::prefix('languages')->name('admin.languages.')->group(function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::get('/create', [LanguageController::class, 'create'])->name('create');
        Route::post('/', [LanguageController::class, 'store'])->name('store');
        Route::get('/{language}', [LanguageController::class, 'show'])->name('show');
        Route::get('/{language}/edit', [LanguageController::class, 'edit'])->name('edit');
        Route::put('/{language}', [LanguageController::class, 'update'])->name('update');
        Route::delete('/{language}', [LanguageController::class, 'destroy'])->name('destroy');
        Route::post('/{language}/set-default', [LanguageController::class, 'setDefault'])->name('set-default');
        Route::post('/{language}/toggle-status', [LanguageController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Управление валютами
    Route::prefix('currencies')->name('admin.currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::get('/{currency}', [CurrencyController::class, 'show'])->name('show');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
        Route::post('/{currency}/set-default', [CurrencyController::class, 'setDefault'])->name('set-default');
        Route::post('/{currency}/toggle-status', [CurrencyController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/update-rates', [CurrencyController::class, 'updateRates'])->name('update-rates');
    });

    // Системные функции
    Route::prefix('system')->name('admin.system.')->group(function () {
        Route::get('/', [SystemController::class, 'index'])->name('index');
        Route::post('/clear-cache', [SystemController::class, 'clearCache'])->name('clear-cache');
        Route::post('/optimize', [SystemController::class, 'optimize'])->name('optimize');
        Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
        Route::post('/backup', [SystemController::class, 'backup'])->name('backup');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\Auth\LoginController;
use App\Http\Controllers\Crm\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| CRM Routes (crm.fitrain.local)
|--------------------------------------------------------------------------
|
| Маршруты для CRM системы - тренеры и клиенты
|
*/

// Главная страница CRM
Route::get('/', [DashboardController::class, 'index'])->name('crm.dashboard');

// Маршруты аутентификации
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('crm.login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('crm.logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('crm.register');
Route::post('/register', [RegisterController::class, 'register']);

// Защищенные маршруты
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('crm.dashboard.main');
    
    // Маршруты только для тренеров
    Route::middleware(['role:trainer'])->group(function () {
        Route::get('/clients', function () {
            return 'Страница клиентов (только для тренеров)';
        })->name('crm.clients');
    });
    
    // Маршруты для всех авторизованных
    Route::get('/calendar', function () {
        return 'Календарь';
    })->name('crm.calendar');
    
    Route::get('/workouts', function () {
        return 'Тренировки';
    })->name('crm.workouts');
    
    Route::get('/progress', function () {
        return 'Прогресс';
    })->name('crm.progress');
    
    Route::get('/nutrition', function () {
        return 'Дневник питания';
    })->name('crm.nutrition');
    
    Route::get('/settings', function () {
        return 'Настройки';
    })->name('crm.settings');
});

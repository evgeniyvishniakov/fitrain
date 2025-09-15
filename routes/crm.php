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
});

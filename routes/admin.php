<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Admin Routes (panel.fitrain.local)
|--------------------------------------------------------------------------
|
| Маршруты для админ панели
|
*/

// Главная страница админки
Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

// Маршруты аутентификации
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Защищенные маршруты
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard.main');
});

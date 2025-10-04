<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Быстрый доступ к админ панели для разработки
Route::get('/admin-panel', function () {
    return redirect()->route('admin.login');
});

// Админ панель через основной домен (fallback)
Route::prefix('admin')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login.fallback');
    Route::post('/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin.logout.fallback');
    
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard.fallback');
    });
});

// Временные маршруты для тестирования админки
Route::prefix('admin')->group(function () {
    Route::get('/languages', function () {
        return app(\App\Http\Controllers\Admin\LanguageController::class)->index();
    });
    
    Route::get('/languages/{id}', function ($id) {
        return app(\App\Http\Controllers\Admin\LanguageController::class)->show($id);
    });
    
    Route::get('/languages/{id}/edit', function ($id) {
        return app(\App\Http\Controllers\Admin\LanguageController::class)->edit($id);
    });
    
    Route::delete('/languages/{id}', function ($id) {
        return app(\App\Http\Controllers\Admin\LanguageController::class)->destroy($id);
    });
});

// Маршруты для переключения языка
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/current', [LanguageController::class, 'current'])->name('language.current');


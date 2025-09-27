<?php

use Illuminate\Support\Facades\Route;

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


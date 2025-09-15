<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landing\HomeController;

/*
|--------------------------------------------------------------------------
| Landing Routes (fitrain.local)
|--------------------------------------------------------------------------
|
| Маршруты для основного домена - лендинг и страница продаж
|
*/

Route::get('/', [HomeController::class, 'index'])->name('landing.home');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('landing.pricing');
Route::get('/features', [HomeController::class, 'features'])->name('landing.features');
Route::get('/about', [HomeController::class, 'about'])->name('landing.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('landing.contact');

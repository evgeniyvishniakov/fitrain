<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API для упражнений
Route::get('/exercises', function () {
    $exercises = \App\Models\Exercise::select('id', 'name', 'category', 'equipment', 'fields_config')->get();
    return response()->json([
        'success' => true,
        'exercises' => $exercises
    ]);
});

// API для шаблонов тренировок
Route::get('/workout-templates', function () {
    $templates = \App\Models\WorkoutTemplate::with('exercises')->get();
    return response()->json([
        'success' => true,
        'templates' => $templates
    ]);
});

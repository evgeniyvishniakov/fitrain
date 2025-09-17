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
    $templates = \App\Models\WorkoutTemplate::active()->get();
    
    // Обрабатываем каждый шаблон и загружаем полные данные упражнений
    $templates->each(function ($template) {
        if ($template->exercises && is_array($template->exercises)) {
            $template->exercises = collect($template->exercises)->map(function ($exerciseData) {
                // Проверяем формат данных упражнения
                if (isset($exerciseData['exercise_id'])) {
                    // Старый формат: {exercise_id, sets, reps, weight, rest}
                    $exercise = \App\Models\Exercise::find($exerciseData['exercise_id']);
                    if ($exercise) {
                        return [
                            'id' => $exercise->id,
                            'name' => $exercise->name,
                            'category' => $exercise->category,
                            'equipment' => $exercise->equipment,
                            'fields_config' => $exercise->fields_config,
                            'sets' => $exerciseData['sets'] ?? null,
                            'reps' => $exerciseData['reps'] ?? null,
                            'weight' => $exerciseData['weight'] ?? null,
                            'rest' => $exerciseData['rest'] ?? null
                        ];
                    }
                } elseif (isset($exerciseData['id'])) {
                    // Новый формат: {id, name, category, equipment}
                    $exercise = \App\Models\Exercise::find($exerciseData['id']);
                    if ($exercise) {
                        return [
                            'id' => $exercise->id,
                            'name' => $exercise->name,
                            'category' => $exercise->category,
                            'equipment' => $exercise->equipment,
                            'fields_config' => $exercise->fields_config
                        ];
                    }
                }
                return $exerciseData;
            })->filter()->values()->toArray();
        }
    });
    
    return response()->json([
        'success' => true,
        'templates' => $templates
    ]);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AthletePaymentController;

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
Route::middleware('web')->get('/exercises', [\App\Http\Controllers\Crm\Trainer\ExerciseController::class, 'api']);

// API для шаблонов тренировок
Route::get('/workout-templates', function () {
    $templates = \App\Models\Trainer\WorkoutTemplate::active()->get();
    
    // Обрабатываем каждый шаблон и загружаем полные данные упражнений
    $templates->each(function ($template) {
        // Используем валидные упражнения (только существующие)
        $template->valid_exercises = $template->valid_exercises;
        
        // Также обновляем exercises для обратной совместимости
        $template->exercises = $template->valid_exercises;
    });
    
    return response()->json([
        'success' => true,
        'templates' => $templates
    ]);
});

// API для платежей спортсменов
Route::middleware('web')->group(function () {
    Route::post('/athletes/{athleteId}/payments', [AthletePaymentController::class, 'store']);
    Route::put('/athletes/{athleteId}/payments/{paymentId}', [AthletePaymentController::class, 'update']);
    Route::delete('/athletes/{athleteId}/payments/{paymentId}', [AthletePaymentController::class, 'destroy']);
});

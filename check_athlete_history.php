<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Получаем ID спортсмена из аргументов командной строки
$athleteId = $argv[1] ?? null;
$exerciseId = $argv[2] ?? null;

if (!$athleteId || !$exerciseId) {
    echo "Использование: php check_athlete_history.php <athlete_id> <exercise_id>\n";
    exit(1);
}

echo "Проверяем историю для athlete_id: {$athleteId}, exercise_id: {$exerciseId}\n\n";

// 1. Проверяем есть ли тренировки у этого спортсмена
$workouts = DB::table('workouts')
    ->where('athlete_id', $athleteId)
    ->orderBy('date', 'desc')
    ->limit(5)
    ->get();

echo "Тренировки спортсмена:\n";
foreach ($workouts as $workout) {
    echo "- ID: {$workout->id}, Дата: {$workout->date}, Название: {$workout->title}\n";
}

echo "\n";

// 2. Проверяем есть ли это упражнение в тренировках
$exerciseInWorkouts = DB::table('workout_exercise')
    ->join('workouts', 'workout_exercise.workout_id', '=', 'workouts.id')
    ->where('workouts.athlete_id', $athleteId)
    ->where('workout_exercise.exercise_id', $exerciseId)
    ->orderBy('workouts.date', 'desc')
    ->get();

echo "Упражнение {$exerciseId} в тренировках спортсмена:\n";
foreach ($exerciseInWorkouts as $item) {
    echo "- Workout ID: {$item->workout_id}, Дата: {$item->date}, Название: {$item->title}\n";
}

echo "\n";

// 3. Проверяем только ПРОШЛЫЕ тренировки (как в коде)
$pastWorkouts = DB::table('workout_exercise')
    ->join('workouts', 'workout_exercise.workout_id', '=', 'workouts.id')
    ->where('workouts.athlete_id', $athleteId)
    ->where('workout_exercise.exercise_id', $exerciseId)
    ->where('workouts.date', '<', now()->toDateString())
    ->orderBy('workouts.date', 'desc')
    ->get();

echo "ПРОШЛЫЕ тренировки с упражнением {$exerciseId}:\n";
foreach ($pastWorkouts as $item) {
    echo "- Workout ID: {$item->workout_id}, Дата: {$item->date}, Название: {$item->title}\n";
}

echo "\n";

// 4. Проверяем есть ли прогресс для этого упражнения
$progress = DB::table('workout_exercise_progress')
    ->where('athlete_id', $athleteId)
    ->where('exercise_id', $exerciseId)
    ->get();

echo "Прогресс упражнения {$exerciseId} для спортсмена {$athleteId}:\n";
foreach ($progress as $item) {
    echo "- Workout ID: {$item->workout_id}, Статус: {$item->exercise_status}\n";
}

echo "\nГотово!\n";



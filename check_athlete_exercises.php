<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$athleteId = $argv[1] ?? 6;

echo "Упражнения в тренировках спортсмена {$athleteId}:\n\n";

// Получаем все упражнения из тренировок спортсмена
$exercises = DB::table('workout_exercise')
    ->join('workouts', 'workout_exercise.workout_id', '=', 'workouts.id')
    ->join('exercises', 'workout_exercise.exercise_id', '=', 'exercises.id')
    ->where('workouts.athlete_id', $athleteId)
    ->orderBy('workouts.date', 'desc')
    ->orderBy('exercises.name', 'asc')
    ->select('workout_exercise.exercise_id', 'exercises.name', 'workouts.date', 'workouts.title')
    ->get();

echo "Все упражнения:\n";
foreach ($exercises as $exercise) {
    echo "- ID: {$exercise->exercise_id}, Название: {$exercise->name}, Дата: {$exercise->date}, Тренировка: {$exercise->title}\n";
}

echo "\n";

// Группируем по упражнениям
$exerciseGroups = $exercises->groupBy('exercise_id');

echo "Упражнения с историей (группировка):\n";
foreach ($exerciseGroups as $exerciseId => $group) {
    $firstExercise = $group->first();
    $count = $group->count();
    echo "- ID: {$exerciseId}, Название: {$firstExercise->name}, Количество тренировок: {$count}\n";
}

echo "\nГотово!\n";



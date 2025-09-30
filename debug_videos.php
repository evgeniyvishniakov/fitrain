<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UserExerciseVideo;

echo "Проверка пользовательских видео:\n";
echo "================================\n";

$videos = UserExerciseVideo::all();

if ($videos->count() > 0) {
    echo "Найдено пользовательских видео: " . $videos->count() . "\n";
    foreach ($videos as $video) {
        echo "ID: {$video->id}, Exercise ID: {$video->exercise_id}, User ID: {$video->user_id}, Video URL: {$video->video_url}\n";
    }
} else {
    echo "Пользовательских видео не найдено!\n";
}

echo "\nПроверка API endpoint:\n";
echo "=====================\n";

// Симулируем запрос к API с аутентификацией
$user = \App\Models\User::first();
if ($user) {
    auth()->login($user);
    echo "Авторизован как пользователь ID: {$user->id}\n";
    
    $controller = new \App\Http\Controllers\Crm\Trainer\ExerciseController();
    $response = $controller->getAllUserVideos();
    
    echo "Response: " . $response->getContent() . "\n";
} else {
    echo "Пользователи не найдены!\n";
}

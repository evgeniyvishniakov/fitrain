<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Trainer\Exercise;

$exercise = Exercise::find(51);
if ($exercise) {
    $exercise->video_url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    $exercise->save();
    echo "Видео добавлено к упражнению: {$exercise->name}\n";
    echo "URL: {$exercise->video_url}\n";
} else {
    echo "Упражнение не найдено!\n";
}


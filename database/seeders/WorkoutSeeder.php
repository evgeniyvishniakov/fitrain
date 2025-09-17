<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trainer\Workout;
use App\Models\Shared\User;

class WorkoutSeeder extends Seeder
{
    public function run()
    {
        $trainer = User::where('email', 'trainer@fitrain.com')->first();
        $athlete1 = User::where('email', 'athlete@fitrain.com')->first();
        $athlete2 = User::where('email', 'athlete2@fitrain.com')->first();

        // Создаем тренировки для первого спортсмена
        Workout::create([
            'title' => 'Силовая тренировка',
            'description' => 'Тренировка на развитие силы и мышечной массы',
            'trainer_id' => $trainer->id,
            'athlete_id' => $athlete1->id,
            'date' => now()->addDays(1),
            'duration' => 90,
            'status' => 'planned',
        ]);

        Workout::create([
            'title' => 'Кардио тренировка',
            'description' => 'Интервальная кардио тренировка для сжигания жира',
            'trainer_id' => $trainer->id,
            'athlete_id' => $athlete1->id,
            'date' => now()->addDays(3),
            'duration' => 60,
            'status' => 'planned',
        ]);

        Workout::create([
            'title' => 'Функциональная тренировка',
            'description' => 'Тренировка на развитие функциональных качеств',
            'trainer_id' => $trainer->id,
            'athlete_id' => $athlete1->id,
            'date' => now()->subDays(1),
            'duration' => 75,
            'status' => 'completed',
        ]);

        // Создаем тренировки для второго спортсмена
        Workout::create([
            'title' => 'Кроссфит тренировка',
            'description' => 'Высокоинтенсивная кроссфит тренировка',
            'trainer_id' => $trainer->id,
            'athlete_id' => $athlete2->id,
            'date' => now()->addDays(2),
            'duration' => 45,
            'status' => 'planned',
        ]);

        Workout::create([
            'title' => 'Йога и растяжка',
            'description' => 'Восстановительная тренировка с элементами йоги',
            'trainer_id' => $trainer->id,
            'athlete_id' => $athlete2->id,
            'date' => now()->subDays(2),
            'duration' => 50,
            'status' => 'completed',
        ]);

        $this->command->info('Тестовые тренировки созданы');
    }
}

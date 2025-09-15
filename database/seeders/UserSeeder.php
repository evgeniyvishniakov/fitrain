<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Создаем тренера
        $trainer = User::create([
            'name' => 'Иван Петров',
            'email' => 'trainer@fitrain.com',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 123-45-67',
            'specialization' => 'Фитнес, бодибилдинг',
            'experience_years' => 5,
            'role' => 'trainer',
        ]);
        $trainer->assignRole('trainer');

        // Создаем спортсмена
        $athlete = User::create([
            'name' => 'Анна Сидорова',
            'email' => 'athlete@fitrain.com',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 765-43-21',
            'age' => 25,
            'weight' => 65.5,
            'height' => 170.0,
            'trainer_id' => $trainer->id,
            'role' => 'athlete',
        ]);
        $athlete->assignRole('athlete');

        // Создаем еще одного спортсмена
        $athlete2 = User::create([
            'name' => 'Михаил Козлов',
            'email' => 'athlete2@fitrain.com',
            'password' => Hash::make('password'),
            'phone' => '+7 (999) 555-44-33',
            'age' => 30,
            'weight' => 80.0,
            'height' => 180.0,
            'trainer_id' => $trainer->id,
            'role' => 'athlete',
        ]);
        $athlete2->assignRole('athlete');

        $this->command->info('Тестовые пользователи созданы:');
        $this->command->info('Тренер: trainer@fitrain.com / password');
        $this->command->info('Спортсмен 1: athlete@fitrain.com / password');
        $this->command->info('Спортсмен 2: athlete2@fitrain.com / password');
    }
}

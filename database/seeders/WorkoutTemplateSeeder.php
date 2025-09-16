<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkoutTemplate;
use App\Models\Exercise;
use App\Models\User;

class WorkoutTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Находим тренера или создаем системного пользователя
        $trainer = User::whereHas('roles', function($query) {
            $query->where('name', 'trainer');
        })->first();

        if (!$trainer) {
            $trainer = User::first();
        }

        // Базовые шаблоны тренировок
        $templates = [
            [
                'name' => 'Базовая силовая тренировка',
                'description' => 'Классическая силовая тренировка для развития основных мышечных групп',
                'category' => 'strength',
                'difficulty' => 'beginner',
                'estimated_duration' => 60,
                'exercises' => [
                    [
                        'exercise_id' => 1, // Жим лежа
                        'sets' => 3,
                        'reps' => '8-12',
                        'weight' => '60-80% от 1ПМ',
                        'rest' => 120
                    ],
                    [
                        'exercise_id' => 2, // Приседания
                        'sets' => 3,
                        'reps' => '8-12',
                        'weight' => '60-80% от 1ПМ',
                        'rest' => 120
                    ],
                    [
                        'exercise_id' => 3, // Тяга штанги в наклоне
                        'sets' => 3,
                        'reps' => '8-12',
                        'weight' => '60-80% от 1ПМ',
                        'rest' => 120
                    ]
                ],
                'is_public' => true
            ],
            [
                'name' => 'Кардио интервалы',
                'description' => 'Высокоинтенсивная интервальная тренировка для сжигания жира',
                'category' => 'cardio',
                'difficulty' => 'intermediate',
                'estimated_duration' => 30,
                'exercises' => [
                    [
                        'exercise_id' => 4, // Берпи
                        'sets' => 4,
                        'reps' => '30 сек',
                        'weight' => null,
                        'rest' => 30
                    ],
                    [
                        'exercise_id' => 5, // Прыжки на скакалке
                        'sets' => 4,
                        'reps' => '45 сек',
                        'weight' => null,
                        'rest' => 15
                    ]
                ],
                'is_public' => true
            ],
            [
                'name' => 'Тренировка на гибкость',
                'description' => 'Комплекс упражнений для развития гибкости и подвижности суставов',
                'category' => 'flexibility',
                'difficulty' => 'beginner',
                'estimated_duration' => 45,
                'exercises' => [
                    [
                        'exercise_id' => 6, // Растяжка спины
                        'sets' => 2,
                        'reps' => '60 сек',
                        'weight' => null,
                        'rest' => 30
                    ],
                    [
                        'exercise_id' => 7, // Растяжка ног
                        'sets' => 2,
                        'reps' => '45 сек',
                        'weight' => null,
                        'rest' => 30
                    ]
                ],
                'is_public' => true
            ],
            [
                'name' => 'Смешанная тренировка',
                'description' => 'Комбинация силовых и кардио упражнений',
                'category' => 'mixed',
                'difficulty' => 'intermediate',
                'estimated_duration' => 75,
                'exercises' => [
                    [
                        'exercise_id' => 1, // Жим лежа
                        'sets' => 3,
                        'reps' => '10-12',
                        'weight' => '70% от 1ПМ',
                        'rest' => 90
                    ],
                    [
                        'exercise_id' => 4, // Берпи
                        'sets' => 3,
                        'reps' => '20 сек',
                        'weight' => null,
                        'rest' => 40
                    ],
                    [
                        'exercise_id' => 2, // Приседания
                        'sets' => 3,
                        'reps' => '12-15',
                        'weight' => '60% от 1ПМ',
                        'rest' => 90
                    ]
                ],
                'is_public' => true
            ]
        ];

        foreach ($templates as $templateData) {
            WorkoutTemplate::create(array_merge($templateData, [
                'created_by' => $trainer->id
            ]));
        }

        $this->command->info('Создано ' . count($templates) . ' шаблонов тренировок');
    }
}
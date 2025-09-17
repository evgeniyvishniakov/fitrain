<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Trainer\WorkoutTemplate;
use App\Models\Trainer\Exercise;
use App\Models\Shared\User;

class GenerateWorkoutTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'templates:generate {--count=5 : Количество шаблонов для создания} {--trainer= : ID тренера}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматически генерирует шаблоны тренировок';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $trainerId = $this->option('trainer');

        // Находим тренера
        if ($trainerId) {
            $trainer = User::find($trainerId);
            if (!$trainer || !$trainer->hasRole('trainer')) {
                $this->error('Тренер с ID ' . $trainerId . ' не найден или не имеет роли тренера');
                return 1;
            }
        } else {
            $trainer = User::whereHas('roles', function($query) {
                $query->where('name', 'trainer');
            })->first();

            if (!$trainer) {
                $this->error('Тренер не найден. Создайте пользователя с ролью тренера.');
                return 1;
            }
        }

        // Получаем все активные упражнения
        $exercises = Exercise::active()->get();
        if ($exercises->isEmpty()) {
            $this->error('Нет доступных упражнений. Сначала создайте упражнения.');
            return 1;
        }

        $this->info("Создание {$count} шаблонов тренировок для тренера: {$trainer->name}");

        $categories = ['strength', 'cardio', 'flexibility', 'mixed'];
        $difficulties = ['beginner', 'intermediate', 'advanced'];

        for ($i = 0; $i < $count; $i++) {
            $category = $categories[array_rand($categories)];
            $difficulty = $difficulties[array_rand($difficulties)];

            $templateData = $this->generateTemplate($category, $difficulty, $exercises);

            WorkoutTemplate::create([
                'name' => $templateData['name'],
                'description' => $templateData['description'],
                'category' => $category,
                'difficulty' => $difficulty,
                'estimated_duration' => $templateData['duration'],
                'exercises' => $templateData['exercises'],
                'created_by' => $trainer->id,
                'is_public' => rand(0, 1) == 1
            ]);

            $this->line("✓ Создан шаблон: {$templateData['name']}");
        }

        $this->info("Успешно создано {$count} шаблонов тренировок!");
        return 0;
    }

    private function generateTemplate($category, $difficulty, $exercises)
    {
        $templates = [
            'strength' => [
                'beginner' => [
                    'name' => 'Базовая силовая тренировка',
                    'description' => 'Идеальная тренировка для начинающих в силовом спорте',
                    'duration' => 60,
                    'exercises_count' => 4
                ],
                'intermediate' => [
                    'name' => 'Прогрессивная силовая тренировка',
                    'description' => 'Тренировка для атлетов среднего уровня подготовки',
                    'duration' => 75,
                    'exercises_count' => 6
                ],
                'advanced' => [
                    'name' => 'Интенсивная силовая тренировка',
                    'description' => 'Сложная тренировка для опытных атлетов',
                    'duration' => 90,
                    'exercises_count' => 8
                ]
            ],
            'cardio' => [
                'beginner' => [
                    'name' => 'Легкое кардио',
                    'description' => 'Низкоинтенсивная кардио тренировка',
                    'duration' => 30,
                    'exercises_count' => 3
                ],
                'intermediate' => [
                    'name' => 'Интервальное кардио',
                    'description' => 'Высокоинтенсивная интервальная тренировка',
                    'duration' => 45,
                    'exercises_count' => 5
                ],
                'advanced' => [
                    'name' => 'Экстремальное кардио',
                    'description' => 'Максимально интенсивная кардио тренировка',
                    'duration' => 60,
                    'exercises_count' => 7
                ]
            ],
            'flexibility' => [
                'beginner' => [
                    'name' => 'Базовая растяжка',
                    'description' => 'Основные упражнения на гибкость',
                    'duration' => 30,
                    'exercises_count' => 5
                ],
                'intermediate' => [
                    'name' => 'Продвинутая растяжка',
                    'description' => 'Упражнения для улучшения подвижности',
                    'duration' => 45,
                    'exercises_count' => 7
                ],
                'advanced' => [
                    'name' => 'Профессиональная растяжка',
                    'description' => 'Сложные упражнения на гибкость',
                    'duration' => 60,
                    'exercises_count' => 9
                ]
            ],
            'mixed' => [
                'beginner' => [
                    'name' => 'Смешанная тренировка для начинающих',
                    'description' => 'Комбинация силовых и кардио упражнений',
                    'duration' => 45,
                    'exercises_count' => 5
                ],
                'intermediate' => [
                    'name' => 'Функциональная тренировка',
                    'description' => 'Разносторонняя тренировка всего тела',
                    'duration' => 60,
                    'exercises_count' => 7
                ],
                'advanced' => [
                    'name' => 'Кроссфит тренировка',
                    'description' => 'Высокоинтенсивная тренировка всех систем',
                    'duration' => 75,
                    'exercises_count' => 9
                ]
            ]
        ];

        $templateInfo = $templates[$category][$difficulty];
        
        // Выбираем упражнения для данного типа тренировки
        $categoryExercises = $exercises->where('category', $category)->take($templateInfo['exercises_count']);
        if ($categoryExercises->count() < $templateInfo['exercises_count']) {
            $categoryExercises = $exercises->random($templateInfo['exercises_count']);
        }

        $exercisesData = [];
        foreach ($categoryExercises as $exercise) {
            $sets = rand(2, 4);
            $rest = $category === 'cardio' ? rand(30, 60) : rand(60, 180);
            
            $exercisesData[] = [
                'exercise_id' => $exercise->id,
                'sets' => $sets,
                'reps' => $this->generateReps($category, $difficulty),
                'weight' => $category === 'cardio' || $category === 'flexibility' ? null : $this->generateWeight($difficulty),
                'rest' => $rest
            ];
        }

        return [
            'name' => $templateInfo['name'],
            'description' => $templateInfo['description'],
            'duration' => $templateInfo['duration'],
            'exercises' => $exercisesData
        ];
    }

    private function generateReps($category, $difficulty)
    {
        $reps = [
            'strength' => [
                'beginner' => ['8-12', '10-15', '12-15'],
                'intermediate' => ['6-10', '8-12', '10-12'],
                'advanced' => ['4-8', '6-10', '8-10']
            ],
            'cardio' => [
                'beginner' => ['30 сек', '45 сек', '1 мин'],
                'intermediate' => ['45 сек', '1 мин', '1.5 мин'],
                'advanced' => ['1 мин', '2 мин', '3 мин']
            ],
            'flexibility' => [
                'beginner' => ['30 сек', '45 сек', '1 мин'],
                'intermediate' => ['45 сек', '1 мин', '1.5 мин'],
                'advanced' => ['1 мин', '2 мин', '3 мин']
            ],
            'mixed' => [
                'beginner' => ['8-12', '30 сек', '10-15'],
                'intermediate' => ['6-10', '45 сек', '8-12'],
                'advanced' => ['4-8', '1 мин', '6-10']
            ]
        ];

        return $reps[$category][$difficulty][array_rand($reps[$category][$difficulty])];
    }

    private function generateWeight($difficulty)
    {
        $weights = [
            'beginner' => ['50-60% от 1ПМ', '40-50% от 1ПМ', 'Собственный вес'],
            'intermediate' => ['60-70% от 1ПМ', '70-80% от 1ПМ', '50-60% от 1ПМ'],
            'advanced' => ['80-90% от 1ПМ', '70-85% от 1ПМ', '60-75% от 1ПМ']
        ];

        return $weights[$difficulty][array_rand($weights[$difficulty])];
    }
}
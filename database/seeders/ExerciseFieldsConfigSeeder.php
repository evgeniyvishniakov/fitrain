<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trainer\Exercise;

class ExerciseFieldsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Обновляем все существующие упражнения с конфигурацией полей
        
        // Упражнения с весом (силовые)
        $weightExercises = ['Жим лежа', 'Приседания', 'Тяга штанги в наклоне', 'Жим гантелей сидя'];
        foreach ($weightExercises as $name) {
            $exercise = Exercise::where('name', $name)->first();
            if ($exercise) {
                $exercise->update(['fields_config' => ['sets', 'reps', 'weight', 'rest']]);
            }
        }
        
        // Упражнения с собственным весом (без веса)
        $bodyweightExercises = ['Подтягивания', 'Отжимания'];
        foreach ($bodyweightExercises as $name) {
            $exercise = Exercise::where('name', $name)->first();
            if ($exercise) {
                $exercise->update(['fields_config' => ['sets', 'reps', 'rest']]);
            }
        }
        
        // Кардио упражнения
        $cardioExercises = ['Прыжки на скакалке', 'Берпи'];
        foreach ($cardioExercises as $name) {
            $exercise = Exercise::where('name', $name)->first();
            if ($exercise) {
                $exercise->update(['fields_config' => ['time', 'tempo']]);
            }
        }
        
        // Упражнения на гибкость
        $flexibilityExercises = ['Растяжка спины', 'Растяжка ног'];
        foreach ($flexibilityExercises as $name) {
            $exercise = Exercise::where('name', $name)->first();
            if ($exercise) {
                $exercise->update(['fields_config' => ['time', 'rest']]);
            }
        }
        
        // Удаляем тестовые упражнения
        Exercise::where('name', 'вапвапвапвап')->delete();
        
        // Если упражнений нет, создаем примеры
        if (Exercise::count() === 0) {
            $exercises = [
                [
                    'name' => 'Жим лежа',
                    'description' => 'Базовое упражнение для развития грудных мышц',
                    'category' => 'Грудь',
                    'equipment' => 'Штанга',
                    'muscle_groups' => ['Грудь', 'Трицепс', 'Плечи'],
                    'difficulty' => 'intermediate',
                    'is_active' => true,
                    'fields_config' => ['sets', 'reps', 'weight', 'rest']
                ],
                [
                    'name' => 'Приседания',
                    'description' => 'Базовое упражнение для развития ног',
                    'category' => 'Ноги',
                    'equipment' => 'Штанга',
                    'muscle_groups' => ['Квадрицепс', 'Ягодицы', 'Бицепс бедра'],
                    'difficulty' => 'intermediate',
                    'is_active' => true,
                    'fields_config' => ['sets', 'reps', 'weight', 'rest']
                ],
                [
                    'name' => 'Подтягивания',
                    'description' => 'Упражнение с собственным весом для спины',
                    'category' => 'Спина',
                    'equipment' => 'Турник',
                    'muscle_groups' => ['Широчайшие', 'Бицепс', 'Ромбовидные'],
                    'difficulty' => 'intermediate',
                    'is_active' => true,
                    'fields_config' => ['sets', 'reps', 'rest']
                ],
                [
                    'name' => 'Планка',
                    'description' => 'Статическое упражнение для кора',
                    'category' => 'Гибкость',
                    'equipment' => 'Собственный вес',
                    'muscle_groups' => ['Пресс', 'Косые мышцы', 'Мышцы спины'],
                    'difficulty' => 'beginner',
                    'is_active' => true,
                    'fields_config' => ['sets', 'time', 'rest']
                ],
                [
                    'name' => 'Бег',
                    'description' => 'Кардио упражнение',
                    'category' => 'Кардио',
                    'equipment' => 'Собственный вес',
                    'muscle_groups' => ['Ноги', 'Сердце'],
                    'difficulty' => 'beginner',
                    'is_active' => true,
                    'fields_config' => ['distance', 'time', 'tempo']
                ],
                [
                    'name' => 'Скакалка',
                    'description' => 'Кардио упражнение со скакалкой',
                    'category' => 'Кардио',
                    'equipment' => 'Скакалка',
                    'muscle_groups' => ['Ноги', 'Плечи', 'Сердце'],
                    'difficulty' => 'beginner',
                    'is_active' => true,
                    'fields_config' => ['time', 'tempo']
                ]
            ];
            
            foreach ($exercises as $exerciseData) {
                Exercise::create($exerciseData);
            }
        }
    }
}
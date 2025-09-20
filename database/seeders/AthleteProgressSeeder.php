<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shared\User;
use App\Models\Trainer\AthleteMeasurement;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AthleteProgressSeeder extends Seeder
{
    public function run()
    {
        // Получаем тренера
        $trainer = User::whereHas('roles', function($query) {
            $query->where('name', 'trainer');
        })->first();

        if (!$trainer) {
            $this->command->error('Тренер не найден! Сначала запустите UserSeeder.');
            return;
        }

        // Создаем 5 спортсменов с разными профилями
        $athletes = [
            [
                'name' => 'Анна Сидорова',
                'email' => 'anna.sidorova@fitrain.com',
                'gender' => 'female',
                'birth_date' => Carbon::now()->subYears(28)->subMonths(3),
                'sport_level' => 'intermediate',
                'goals' => ['weight_loss', 'muscle_tone', 'endurance'],
                'current_weight' => 72.0,
                'current_height' => 165.0,
                'health_restrictions' => ['knee_injury_2022'],
                'is_active' => true,
                'profile_type' => 'weight_loss' // Тип прогресса
            ],
            [
                'name' => 'Михаил Козлов',
                'email' => 'mikhail.kozlov@fitrain.com',
                'gender' => 'male',
                'birth_date' => Carbon::now()->subYears(32)->subMonths(7),
                'sport_level' => 'beginner',
                'goals' => ['muscle_gain', 'strength'],
                'current_weight' => 78.0,
                'current_height' => 178.0,
                'health_restrictions' => [],
                'is_active' => true,
                'profile_type' => 'muscle_gain'
            ],
            [
                'name' => 'Елена Волкова',
                'email' => 'elena.volkova@fitrain.com',
                'gender' => 'female',
                'birth_date' => Carbon::now()->subYears(24)->subMonths(2),
                'sport_level' => 'advanced',
                'goals' => ['competition_prep', 'strength', 'endurance'],
                'current_weight' => 58.0,
                'current_height' => 162.0,
                'health_restrictions' => [],
                'is_active' => true,
                'profile_type' => 'competition'
            ],
            [
                'name' => 'Дмитрий Соколов',
                'email' => 'dmitry.sokolov@fitrain.com',
                'gender' => 'male',
                'birth_date' => Carbon::now()->subYears(35)->subMonths(5),
                'sport_level' => 'intermediate',
                'goals' => ['weight_loss', 'health', 'endurance'],
                'current_weight' => 95.0,
                'current_height' => 182.0,
                'health_restrictions' => ['diabetes_type_2'],
                'is_active' => true,
                'profile_type' => 'health_improvement'
            ],
            [
                'name' => 'Ольга Морозова',
                'email' => 'olga.morozova@fitrain.com',
                'gender' => 'female',
                'birth_date' => Carbon::now()->subYears(29)->subMonths(8),
                'sport_level' => 'beginner',
                'goals' => ['weight_loss', 'flexibility', 'general_fitness'],
                'current_weight' => 68.0,
                'current_height' => 168.0,
                'health_restrictions' => [],
                'is_active' => true,
                'profile_type' => 'general_fitness'
            ]
        ];

        $createdAthletes = [];

        foreach ($athletes as $athleteData) {
            $athlete = User::create([
                'name' => $athleteData['name'],
                'email' => $athleteData['email'],
                'password' => Hash::make('password'),
                'phone' => '+7 (999) ' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(10, 99),
                'trainer_id' => $trainer->id,
                'birth_date' => $athleteData['birth_date'],
                'gender' => $athleteData['gender'],
                'sport_level' => $athleteData['sport_level'],
                'goals' => $athleteData['goals'],
                'current_weight' => $athleteData['current_weight'],
                'current_height' => $athleteData['current_height'],
                'health_restrictions' => $athleteData['health_restrictions'],
                'is_active' => $athleteData['is_active'],
                'last_medical_checkup' => Carbon::now()->subMonths(rand(1, 6)),
                'profile_modules' => ['measurements', 'progress', 'nutrition', 'workouts']
            ]);

            $athlete->assignRole('athlete');
            $createdAthletes[] = [
                'athlete' => $athlete,
                'profile_type' => $athleteData['profile_type']
            ];

            $this->command->info("Создан спортсмен: {$athlete->name}");
        }

        // Создаем измерения для каждого спортсмена за год (12 месяцев)
        foreach ($createdAthletes as $athleteData) {
            $athlete = $athleteData['athlete'];
            $profileType = $athleteData['profile_type'];

            $this->command->info("Создаю измерения для {$athlete->name}...");

            // Начальные значения
            $currentWeight = $athlete->current_weight;
            $currentHeight = $athlete->current_height;
            $currentBodyFat = $this->getInitialBodyFat($athlete->gender, $profileType);
            $currentMuscleMass = $this->getInitialMuscleMass($athlete->gender, $currentWeight, $currentBodyFat);

            // Создаем измерения за каждый месяц
            for ($month = 0; $month < 12; $month++) {
                $measurementDate = Carbon::now()->subMonths(11 - $month)->startOfMonth();

                // Рассчитываем прогресс в зависимости от типа профиля
                $progress = $this->calculateProgress($profileType, $month, $athlete->gender);

                // Обновляем значения
                $currentWeight += $progress['weight_change'];
                $currentBodyFat += $progress['body_fat_change'];
                $currentMuscleMass += $progress['muscle_mass_change'];

                // Создаем измерение
                AthleteMeasurement::create([
                    'athlete_id' => $athlete->id,
                    'measurement_date' => $measurementDate,
                    'weight' => round($currentWeight, 1),
                    'height' => $currentHeight, // Рост обычно не меняется у взрослых
                    'body_fat_percentage' => round(max(5, min(40, $currentBodyFat)), 1),
                    'muscle_mass' => round(max(20, $currentMuscleMass), 1),
                    'water_percentage' => round(50 + rand(-5, 5), 1),
                    'chest' => round($this->calculateChest($athlete->gender, $currentWeight, $currentMuscleMass), 1),
                    'waist' => round($this->calculateWaist($athlete->gender, $currentWeight, $currentBodyFat), 1),
                    'hips' => round($this->calculateHips($athlete->gender, $currentWeight), 1),
                    'bicep' => round($this->calculateBicep($athlete->gender, $currentMuscleMass), 1),
                    'thigh' => round($this->calculateThigh($athlete->gender, $currentWeight), 1),
                    'neck' => round($this->calculateNeck($athlete->gender, $currentWeight), 1),
                    'resting_heart_rate' => round(65 + rand(-10, 10), 0),
                    'blood_pressure_systolic' => round(120 + rand(-15, 15), 0),
                    'blood_pressure_diastolic' => round(80 + rand(-10, 10), 0),
                    'notes' => $this->getProgressNotes($profileType, $month),
                    'measured_by' => 'trainer'
                ]);
            }

            // Обновляем текущие значения спортсмена
            $athlete->update([
                'current_weight' => $currentWeight,
                'current_height' => $currentHeight
            ]);
        }

        $this->command->info('✅ Создано 5 спортсменов с годовыми измерениями!');
        $this->command->info('📊 Каждый спортсмен имеет 12 измерений с реалистичным прогрессом');
    }

    private function getInitialBodyFat($gender, $profileType)
    {
        $baseBodyFat = $gender === 'male' ? 18 : 25;
        
        switch ($profileType) {
            case 'weight_loss':
                return $baseBodyFat + rand(3, 8);
            case 'muscle_gain':
                return $baseBodyFat - rand(2, 5);
            case 'competition':
                return $baseBodyFat - rand(5, 10);
            case 'health_improvement':
                return $baseBodyFat + rand(5, 12);
            default:
                return $baseBodyFat + rand(-2, 4);
        }
    }

    private function getInitialMuscleMass($gender, $weight, $bodyFatPercentage)
    {
        $leanMass = $weight * (1 - $bodyFatPercentage / 100);
        return $leanMass * ($gender === 'male' ? 0.9 : 0.85); // Примерная доля мышц от безжировой массы
    }

    private function calculateProgress($profileType, $month, $gender)
    {
        $progress = [
            'weight_change' => 0,
            'body_fat_change' => 0,
            'muscle_mass_change' => 0
        ];

        switch ($profileType) {
            case 'weight_loss':
                // Постепенная потеря веса
                $progress['weight_change'] = -0.3 - (rand(0, 10) / 100); // 0.3-0.4 кг в месяц
                $progress['body_fat_change'] = -0.4 - (rand(0, 20) / 100); // снижение жира
                $progress['muscle_mass_change'] = rand(-5, 10) / 100; // небольшие изменения мышц
                break;

            case 'muscle_gain':
                // Набор мышечной массы
                $progress['weight_change'] = 0.2 + (rand(0, 15) / 100); // 0.2-0.35 кг в месяц
                $progress['body_fat_change'] = rand(-5, 15) / 100; // небольшое увеличение жира
                $progress['muscle_mass_change'] = 0.15 + (rand(0, 10) / 100); // рост мышц
                break;

            case 'competition':
                // Подготовка к соревнованиям
                if ($month < 6) {
                    $progress['weight_change'] = -0.4 - (rand(0, 15) / 100);
                    $progress['body_fat_change'] = -0.5 - (rand(0, 20) / 100);
                    $progress['muscle_mass_change'] = rand(-10, 15) / 100;
                } else {
                    $progress['weight_change'] = -0.2 - (rand(0, 10) / 100);
                    $progress['body_fat_change'] = -0.3 - (rand(0, 15) / 100);
                    $progress['muscle_mass_change'] = rand(-5, 10) / 100;
                }
                break;

            case 'health_improvement':
                // Улучшение здоровья
                $progress['weight_change'] = -0.5 - (rand(0, 20) / 100); // более быстрая потеря веса
                $progress['body_fat_change'] = -0.6 - (rand(0, 25) / 100);
                $progress['muscle_mass_change'] = rand(-10, 20) / 100;
                break;

            case 'general_fitness':
                // Общая физическая подготовка
                $progress['weight_change'] = -0.2 + (rand(-10, 10) / 100); // стабильный вес
                $progress['body_fat_change'] = -0.2 - (rand(0, 15) / 100);
                $progress['muscle_mass_change'] = rand(0, 15) / 100;
                break;
        }

        return $progress;
    }

    private function calculateChest($gender, $weight, $muscleMass)
    {
        $base = $gender === 'male' ? 95 : 85;
        $weightFactor = ($weight - 70) * 0.3;
        $muscleFactor = ($muscleMass - 35) * 0.2;
        return $base + $weightFactor + $muscleFactor;
    }

    private function calculateWaist($gender, $weight, $bodyFat)
    {
        $base = $gender === 'male' ? 85 : 75;
        $weightFactor = ($weight - 70) * 0.4;
        $fatFactor = ($bodyFat - 20) * 0.5;
        return $base + $weightFactor + $fatFactor;
    }

    private function calculateHips($gender, $weight)
    {
        $base = $gender === 'male' ? 95 : 100;
        $weightFactor = ($weight - 70) * 0.3;
        return $base + $weightFactor;
    }

    private function calculateBicep($gender, $muscleMass)
    {
        $base = $gender === 'male' ? 32 : 26;
        $muscleFactor = ($muscleMass - 35) * 0.15;
        return $base + $muscleFactor;
    }

    private function calculateThigh($gender, $weight)
    {
        $base = $gender === 'male' ? 55 : 58;
        $weightFactor = ($weight - 70) * 0.2;
        return $base + $weightFactor;
    }

    private function calculateNeck($gender, $weight)
    {
        $base = $gender === 'male' ? 38 : 32;
        $weightFactor = ($weight - 70) * 0.1;
        return $base + $weightFactor;
    }

    private function getProgressNotes($profileType, $month)
    {
        $notes = [
            'weight_loss' => [
                'Отличный прогресс в снижении веса! Продолжаем работу над питанием.',
                'Вес снижается стабильно. Добавили кардио тренировки.',
                'Хорошие результаты. Корректируем программу питания.',
                'Прогресс замедлился, меняем подход к тренировкам.',
                'Отличный месяц! Вес продолжает снижаться.',
                'Стабильные результаты. Добавили интервальные тренировки.',
                'Прогресс стабильный. Работаем над мышечным тонусом.',
                'Отличные результаты! Близко к цели.',
                'Финальная прямая. Интенсивные тренировки.',
                'Почти достигли цели! Корректируем детали.',
                'Цель достигнута! Поддерживаем результат.',
                'Отличные результаты за год! Поддерживаем форму.'
            ],
            'muscle_gain' => [
                'Начало программы набора массы. Фокус на силовые тренировки.',
                'Прогресс в силе виден. Увеличиваем веса.',
                'Мышцы растут! Корректируем питание.',
                'Отличный прогресс в объемах. Добавляем упражнения.',
                'Стабильный рост мышц. Увеличиваем калории.',
                'Прогресс в силе и массе. Меняем программу.',
                'Отличные результаты! Работаем над детализацией.',
                'Мышцы растут стабильно. Добавляем изоляцию.',
                'Прогресс замедлился. Меняем подход.',
                'Отличный месяц! Масса растет.',
                'Стабильные результаты. Финальная прямая.',
                'Отличный год! Цель набора массы достигнута.'
            ],
            'competition' => [
                'Подготовка к соревнованиям. Начальная фаза.',
                'Базовая подготовка. Работаем над формой.',
                'Прогресс в форме. Корректируем питание.',
                'Форма улучшается. Добавляем кардио.',
                'Отличный прогресс! Близко к пику формы.',
                'Пиковая форма. Финальные корректировки.',
                'Поддержание формы. Готовимся к выступлению.',
                'Соревновательная форма. Финальная подготовка.',
                'Выступление прошло отлично! Восстановление.',
                'Восстановительный период. Планируем следующий цикл.',
                'Новый цикл подготовки. Работаем над слабыми местами.',
                'Готовность к новому сезону. Планируем цели.'
            ],
            'health_improvement' => [
                'Начало программы оздоровления. Медленный старт.',
                'Прогресс в самочувствии. Работаем над выносливостью.',
                'Вес снижается стабильно. Улучшается здоровье.',
                'Отличные результаты! Самочувствие улучшается.',
                'Стабильный прогресс. Добавляем активности.',
                'Прогресс в выносливости. Работаем над силой.',
                'Отличное самочувствие! Продолжаем программу.',
                'Цели здоровья достигнуты. Поддерживаем результат.',
                'Стабильные результаты. Планируем новые цели.',
                'Отличный прогресс! Здоровье значительно улучшилось.',
                'Поддерживаем достигнутые результаты.',
                'Год оздоровления завершен успешно!'
            ],
            'general_fitness' => [
                'Начало общей программы. Работаем над базой.',
                'Прогресс в общей физической подготовке.',
                'Улучшается выносливость и сила. Продолжаем.',
                'Стабильный прогресс во всех аспектах.',
                'Отличные результаты! Добавляем разнообразие.',
                'Прогресс в координации и гибкости.',
                'Стабильные результаты. Работаем над слабостями.',
                'Отличный прогресс! Все показатели улучшились.',
                'Поддерживаем форму. Планируем новые цели.',
                'Стабильные результаты. Готовы к новым вызовам.',
                'Отличная форма! Поддерживаем достигнутое.',
                'Год тренировок завершен успешно!'
            ]
        ];

        return $notes[$profileType][$month] ?? 'Регулярное измерение показателей.';
    }
}

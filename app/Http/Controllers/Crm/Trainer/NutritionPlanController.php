<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Controller;
use App\Models\NutritionPlan;
use App\Models\NutritionDay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NutritionPlanController extends Controller
{
    /**
     * Список планов питания для спортсмена
     */
    public function index(Request $request)
    {
        $athleteId = $request->get('athlete_id');
        
        if (!$athleteId) {
            return response()->json(['error' => 'ID спортсмена не указан'], 400);
        }

        // Проверяем, что тренер имеет доступ к этому спортсмену
        $athlete = User::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->first();

        if (!$athlete) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $plans = NutritionPlan::with(['nutritionDays' => function($query) {
            $query->orderBy('date');
        }])
        ->where('athlete_id', $athleteId)
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        // Преобразуем nutritionDays в nutrition_days для совместимости с фронтендом
        $plans->transform(function ($plan) {
            $plan->nutrition_days = $plan->nutritionDays;
            unset($plan->nutritionDays);
            return $plan;
        });

        return response()->json($plans);
    }

    /**
     * Создать новый план питания
     */
        public function store(Request $request)
        {
            // Записываем в лог сразу, чтобы убедиться, что метод вызывается
            \Log::info('=== МЕТОД STORE ВЫЗВАН ===', ['время' => now()]);
            
            try {
                \Log::info('=== НАЧАЛО СОЗДАНИЯ ПЛАНА ПИТАНИЯ ===');
                \Log::info('Данные запроса:', $request->all());
                \Log::info('Месяц: ' . $request->month);
                \Log::info('Год: ' . $request->year);
                \Log::info('Количество дней в запросе: ' . ($request->has('days') ? count($request->days) : 0));
                $request->validate([
                    'athlete_id' => 'required|exists:users,id',
                    'month' => 'required|integer|min:1|max:12',
                    'year' => 'required|integer|min:2020|max:2030',
                    'title' => 'nullable|string|max:255',
                    'description' => 'nullable|string',
                    'days' => 'nullable|array',
                    'days.*.date' => 'nullable|date',
                    'days.*.proteins' => 'nullable|numeric|min:0',
                    'days.*.fats' => 'nullable|numeric|min:0',
                    'days.*.carbs' => 'nullable|numeric|min:0',
                    'days.*.notes' => 'nullable|string'
                ]);
                
                \Log::info('Валидация прошла успешно');

            // Проверяем доступ
            $athlete = User::where('id', $request->athlete_id)
                ->where('trainer_id', auth()->id())
                ->first();

            if (!$athlete) {
                \Log::error('Доступ запрещен для спортсмена: ' . $request->athlete_id);
                return response()->json(['error' => 'Доступ запрещен'], 403);
            }

            \Log::info('Доступ подтвержден для спортсмена: ' . $athlete->id);

            DB::beginTransaction();

            // Ищем существующий план на этот месяц
            $plan = NutritionPlan::where('athlete_id', $request->athlete_id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->first();

            if ($plan) {
                // Обновляем существующий план
                $plan->update([
                    'title' => $request->title,
                    'description' => $request->description
                ]);
                \Log::info('План питания обновлен с ID: ' . $plan->id);
                
                // НЕ удаляем существующие дни - будем обновлять/добавлять только переданные дни
                // Это позволяет добавлять дни к существующему плану без потери данных
            } else {
                // Создаем новый план
                $plan = NutritionPlan::create([
                    'athlete_id' => $request->athlete_id,
                    'trainer_id' => auth()->id(),
                    'month' => $request->month,
                    'year' => $request->year,
                    'title' => $request->title,
                    'description' => $request->description
                ]);
                \Log::info('План питания создан с ID: ' . $plan->id);
            }

            // Сохраняем данные по дням, если они есть и валидны
            if ($request->has('days') && is_array($request->days) && count($request->days) > 0) {
                \Log::info('=== НАЧИНАЕМ СОХРАНЕНИЕ ДНЕЙ ===');
                \Log::info('Количество дней в запросе: ' . count($request->days));
                \Log::info('Ожидаемый месяц: ' . $request->month);
                \Log::info('Ожидаемый год: ' . $request->year);
                
                $savedCount = 0;
                $skippedCount = 0;
                
                foreach ($request->days as $index => $dayData) {
                    \Log::info("=== Обрабатываем день #$index ===", ['dayData' => $dayData]);
                    
                    // Проверяем, что есть хотя бы одна заполненная ячейка
                    if (!empty($dayData['date']) && 
                        (isset($dayData['proteins']) || isset($dayData['fats']) || 
                         isset($dayData['carbs']) || isset($dayData['notes']))) {
                        
                        // Проверяем валидность даты - она должна принадлежать указанному месяцу
                        $dateParts = explode('-', $dayData['date']);
                        \Log::info("Дата разбита на части: " . implode(', ', $dateParts));
                        
                        if (count($dateParts) === 3) {
                            $dateYear = (int)$dateParts[0];
                            $dateMonth = (int)$dateParts[1];
                            $dateDay = (int)$dateParts[2];
                            
                            \Log::info("Парсинг даты: год=$dateYear, месяц=$dateMonth, день=$dateDay");
                            \Log::info("Сравнение: год запроса={$request->year}, месяц запроса={$request->month}");
                            
                            // Проверяем, что дата принадлежит указанному месяцу и году
                            if ($dateYear === (int)$request->year && $dateMonth === (int)$request->month) {
                                // Проверяем, что день не превышает максимальное количество дней в месяце
                                $daysInMonth = (int)date('t', mktime(0, 0, 0, $dateMonth, 1, $dateYear));
                                \Log::info("Дней в месяце: $daysInMonth, проверяемый день: $dateDay");
                                
                                if ($dateDay >= 1 && $dateDay <= $daysInMonth) {
                                    // Используем updateOrCreate для обновления существующих дней или создания новых
                                    $nutritionDay = NutritionDay::updateOrCreate(
                                        [
                                            'nutrition_plan_id' => $plan->id,
                                            'date' => $dayData['date']
                                        ],
                                        [
                                            'proteins' => $dayData['proteins'] ?? 0,
                                            'fats' => $dayData['fats'] ?? 0,
                                            'carbs' => $dayData['carbs'] ?? 0,
                                            'notes' => $dayData['notes'] ?? null
                                        ]
                                    );
                                    
                                    $savedCount++;
                                    \Log::info("✓ День питания сохранен/обновлен с ID: {$nutritionDay->id}, дата: {$dayData['date']}");
                                } else {
                                    $skippedCount++;
                                    \Log::warning("✗ Пропущен день с невалидной датой: {$dayData['date']} (день $dateDay не существует в месяце $dateMonth, максимум $daysInMonth)");
                                }
                            } else {
                                $skippedCount++;
                                \Log::warning("✗ Пропущен день с датой из другого месяца/года: {$dayData['date']} (ожидалось: {$request->year}-{$request->month})");
                            }
                        } else {
                            $skippedCount++;
                            \Log::warning("✗ Пропущен день с неправильным форматом даты: {$dayData['date']}");
                        }
                    } else {
                        \Log::info("Пропущен день (пустые данные):", ['dayData' => $dayData]);
                    }
                }
                
                \Log::info("=== ИТОГИ СОХРАНЕНИЯ ===");
                \Log::info("Сохранено дней: $savedCount");
                \Log::info("Пропущено дней: $skippedCount");
            } else {
                \Log::info('Нет данных по дням для сохранения');
            }

            DB::commit();
            \Log::info('Транзакция завершена успешно');
            
            // Возвращаем структуру совместимую с фронтендом
            $plan->load(['nutritionDays' => function($query) {
                $query->orderBy('date');
            }]);
            $plan->nutrition_days = $plan->nutritionDays;
            unset($plan->nutritionDays);
            
            return response()->json($plan);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Ошибка валидации: ' . json_encode($e->errors()));
            return response()->json(['error' => 'Ошибка валидации: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('=== ОШИБКА ПРИ СОЗДАНИИ ПЛАНА ПИТАНИЯ ===');
            \Log::error('Сообщение: ' . $e->getMessage());
            \Log::error('Файл: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Данные запроса: ' . json_encode($request->all()));
            
            return response()->json([
                'error' => 'Ошибка при создании плана питания: ' . $e->getMessage(),
                'details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Получить план питания с днями
     */
    public function show($id)
    {
        $plan = NutritionPlan::with(['nutritionDays' => function($query) {
            $query->orderBy('date');
        }, 'athlete'])
        ->findOrFail($id);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        // Преобразуем nutritionDays в nutrition_days для совместимости с фронтендом
        $plan->nutrition_days = $plan->nutritionDays;
        unset($plan->nutritionDays);

        return response()->json($plan);
    }

    /**
     * Обновить план питания
     */
    public function update(Request $request, $id)
    {
        $plan = NutritionPlan::findOrFail($id);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $plan->update($request->only(['title', 'description']));

        return response()->json($plan->load('nutritionDays'));
    }

    /**
     * Сохранить данные дня питания
     */
    public function saveDay(Request $request, $planId)
    {
        $plan = NutritionPlan::findOrFail($planId);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'proteins' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $nutritionDay = NutritionDay::updateOrCreate(
            [
                'nutrition_plan_id' => $planId,
                'date' => $request->date
            ],
            [
                'proteins' => $request->proteins ?? 0,
                'fats' => $request->fats ?? 0,
                'carbs' => $request->carbs ?? 0,
                'notes' => $request->notes
            ]
        );

        return response()->json($nutritionDay);
    }

    /**
     * Массовое сохранение дней питания
     */
    public function saveDays(Request $request, $planId)
    {
        $plan = NutritionPlan::findOrFail($planId);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $request->validate([
            'days' => 'required|array',
            'days.*.date' => 'required|date',
            'days.*.proteins' => 'nullable|numeric|min:0',
            'days.*.fats' => 'nullable|numeric|min:0',
            'days.*.carbs' => 'nullable|numeric|min:0',
            'days.*.notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->days as $dayData) {
                NutritionDay::updateOrCreate(
                    [
                        'nutrition_plan_id' => $planId,
                        'date' => $dayData['date']
                    ],
                    [
                        'proteins' => $dayData['proteins'] ?? 0,
                        'fats' => $dayData['fats'] ?? 0,
                        'carbs' => $dayData['carbs'] ?? 0,
                        'notes' => $dayData['notes'] ?? null
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Дни питания сохранены']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Ошибка при сохранении'], 500);
        }
    }

    /**
     * Удалить день питания
     */
    public function deleteDay($planId, $dayId)
    {
        $plan = NutritionPlan::findOrFail($planId);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $day = NutritionDay::where('nutrition_plan_id', $planId)
            ->where('id', $dayId)
            ->first();

        if ($day) {
            $day->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'День не найден'], 404);
    }

    /**
     * Удалить план питания
     */
    public function destroy($id)
    {
        $plan = NutritionPlan::findOrFail($id);

        // Проверяем доступ
        if ($plan->trainer_id !== auth()->id()) {
            return response()->json(['error' => 'Доступ запрещен'], 403);
        }

        $plan->delete();

        return response()->json(['success' => true]);
    }
}

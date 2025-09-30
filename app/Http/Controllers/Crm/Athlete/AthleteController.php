<?php

namespace App\Http\Controllers\Crm\Athlete;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Athlete\Athlete;
use App\Models\Athlete\ExerciseProgress;
use App\Models\Trainer\Workout;
use App\Models\Trainer\Progress;
use App\Models\Trainer\Nutrition;
use Illuminate\Http\Request;

class AthleteController extends BaseController
{
    public function dashboard()
    {
        $athlete = auth()->user();
        
        // Общее количество тренировок
        $totalWorkouts = $athlete->workouts()->count();
        
        // Запланированные тренировки
        $plannedWorkouts = $athlete->workouts()->where('status', 'planned')->count();
        
        // Завершенные тренировки
        $completedWorkouts = $athlete->workouts()->where('status', 'completed')->count();
        
        // Последняя тренировка или следующая
        $lastOrNextWorkout = $athlete->workouts()
            ->with('trainer')
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->first();
        
        // Ближайшие тренировки (следующие 7 дней)
        $upcomingWorkouts = $athlete->workouts()
            ->with('trainer')
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();
        
        // Все тренировки для календаря (расширенный диапазон)
        $monthWorkouts = $athlete->workouts()
            ->with('trainer')
            ->where('date', '>=', now()->startOfMonth()->subMonth()->toDateString())
            ->where('date', '<=', now()->endOfMonth()->addMonth()->toDateString())
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get() ?? collect();
        
        
        $recentWorkouts = $athlete->workouts()->with('trainer')->latest()->take(5)->get();
        
        // Данные для карточек измерений
        $lastMeasurement = $athlete->measurements()->latest('measurement_date')->first();
        $currentWeight = $lastMeasurement ? $lastMeasurement->weight : $athlete->current_weight;
        $totalMeasurements = $athlete->measurements()->count();
        
        // Рассчитываем ИМТ из последнего измерения
        $bmi = null;
        $bmiCategory = null;
        $bmiColor = null;
        
        if ($lastMeasurement && $lastMeasurement->weight && $athlete->current_height) {
            $heightInMeters = $athlete->current_height / 100;
            $bmi = round($lastMeasurement->weight / ($heightInMeters * $heightInMeters), 1);
        } elseif ($athlete->current_weight && $athlete->current_height) {
            $heightInMeters = $athlete->current_height / 100;
            $bmi = round($athlete->current_weight / ($heightInMeters * $heightInMeters), 1);
        }
        
        // Определяем категорию и цвет ИМТ
        if ($bmi) {
            if ($bmi < 18.5) {
                $bmiCategory = 'Недостаточный вес';
                $bmiColor = 'blue';
            } elseif ($bmi < 25) {
                $bmiCategory = 'Нормальный вес';
                $bmiColor = 'green';
            } elseif ($bmi < 30) {
                $bmiCategory = 'Избыточный вес';
                $bmiColor = 'yellow';
            } else {
                $bmiCategory = 'Ожирение';
                $bmiColor = 'red';
            }
        }
        
        
        return view('crm.athlete.dashboard', compact(
            'athlete', 
            'totalWorkouts', 
            'plannedWorkouts', 
            'completedWorkouts', 
            'lastOrNextWorkout', 
            'upcomingWorkouts', 
            'monthWorkouts', 
            'recentWorkouts', 
            'currentWeight', 
            'bmi', 
            'bmiCategory', 
            'bmiColor', 
            'totalMeasurements', 
            'lastMeasurement'
        ));
    }
    
    public function getWorkouts(Request $request)
    {
        $athlete = auth()->user();
        
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $workouts = $athlete->workouts()
            ->with('trainer')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'workouts' => $workouts->map(function($workout) {
                return [
                    'id' => $workout->id,
                    'title' => $workout->title,
                    'date' => $workout->date,
                    'time' => $workout->time,
                    'status' => $workout->status,
                    'trainer_name' => $workout->trainer->name ?? 'Тренер'
                ];
            })
        ]);
    }
    
    public function profile()
    {
        $athlete = auth()->user();
        return view('crm.athlete.profile', compact('athlete'));
    }
    
    public function updateProfile(Request $request)
    {
        $athlete = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $athlete->id,
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);
        
        $athlete->update($request->all());
        
        return redirect()->route('crm.athlete.profile')->with('success', 'Профиль обновлен');
    }
    
    public function workouts()
    {
        $athlete = auth()->user();
        
        try {
            // Получаем тренировки спортсмена с тренером
            $workouts = $athlete->workouts()
                ->with(['trainer'])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->paginate(10);
            
            // Загружаем упражнения для каждой тренировки отдельно
            $workouts->getCollection()->transform(function ($workout) {
                $workout->exercises = $workout->exercises()
                    ->select('exercises.id', 'exercises.name', 'exercises.description', 'exercises.category', 'exercises.equipment', 'exercises.muscle_groups', 'exercises.instructions', 'exercises.video_url', 'exercises.fields_config', 'exercises.image_url', 'workout_exercise.*')
                    ->get();
                return $workout;
            });
            
            // Обрабатываем null значения в упражнениях
            $workouts->getCollection()->transform(function ($workout) {
                if ($workout->exercises) {
                    foreach ($workout->exercises as $exercise) {
                        if ($exercise->pivot) {
                            foreach (['sets', 'reps', 'weight', 'rest', 'time', 'distance', 'tempo', 'notes'] as $field) {
                                if (isset($exercise->pivot->$field) && 
                                    ($exercise->pivot->$field === null || 
                                     $exercise->pivot->$field === 'null')) {
                                    $exercise->pivot->$field = '';
                                }
                            }
                        }
                    }
                }
                return $workout;
            });
            
            // Подсчитываем статистику
            $workoutsCount = $athlete->workouts()->count();
            $completedCount = $athlete->workouts()->where('status', 'completed')->count();
            $inProgressCount = $athlete->workouts()->where('status', 'in_progress')->count();
            $plannedCount = $athlete->workouts()->where('status', 'planned')->count();
            
        } catch (\Exception $e) {
            // Если есть ошибки, используем пустые данные
            $workouts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $workoutsCount = 0;
            $completedCount = 0;
            $inProgressCount = 0;
            $plannedCount = 0;
        }
        
        return view('crm.athlete.workouts', compact('workouts', 'workoutsCount', 'completedCount', 'inProgressCount', 'plannedCount'));
    }
    
    public function nutrition()
    {
        $athlete = auth()->user();
        $nutrition = $athlete->nutrition()->paginate(10);
        
        return view('crm.athlete.nutrition', compact('nutrition'));
    }
    
    public function settings()
    {
        $athlete = auth()->user();
        return view('crm.athlete.settings', compact('athlete'));
    }

    public function measurements()
    {
        $athlete = auth()->user();
        
        // Отладочная информация
        \Log::info('AthleteController::measurements', [
            'athlete_id' => $athlete->id,
            'athlete_name' => $athlete->name,
            'athlete_email' => $athlete->email,
            'measurements_count' => $athlete->measurements()->count()
        ]);
        
        try {
            // Получаем ВСЕ измерения спортсмена для клиентской пагинации
            $allMeasurements = $athlete->measurements()
                ->orderBy('measurement_date', 'desc')
                ->get();
            
            // Создаем объект пагинации для совместимости с шаблоном
            $measurements = new \Illuminate\Pagination\LengthAwarePaginator(
                $allMeasurements,
                $allMeasurements->count(),
                6, // itemsPerPage
                1, // currentPage
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            // Получаем последние измерения для статистики
            $lastMeasurement = $athlete->measurements()
                ->orderBy('measurement_date', 'desc')
                ->first();
            
            // Подсчитываем общее количество измерений
            $totalMeasurements = $athlete->measurements()->count();
            
            \Log::info('Measurements loaded', [
                'total_measurements' => $totalMeasurements,
                'last_measurement_date' => $lastMeasurement ? $lastMeasurement->measurement_date : null,
                'measurements_on_page' => $measurements->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading measurements', [
                'error' => $e->getMessage(),
                'athlete_id' => $athlete->id
            ]);
            
            // Если есть ошибки, используем пустые данные
            $measurements = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $lastMeasurement = null;
            $totalMeasurements = 0;
        }
        
        return view('crm.athlete.measurements', compact('athlete', 'measurements', 'lastMeasurement', 'totalMeasurements'));
    }

    /**
     * Создание нового измерения спортсменом
     */
    public function storeMeasurement(Request $request)
    {
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'resting_heart_rate' => 'nullable|numeric|min:0',
            'blood_pressure_systolic' => 'nullable|numeric|min:0',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        $athlete = auth()->user();

        try {
            // Добавляем рост из профиля спортсмена
            $data = $request->all();
            $data['height'] = $athlete->height;
            
            $measurement = $athlete->measurements()->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Измерение успешно добавлено',
                'measurement' => $measurement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении измерения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение конкретного измерения для редактирования
     */
    public function getMeasurement($id)
    {
        $athlete = auth()->user();
        
        $measurement = $athlete->measurements()->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'measurement' => $measurement
        ]);
    }

    /**
     * Обновление измерения спортсменом
     */
    public function updateMeasurement(Request $request, $id)
    {
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'resting_heart_rate' => 'nullable|numeric|min:0',
            'blood_pressure_systolic' => 'nullable|numeric|min:0',
            'blood_pressure_diastolic' => 'nullable|numeric|min:0',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        $athlete = auth()->user();
        
        $measurement = $athlete->measurements()->findOrFail($id);

        try {
            // Добавляем рост из профиля спортсмена
            $data = $request->all();
            $data['height'] = $athlete->height;
            
            $measurement->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Измерение успешно обновлено',
                'measurement' => $measurement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении измерения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление измерения спортсменом
     */
    public function deleteMeasurement($id)
    {
        $athlete = auth()->user();
        
        $measurement = $athlete->measurements()->findOrFail($id);

        try {
            $measurement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Измерение успешно удалено'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении измерения: ' . $e->getMessage()
            ], 500);
        }
    }

    public function progress()
    {
        $athlete = auth()->user();
        
        try {
            // Получаем прогресс спортсмена
            $progressData = $athlete->progress()
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Получаем последние тренировки
            $recentWorkouts = collect();
            if (method_exists($athlete, 'workouts')) {
                $recentWorkouts = $athlete->workouts()
                    ->with('trainer')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
            }
            
            // Получаем измерения, сортируем по measurement_date для корректного отображения графиков
            $measurements = collect();
            if (method_exists($athlete, 'measurements')) {
                $measurements = $athlete->measurements()
                    ->orderBy('measurement_date', 'asc') // Сортируем по дате измерения для графиков
                    ->get();
            }
            
        } catch (\Exception $e) {
            // Если есть ошибки, используем пустые коллекции
            $progressData = collect();
            $recentWorkouts = collect();
            $measurements = collect();
        }
        
        return view('crm.athlete.progress', compact('athlete', 'progressData', 'recentWorkouts', 'measurements'));
    }

    public function updateExerciseProgress(Request $request)
    {
        try {
            $athlete = auth()->user();
            
            $request->validate([
                'workout_id' => 'required|exists:workouts,id',
                'exercises' => 'required|array'
            ]);


            foreach ($request->exercises as $exerciseData) {
                // Если статус null, удаляем запись из базы данных
                if ($exerciseData['status'] === null) {
                    ExerciseProgress::where('workout_id', $request->workout_id)
                        ->where('exercise_id', $exerciseData['exercise_id'])
                        ->where('athlete_id', $athlete->id)
                        ->delete();
                } else {
                    // Иначе создаем или обновляем запись
                    $progress = ExerciseProgress::updateOrCreate(
                        [
                            'workout_id' => $request->workout_id,
                            'exercise_id' => $exerciseData['exercise_id'],
                            'athlete_id' => $athlete->id
                        ],
                        [
                            'status' => $exerciseData['status'] ?? 'not_done',
                            'athlete_comment' => $exerciseData['athlete_comment'] ?? null,
                            'sets_data' => $exerciseData['sets_data'] ?? null,
                            'completed_at' => ($exerciseData['status'] ?? 'not_done') === 'completed' ? now() : null
                        ]
                    );
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Прогресс обновлен'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating exercise progress', [
                'error' => $e->getMessage(),
                'athlete_id' => auth()->user()->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExerciseProgress(Request $request)
    {
        try {
            $athlete = auth()->user();
            
            $request->validate([
                'workout_id' => 'required|exists:workouts,id'
            ]);

            $progress = ExerciseProgress::where('workout_id', $request->workout_id)
                ->where('athlete_id', $athlete->id)
                ->get()
                ->keyBy('exercise_id');

            return response()->json([
                'success' => true,
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить статус тренировки спортсменом
     */
    public function updateWorkoutStatus(Request $request, $workoutId)
    {
        try {
            $athlete = auth()->user();
            
            $request->validate([
                'status' => 'required|in:planned,completed,cancelled'
            ]);

            // Проверяем, что тренировка принадлежит спортсмену
            $workout = $athlete->workouts()->findOrFail($workoutId);
            
            // Обновляем статус тренировки
            $workout->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Статус тренировки обновлен',
                'workout' => $workout
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating workout status', [
                'error' => $e->getMessage(),
                'athlete_id' => auth()->user()->id ?? null,
                'workout_id' => $workoutId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновить настройки спортсмена
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'language_code' => 'required|string|exists:languages,code',
            'currency_code' => 'required|string|exists:currencies,code',
            'timezone' => 'required|string|max:50',
        ]);

        $athlete = auth()->user();
        $athlete->update($request->only([
            'language_code', 'currency_code', 'timezone'
        ]));

        return redirect()->back()->with('success', 'Настройки языка и валюты обновлены');
    }
}

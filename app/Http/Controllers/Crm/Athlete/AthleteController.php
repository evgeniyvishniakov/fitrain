<?php

namespace App\Http\Controllers\Crm\Athlete;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Athlete\Athlete;
use App\Models\Athlete\ExerciseProgress;
use App\Models\Trainer\Workout;
use App\Models\Trainer\Exercise;
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
        
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.dashboard' : 'crm.athlete.dashboard';
        
        return view($view, compact(
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
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.profile' : 'crm.athlete.profile';
        
        return view($view, compact('athlete'));
    }
    
    public function updateProfile(Request $request)
    {
        $athlete = auth()->user();
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $athlete->id,
                'phone' => 'nullable|string|max:20',
                'age' => 'nullable|integer|min:1|max:120',
                'height' => 'nullable|numeric|min:50|max:250',
                'birth_date' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female',
                'sport_level' => 'nullable|in:beginner,intermediate,advanced',
                'goals' => 'nullable|array',
                'goals.*' => 'in:weight_loss,muscle_gain,muscle_tone,endurance,strength,flexibility',
            ]);
            
            $data = $request->all();
            
            // Обработка целей
            if (isset($data['goals'])) {
                $data['goals'] = array_values(array_filter($data['goals']));
            } else {
                $data['goals'] = [];
            }
            
            $athlete->update($data);
            
            // Если это AJAX запрос, возвращаем JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Профиль успешно обновлен'
                ]);
            }
            
            return redirect()->route('crm.athlete.settings')->with('success', 'Профиль успешно обновлен');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Если это AJAX запрос, возвращаем JSON с ошибками валидации
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $e->errors()
                ], 422);
            }
            
            throw $e;
        } catch (\Exception $e) {
            // Если это AJAX запрос, возвращаем JSON с ошибкой
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при сохранении профиля'
                ], 500);
            }
            
            throw $e;
        }
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
                    ->orderBy('workout_exercise.order_index', 'asc')
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
            $remainingCount = $athlete->total_sessions - $athlete->used_sessions;
            
        } catch (\Exception $e) {
            // Если есть ошибки, используем пустые данные
            $workouts = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $workoutsCount = 0;
            $completedCount = 0;
            $inProgressCount = 0;
            $plannedCount = 0;
            $remainingCount = 0;
        }
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.workouts' : 'crm.athlete.workouts';
        
        return view($view, compact('workouts', 'workoutsCount', 'completedCount', 'inProgressCount', 'plannedCount', 'remainingCount'));
    }
    
    public function nutrition()
    {
        $athlete = auth()->user();
        $nutrition = $athlete->nutrition()->paginate(10);
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.nutrition' : 'crm.athlete.nutrition';
        
        return view($view, compact('nutrition'));
    }
    
    public function settings()
    {
        $athlete = auth()->user();
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.settings' : 'crm.athlete.settings';
        
        return view($view, compact('athlete'));
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
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.measurements' : 'crm.athlete.measurements';
        
        return view($view, compact('athlete', 'measurements', 'lastMeasurement', 'totalMeasurements'));
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
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.progress' : 'crm.athlete.progress';
        
        return view($view, compact('athlete', 'progressData', 'recentWorkouts', 'measurements'));
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
        try {
            $athlete = auth()->user();
            
            // Если это запрос на обновление профиля (есть поля профиля)
            if ($request->has('name') || $request->has('email')) {
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $athlete->id,
                    'phone' => 'nullable|string|max:20',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|in:male,female',
                    'height' => 'nullable|numeric|min:100|max:250',
                    'weight' => 'nullable|numeric|min:30|max:300',
                ]);
                
                $athlete->update($validated);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => __('common.profile_updated_successfully'),
                        'user' => $athlete->fresh()
                    ]);
                }
                
                return redirect()->back()->with('success', __('common.profile_updated_successfully'));
            }
            
            // Если это запрос на обновление настроек языка и валюты
            $validated = $request->validate([
                'language_code' => 'required|string|exists:languages,code',
                'currency_code' => 'required|string|exists:currencies,code',
            ]);

            $athlete->update($validated);

            // Обновляем локаль в сессии для немедленного применения
            if ($request->has('language_code')) {
                session(['locale' => $request->get('language_code')]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Настройки языка и валюты обновлены'
                ]);
            }

            return redirect()->back()->with('success', 'Настройки языка и валюты обновлены');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при сохранении: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Ошибка при сохранении: ' . $e->getMessage());
        }
    }

    /**
     * Страница упражнений для спортсмена
     */
    public function exercises()
    {
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.exercises' : 'crm.athlete.exercises';
        
        return view($view);
    }

    /**
     * Получить упражнения из тренировок спортсмена (API)
     */
    public function getExercisesFromWorkouts()
    {
        try {
            $athlete = auth()->user();
            
            // Получаем все упражнения из тренировок спортсмена через промежуточную таблицу
            $exercises = Exercise::whereHas('workouts', function($query) use ($athlete) {
                $query->where('athlete_id', $athlete->id);
            })
            ->with(['creator'])
            ->get()
            ->unique('id')
            ->sortBy('name')
            ->values();
            
            return response()->json([
                'success' => true,
                'exercises' => $exercises
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка загрузки упражнений спортсмена: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить историю упражнения (последние данные для автозаполнения)
     */
    public function getExerciseHistory($exerciseId, Request $request)
    {
        try {
            // Для тренера берем athlete_id из запроса, для self-athlete - из auth
            $athleteId = $request->get('athlete_id');
            if (!$athleteId) {
                $athlete = auth()->user();
                $athleteId = $athlete->id;
            }
            
            \Log::info("Запрос истории упражнения {$exerciseId} для athlete {$athleteId}");
            
            // Находим последнюю тренировку с этим упражнением (только ПРОШЛЫЕ тренировки)
            $lastWorkoutId = \DB::table('workout_exercise')
                ->join('workouts', 'workout_exercise.workout_id', '=', 'workouts.id')
                ->where('workouts.athlete_id', $athleteId)
                ->where('workout_exercise.exercise_id', $exerciseId)
                ->where('workouts.date', '<', now()->toDateString()) // Только ПРОШЛЫЕ даты
                ->orderBy('workouts.date', 'desc')
                ->orderBy('workouts.created_at', 'desc')
                ->select('workouts.id')
                ->first();
            
            \Log::info("lastWorkoutId: " . ($lastWorkoutId ? $lastWorkoutId->id : 'null'));
            
            if (!$lastWorkoutId) {
                \Log::info("Истории нет для упражнения {$exerciseId} и athlete {$athleteId}");
                return response()->json([
                    'success' => true,
                    'has_history' => false
                ]);
            }
            
            // Загружаем тренировку
            $lastWorkout = \App\Models\Trainer\Workout::find($lastWorkoutId->id);
            
            // Загружаем данные упражнения из промежуточной таблицы
            $exerciseData = \DB::table('workout_exercise')
                ->where('workout_id', $lastWorkout->id)
                ->where('exercise_id', $exerciseId)
                ->first();
            
            // Получаем конфигурацию полей упражнения
            $exercise = \App\Models\Trainer\Exercise::find($exerciseId);
            $fieldsConfig = $exercise && $exercise->fields_config 
                ? (is_array($exercise->fields_config) ? $exercise->fields_config : json_decode($exercise->fields_config, true))
                : [];
            
            if (!$exerciseData) {
                return response()->json([
                    'success' => true,
                    'has_history' => false
                ]);
            }
            
            // Получаем плановые данные (из промежуточной таблицы)
            $plan = [
                'weight' => $exerciseData->weight ?? 0,
                'reps' => $exerciseData->reps ?? 0,
                'sets' => $exerciseData->sets ?? 0,
                'rest' => $exerciseData->rest ?? 0,
                'time' => $exerciseData->time ?? 0,
                'distance' => $exerciseData->distance ?? 0,
                'tempo' => $exerciseData->tempo ?? null,
            ];
            
            // Получаем фактические данные (из progress если есть)
            $progress = \App\Models\Athlete\ExerciseProgress::where('workout_id', $lastWorkout->id)
                ->where('exercise_id', $exerciseId)
                ->where('athlete_id', $athleteId)
                ->first();
            
            $fact = null;
            $setsDetails = null;
            $exerciseStatus = null;
            
            if ($progress) {
                $exerciseStatus = $progress->status; // completed, partial, not_done
                
                // Если есть детальные данные подходов
                if ($progress->sets_data) {
                    $setsData = is_array($progress->sets_data) ? $progress->sets_data : json_decode($progress->sets_data, true);
                    
                    if (!empty($setsData) && is_array($setsData)) {
                        $avgWeight = collect($setsData)->avg('weight') ?? 0;
                        $avgReps = collect($setsData)->avg('reps') ?? 0;
                        
                        // Определяем процент выполнения на основе плана
                        $plannedSets = $exerciseData->sets ?? count($setsData);
                        $actualSets = count($setsData);
                        $completedPercentage = $plannedSets > 0 ? round(($actualSets / $plannedSets) * 100) : 100;
                        
                        $fact = [
                            'weight' => round($avgWeight, 1),
                            'reps' => round($avgReps),
                            'sets' => $actualSets,
                            'completed_percentage' => $completedPercentage
                        ];
                        
                        // Передаем детали каждого подхода
                        $setsDetails = $setsData;
                    }
                }
                // Если нет детальных данных, но упражнение отмечено как выполненное - используем план
                elseif ($exerciseStatus === 'completed' || $exerciseStatus === 'partial') {
                    $fact = [
                        'weight' => $exerciseData->weight ?? 0,
                        'reps' => $exerciseData->reps ?? 0,
                        'sets' => $exerciseData->sets ?? 0,
                        'time' => $exerciseData->time ?? 0,
                        'distance' => $exerciseData->distance ?? 0,
                        'completed_percentage' => $exerciseStatus === 'completed' ? 100 : 50
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'has_history' => true,
                'workout_date' => $lastWorkout->date,
                'workout_title' => $lastWorkout->title,
                'plan' => $plan,
                'fact' => $fact,
                'sets_details' => $setsDetails,
                'exercise_status' => $exerciseStatus,
                'fields_config' => $fieldsConfig,
                'is_completed' => $lastWorkout->status === 'completed'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка получения истории упражнения: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }
}

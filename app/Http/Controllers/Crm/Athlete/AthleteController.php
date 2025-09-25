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
        $workouts = $athlete->workouts()->count();
        $progress = $athlete->progress()->count();
        $recentWorkouts = $athlete->workouts()->with('trainer')->latest()->take(5)->get();
        
        return view('crm.athlete.dashboard', compact('athlete', 'workouts', 'progress', 'recentWorkouts'));
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
            // Получаем тренировки спортсмена с тренером и упражнениями
            $workouts = $athlete->workouts()
                ->with(['trainer', 'exercises' => function($query) {
                    $query->select('exercises.*', 'workout_exercise.*');
                }])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->paginate(10);
            
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
                ExerciseProgress::updateOrCreate(
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
            
            return response()->json([
                'success' => true,
                'message' => 'Прогресс обновлен'
            ]);
        } catch (\Exception $e) {
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
}

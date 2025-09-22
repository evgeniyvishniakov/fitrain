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
                ->with(['trainer', 'exercises'])
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

    public function progress()
    {
        $athlete = auth()->user();
        
        try {
            // Получаем прогресс спортсмена
            $progressData = $athlete->progress()
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Получаем последние тренировки (если метод существует)
            $recentWorkouts = collect();
            if (method_exists($athlete, 'athleteWorkouts')) {
                $recentWorkouts = $athlete->athleteWorkouts()
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get();
            }
            
            // Получаем измерения (если метод существует)
            $measurements = collect();
            if (method_exists($athlete, 'measurements')) {
                $measurements = $athlete->measurements()
                    ->orderBy('created_at', 'desc')
                    ->take(20)
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

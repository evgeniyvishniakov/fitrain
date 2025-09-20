<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Workout;
use App\Models\Shared\User;
use Illuminate\Http\Request;

class WorkoutController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            $workouts = $user->trainerWorkouts()->with(['athlete', 'exercises' => function($query) {
                $query->select('exercises.*', 'workout_exercise.*');
            }])->latest()->get();
            $athletes = $user->athletes()->get();
        } else {
            $workouts = $user->workouts()->with(['trainer', 'exercises' => function($query) {
                $query->select('exercises.*', 'workout_exercise.*');
            }])->latest()->get();
            $athletes = collect();
        }
        
        return view('crm.trainer.workouts.index', compact('workouts', 'athletes'));
    }
    
    
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('trainer')) {
            abort(403, 'Доступ запрещен');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'athlete_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:planned,completed,cancelled',
        ]);
        
        $workout = Workout::create([
            'title' => $request->title,
            'description' => $request->description,
            'trainer_id' => auth()->id(),
            'athlete_id' => $request->athlete_id,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'status' => $request->status ?? 'planned',
            'is_counted' => false, // По умолчанию не засчитана
        ]);

        // Сохраняем упражнения через связь many-to-many
        if ($request->exercises && is_array($request->exercises)) {
            foreach ($request->exercises as $exerciseData) {
                $workout->exercises()->attach($exerciseData['exercise_id'], [
                    'sets' => $exerciseData['sets'] ?? 3,
                    'reps' => $exerciseData['reps'] ?? 12,
                    'weight' => $exerciseData['weight'] ?? 0,
                    'rest' => $exerciseData['rest'] ?? 60,
                    'time' => $exerciseData['time'] ?? 0,
                    'distance' => $exerciseData['distance'] ?? 0,
                    'tempo' => $exerciseData['tempo'] ?? null,
                    'notes' => $exerciseData['notes'] ?? null,
                ]);
            }
        }

        // Если тренировка завершена, списываем тренировку
        if ($request->status === 'completed') {
            $this->countWorkout($workout);
        }
        
        // Загружаем связанные данные для фронтенда
        $workout->load(['athlete', 'trainer']);
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка создана',
            'workout' => $workout
        ]);
    }
    
    public function show($id)
    {
        $workout = Workout::with(['trainer', 'athlete'])->findOrFail($id);
        
        // Проверяем доступ
        if (auth()->user()->hasRole('athlete') && $workout->athlete_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if (auth()->user()->hasRole('trainer') && $workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        return view('crm.trainer.workouts.show', compact('workout'));
    }
    
    
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('trainer')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        if ($workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'athlete_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,completed,cancelled',
        ]);
        
        
        $oldStatus = $workout->status;
        $workout->update([
            'title' => $request->title,
            'description' => $request->description,
            'athlete_id' => $request->athlete_id,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'status' => $request->status,
        ]);

        // Обновляем упражнения
        if ($request->exercises && is_array($request->exercises)) {
            // Проверяем, что все упражнения существуют
            $exerciseIds = collect($request->exercises)->pluck('exercise_id')->filter();
            $existingExercises = \App\Models\Trainer\Exercise::whereIn('id', $exerciseIds)->pluck('id')->toArray();
            $missingExercises = $exerciseIds->diff($existingExercises);
            
            if ($missingExercises->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Некоторые упражнения не найдены в базе данных. ID: ' . $missingExercises->implode(', ')
                ], 400);
            }
            
            // Только если все упражнения существуют, обновляем
            $workout->exercises()->detach(); // Удаляем все старые связи
            foreach ($request->exercises as $exerciseData) {
                if (!isset($exerciseData['exercise_id'])) {
                    continue;
                }
                $workout->exercises()->attach($exerciseData['exercise_id'], [
                    'sets' => $exerciseData['sets'] ?? 3,
                    'reps' => $exerciseData['reps'] ?? 12,
                    'weight' => $exerciseData['weight'] ?? 0,
                    'rest' => $exerciseData['rest'] ?? 60,
                    'time' => $exerciseData['time'] ?? 0,
                    'distance' => $exerciseData['distance'] ?? 0,
                    'tempo' => $exerciseData['tempo'] ?? null,
                    'notes' => $exerciseData['notes'] ?? null,
                ]);
            }
        } else {
            // Если нет упражнений, удаляем все
            $workout->exercises()->detach();
        }

        // Обрабатываем изменение статуса
        if ($oldStatus !== $request->status) {
            if ($request->status === 'completed' && !$workout->is_counted) {
                // Статус изменился на "завершена" - списываем тренировку
                $this->countWorkout($workout);
            } elseif ($oldStatus === 'completed' && $request->status !== 'completed' && $workout->is_counted) {
                // Статус изменился с "завершена" на другой - возвращаем тренировку
                $this->uncountWorkout($workout);
            }
        }
        
        // Загружаем связанные данные для фронтенда
        $workout->load(['athlete', 'trainer']);
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка обновлена',
            'workout' => $workout
        ]);
    }
    
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('trainer')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        if ($workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        // Если тренировка была засчитана, возвращаем тренировку спортсмену
        if ($workout->is_counted) {
            $this->uncountWorkout($workout);
        }
        
        $workout->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка удалена'
        ]);
    }

    /**
     * Засчитать тренировку (списать у спортсмена)
     */
    private function countWorkout(Workout $workout)
    {
        $athlete = User::find($workout->athlete_id);
        
        if ($athlete && $athlete->used_sessions < $athlete->total_sessions) {
            $athlete->increment('used_sessions');
            $workout->update(['is_counted' => true]);
        }
    }

    /**
     * Отменить зачет тренировки (вернуть спортсмену)
     */
    private function uncountWorkout(Workout $workout)
    {
        $athlete = User::find($workout->athlete_id);
        
        if ($athlete && $athlete->used_sessions > 0) {
            $athlete->decrement('used_sessions');
            $workout->update(['is_counted' => false]);
        }
    }
}

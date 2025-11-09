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
                $query->select('exercises.id', 'exercises.name', 'exercises.description', 'exercises.category', 'exercises.equipment', 'exercises.muscle_groups', 'exercises.instructions', 'exercises.video_url', 'exercises.fields_config', 'exercises.image_url', 'exercises.image_url_2', 'exercises.image_url_female', 'exercises.image_url_female_2', 'workout_exercise.*')
                    ->orderBy('workout_exercise.order_index', 'asc');
            }])->latest()->paginate(10);
            $athletes = $user->athletes()->get();
            
        } elseif ($user->hasRole('self-athlete')) {
            // Self-Athlete видит только свои тренировки
            $workouts = Workout::where('athlete_id', $user->id)->with(['exercises' => function($query) {
                $query->select('exercises.id', 'exercises.name', 'exercises.description', 'exercises.category', 'exercises.equipment', 'exercises.muscle_groups', 'exercises.instructions', 'exercises.video_url', 'exercises.fields_config', 'exercises.image_url', 'exercises.image_url_2', 'exercises.image_url_female', 'exercises.image_url_female_2', 'workout_exercise.*')
                    ->orderBy('workout_exercise.order_index', 'asc');
            }])->latest()->paginate(10);
            $athletes = [$user]; // Self-Athlete сам себе спортсмен
            
        } else {
            $workouts = $user->workouts()->with(['trainer', 'exercises' => function($query) {
                $query->select('exercises.id', 'exercises.name', 'exercises.description', 'exercises.category', 'exercises.equipment', 'exercises.muscle_groups', 'exercises.instructions', 'exercises.video_url', 'exercises.fields_config', 'exercises.image_url', 'exercises.image_url_2', 'exercises.image_url_female', 'exercises.image_url_female_2', 'workout_exercise.*')
                    ->orderBy('workout_exercise.order_index', 'asc');
            }])->latest()->paginate(10);
            $athletes = [];
        }
        
        // Обрабатываем null значения в упражнениях и применяем логику выбора изображений по полу
        $controller = $this;
        $workouts->getCollection()->transform(function ($workout) use ($controller) {
            if ($workout->exercises) {
                // Получаем пол спортсмена
                $athlete = $workout->athlete ?? auth()->user();
                $athleteGender = $athlete->gender ?? null;
                
                foreach ($workout->exercises as $exercise) {
                    // Применяем логику выбора изображений в зависимости от пола
                    if ($athleteGender === 'female') {
                        // Для девушек: используем женские изображения, если они есть, иначе обычные
                        if ($exercise->image_url_female) {
                            $exercise->image_url = $exercise->image_url_female;
                        }
                        if ($exercise->image_url_female_2) {
                            $exercise->image_url_2 = $exercise->image_url_female_2;
                        }
                    }
                    // Для мужчин всегда используются обычные изображения (image_url, image_url_2)
                    
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
            return $controller->prepareWorkoutForFrontend($workout);
        });
        
        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.workouts' : 'crm.trainer.workouts.index';
        
        return view($view, compact('workouts', 'athletes'));
    }
    
    
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('self-athlete')) {
            abort(403, 'Доступ запрещен');
        }
        
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:planned,completed,cancelled',
        ];
        
        // Для тренера athlete_id обязателен, для Self-Athlete автоматически
        if (auth()->user()->hasRole('trainer')) {
            $rules['athlete_id'] = 'required|exists:users,id';
        }
        
        $request->validate($rules);
        
        $workout = Workout::create([
            'title' => $request->title,
            'description' => $request->description,
            'trainer_id' => auth()->id(),
            'athlete_id' => auth()->user()->hasRole('self-athlete') ? auth()->id() : $request->athlete_id,
            'date' => $request->date,
            'time' => $request->time,
            'duration' => $request->duration,
            'status' => $request->status ?? 'planned',
            'is_counted' => false, // По умолчанию не засчитана
        ]);

        // Сохраняем упражнения через связь many-to-many
        if ($request->exercises && is_array($request->exercises)) {
            foreach ($request->exercises as $index => $exerciseData) {
                $workout->exercises()->attach($exerciseData['exercise_id'], [
                    'sets' => $exerciseData['sets'] ?? 3,
                    'reps' => $exerciseData['reps'] ?? 12,
                    'weight' => $exerciseData['weight'] ?? 0,
                    'rest' => $exerciseData['rest'] ?? 60,
                    'time' => $exerciseData['time'] ?? 0,
                    'distance' => $exerciseData['distance'] ?? 0,
                    'tempo' => $exerciseData['tempo'] ?? null,
                    'notes' => $exerciseData['notes'] ?? null,
                    'order_index' => $index,
                ]);
            }
        }

        // Если тренировка завершена, списываем тренировку
        if ($request->status === 'completed') {
            $this->countWorkout($workout);
        }
        
        // Загружаем связанные данные для фронтенда
        $workout->load(['athlete', 'trainer', 'exercises' => function($query) {
            $query->select('exercises.*', 'workout_exercise.*')
                ->orderBy('workout_exercise.order_index', 'asc');
        }]);
        
        // Добавляем прогресс упражнений для новой тренировки
        if ($workout->exercises) {
            foreach ($workout->exercises as $exercise) {
                $exerciseId = $exercise->exercise_id ?? $exercise->id;
                $progress = \App\Models\Athlete\ExerciseProgress::where('workout_id', $workout->id)
                    ->where('exercise_id', $exerciseId)
                    ->where('athlete_id', $workout->athlete_id)
                    ->first();
                
                if ($progress) {
                    $exercise->progress = [
                        'status' => $progress->status,
                        'athlete_comment' => $progress->athlete_comment,
                        'sets_data' => $progress->sets_data,
                        'completed_at' => $progress->completed_at
                    ];
                } else {
                    $exercise->progress = [
                        'status' => null,
                        'athlete_comment' => null,
                        'sets_data' => null,
                        'completed_at' => null
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка создана',
            'workout' => $this->formatWorkoutForResponse($workout)
        ]);
    }
    
    public function show($id)
    {
        $workout = Workout::with(['trainer', 'athlete'])->findOrFail($id);
        
        // Проверяем доступ
        if (auth()->user()->hasRole('athlete') && $workout->athlete_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        // Для тренера проверяем trainer_id, для Self-Athlete проверяем athlete_id
        if (auth()->user()->hasRole('trainer') && $workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        } elseif (auth()->user()->hasRole('self-athlete') && $workout->athlete_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        return view('crm.trainer.workouts.show', compact('workout'));
    }
    
    
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('self-athlete')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        // Для тренера проверяем trainer_id, для Self-Athlete проверяем athlete_id
        if (auth()->user()->hasRole('trainer') && $workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        } elseif (auth()->user()->hasRole('self-athlete') && $workout->athlete_id !== auth()->id()) {
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

        // Обновляем упражнения только если они переданы в запросе
        if ($request->has('exercises')) {
            if (is_array($request->exercises) && count($request->exercises) > 0) {
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
                foreach ($request->exercises as $index => $exerciseData) {
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
                        'order_index' => $index,
                    ]);
                }
            } else {
                // Если массив упражнений пустой, удаляем все
                $workout->exercises()->detach();
            }
        }
        // Если exercises не передан в запросе - не трогаем упражнения вообще

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
        $workout->load(['athlete', 'trainer', 'exercises' => function($query) {
            $query->select('exercises.*', 'workout_exercise.*')
                ->orderBy('workout_exercise.order_index', 'asc');
        }]);
        
        // Добавляем прогресс упражнений для обновленной тренировки
        if ($workout->exercises) {
            foreach ($workout->exercises as $exercise) {
                $exerciseId = $exercise->exercise_id ?? $exercise->id;
                $progress = \App\Models\Athlete\ExerciseProgress::where('workout_id', $workout->id)
                    ->where('exercise_id', $exerciseId)
                    ->where('athlete_id', $workout->athlete_id)
                    ->first();
                
                if ($progress) {
                    $exercise->progress = [
                        'status' => $progress->status,
                        'athlete_comment' => $progress->athlete_comment,
                        'sets_data' => $progress->sets_data,
                        'completed_at' => $progress->completed_at
                    ];
                } else {
                    $exercise->progress = [
                        'status' => null,
                        'athlete_comment' => null,
                        'sets_data' => null,
                        'completed_at' => null
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка обновлена',
            'workout' => $this->formatWorkoutForResponse($workout)
        ]);
    }

    public function duplicate($id)
    {
        if (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('self-athlete')) {
            abort(403, 'Доступ запрещен');
        }

        $originalWorkout = Workout::with(['exercises' => function ($query) {
            $query->select('exercises.*', 'workout_exercise.*')
                ->orderBy('workout_exercise.order_index', 'asc');
        }])->findOrFail($id);

        if (auth()->user()->hasRole('trainer') && $originalWorkout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        } elseif (auth()->user()->hasRole('self-athlete') && $originalWorkout->athlete_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }

        return response()->json([
            'success' => true,
            'workout' => $this->formatWorkoutForResponse($originalWorkout)
        ]);
    }
    
    public function destroy($id)
    {
        if (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('self-athlete')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        // Для тренера проверяем trainer_id, для Self-Athlete проверяем athlete_id
        if (auth()->user()->hasRole('trainer') && $workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        } elseif (auth()->user()->hasRole('self-athlete') && $workout->athlete_id !== auth()->id()) {
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
     * Обновить только статус тренировки
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->hasRole('trainer') && !auth()->user()->hasRole('self-athlete')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        // Для тренера проверяем trainer_id, для Self-Athlete проверяем athlete_id
        if (auth()->user()->hasRole('trainer') && $workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        } elseif (auth()->user()->hasRole('self-athlete') && $workout->athlete_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $request->validate([
            'status' => 'required|in:planned,completed,cancelled'
        ]);
        
        $oldStatus = $workout->status;
        $workout->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

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
        
        return response()->json([
            'success' => true,
            'message' => 'Статус тренировки обновлен',
            'workout' => $this->formatWorkoutForResponse($workout)
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

    protected function prepareWorkoutForFrontend(Workout $workout): Workout
    {
        $workout->formatted_date = optional($workout->date)->format('d.m.Y');
        $workout->date_for_input = optional($workout->date)->format('Y-m-d');
        $workout->date_iso = optional($workout->date)->toIso8601String();

        return $workout;
    }

    protected function formatWorkoutForResponse(Workout $workout): array
    {
        return $this->prepareWorkoutForFrontend($workout)->toArray();
    }
}

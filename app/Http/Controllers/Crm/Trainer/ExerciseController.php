<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Exercise;
use App\Models\UserExerciseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExerciseController extends BaseController
{
    public function index(Request $request)
    {
        // Показываем системные упражнения + свои пользовательские
        $query = Exercise::active()->where(function($q) {
            $q->where('is_system', true) // Системные упражнения видны всем
              ->orWhere('trainer_id', auth()->id()); // + свои пользовательские
        });

        // Фильтрация
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('equipment')) {
            $query->byEquipment($request->equipment);
        }

        $exercises = $query->paginate(12);
        
        // Для JavaScript нужны все упражнения без пагинации
        $allExercises = Exercise::active()->where(function($q) {
            $q->where('is_system', true) // Системные упражнения видны всем
              ->orWhere('trainer_id', auth()->id()); // + свои пользовательские
        })->orderBy('created_at', 'desc')->get();

        // Определяем view в зависимости от роли пользователя
        $view = auth()->user()->hasRole('self-athlete') ? 'crm.self-athlete.exercises' : 'crm.trainer.exercises.index';
        
        return view($view, compact('exercises', 'allExercises'));
    }

    public function create()
    {
        return view('crm.trainer.exercises.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Exercise::CATEGORIES)),
            'equipment' => 'required|string|in:' . implode(',', array_keys(Exercise::EQUIPMENT)),
            'instructions' => 'nullable|string',
            'muscle_groups' => 'nullable|array',
            'image' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'fields_config' => 'nullable|array'
        ]);

        $data = $request->except(['image']);
        $data['trainer_id'] = auth()->id();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('exercises', 'public');
            $data['image_url'] = $path;
        }
        
        $exercise = Exercise::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Упражнение добавлено в каталог',
            'exercise' => $exercise
        ]);
    }

    public function show($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('crm.trainer.exercises.show', compact('exercise'));
    }

    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('crm.trainer.exercises.edit', compact('exercise'));
    }

    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        // Проверяем права на редактирование
        if ($exercise->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя редактировать системные упражнения'
            ], 403);
        }

        if ($exercise->trainer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя редактировать чужие упражнения'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Exercise::CATEGORIES)),
            'equipment' => 'required|string|in:' . implode(',', array_keys(Exercise::EQUIPMENT)),
            'instructions' => 'nullable|string',
            'muscle_groups' => 'nullable|array',
            'image' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'fields_config' => 'nullable|array'
        ]);

        $data = $request->except(['image', 'remove_image']);
        
        // Если пришел флаг удаления картинки
        if ($request->input('remove_image') == '1') {
            if ($exercise->image_url && \Storage::disk('public')->exists($exercise->image_url)) {
                \Storage::disk('public')->delete($exercise->image_url);
            }
            $data['image_url'] = null;
        }
        // Если загружается новая картинка
        elseif ($request->hasFile('image')) {
            if ($exercise->image_url && \Storage::disk('public')->exists($exercise->image_url)) {
                \Storage::disk('public')->delete($exercise->image_url);
            }
            $path = $request->file('image')->store('exercises', 'public');
            $data['image_url'] = $path;
        }
        
        $exercise->update($data);
        $exercise->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Упражнение обновлено',
            'exercise' => $exercise
        ]);
    }

    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        
        // Проверяем права на удаление
        if ($exercise->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалять системные упражнения'
            ], 403);
        }

        if ($exercise->trainer_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалять чужие упражнения'
            ], 403);
        }
        
        // Проверяем использование упражнения в тренировках
        $workoutUsageCount = \DB::table('workout_exercise')
            ->where('exercise_id', $id)
            ->count();
            
        // Проверяем использование упражнения в шаблонах тренировок (включая неактивные)
        $templates = \App\Models\Trainer\WorkoutTemplate::all();
        $templateUsageCount = 0;
        
        foreach ($templates as $template) {
            if ($template->exercises && is_array($template->exercises)) {
                foreach ($template->exercises as $templateExercise) {
                    $exerciseId = $templateExercise['id'] ?? $templateExercise['exercise_id'] ?? null;
                    if ($exerciseId == $id) {
                        $templateUsageCount++;
                        break; // Нашли одно использование в этом шаблоне
                    }
                }
            }
        }
        
        // Если упражнение используется, запрещаем удаление
        if ($workoutUsageCount > 0 || $templateUsageCount > 0) {
            $message = 'Нельзя удалить упражнение, которое используется в ';
            
            if ($workoutUsageCount > 0 && $templateUsageCount > 0) {
                $message .= "тренировках ({$workoutUsageCount}) и шаблонах ({$templateUsageCount})";
            } elseif ($workoutUsageCount > 0) {
                $message .= "тренировках ({$workoutUsageCount})";
            } else {
                $message .= "шаблонах ({$templateUsageCount})";
            }
            
            return response()->json([
                'success' => false,
                'message' => $message
            ], 400);
        }
        
        // Если не используется, удаляем
        if ($exercise->image_url && \Storage::disk('public')->exists($exercise->image_url)) {
            \Storage::disk('public')->delete($exercise->image_url);
        }
        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Упражнение удалено из каталога'
        ]);
    }


    public function api()
    {
        // Показываем системные упражнения + свои пользовательские
        $exercises = Exercise::active()->where(function($q) {
            $q->where('is_system', true) // Системные упражнения видны всем
              ->orWhere('trainer_id', auth()->id()); // + свои пользовательские
        })->get();

        return response()->json([
            'success' => true,
            'exercises' => $exercises
        ]);
    }

    // Методы для работы с пользовательскими видео к системным упражнениям
    
    public function storeUserVideo(Request $request, $exerciseId)
    {
        $exercise = Exercise::findOrFail($exerciseId);
        
        // Проверяем, что упражнение системное
        if (!$exercise->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'Можно добавлять видео только к системным упражнениям'
            ], 400);
        }

        $request->validate([
            'video_url' => 'required|url',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        // Проверяем, есть ли уже видео от этого пользователя для этого упражнения
        $existingVideo = UserExerciseVideo::where('user_id', auth()->id())
            ->where('exercise_id', $exerciseId)
            ->first();

        if ($existingVideo) {
            // Обновляем существующее видео
            $existingVideo->update($request->only(['video_url', 'title', 'description']));
            $video = $existingVideo;
            $message = 'Видео обновлено';
        } else {
            // Создаем новое видео
            $video = UserExerciseVideo::create([
                'user_id' => auth()->id(),
                'exercise_id' => $exerciseId,
                'video_url' => $request->video_url,
                'title' => $request->title,
                'description' => $request->description
            ]);
            $message = 'Видео добавлено';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'video' => $video
        ]);
    }

    public function getUserVideo($exerciseId)
    {
        $video = UserExerciseVideo::where('user_id', auth()->id())
            ->where('exercise_id', $exerciseId)
            ->where('is_active', true)
            ->first();

        return response()->json([
            'success' => true,
            'video' => $video
        ]);
    }

    public function deleteUserVideo($exerciseId)
    {
        $video = UserExerciseVideo::where('user_id', auth()->id())
            ->where('exercise_id', $exerciseId)
            ->first();

        if (!$video) {
            return response()->json([
                'success' => false,
                'message' => 'Видео не найдено'
            ], 404);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Видео удалено'
        ]);
    }

    public function getAllUserVideos()
    {
        $videos = UserExerciseVideo::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'videos' => $videos
        ]);
    }
}

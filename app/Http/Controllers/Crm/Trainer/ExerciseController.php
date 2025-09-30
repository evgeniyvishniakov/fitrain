<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExerciseController extends BaseController
{
    public function index(Request $request)
    {
        $query = Exercise::active();

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

        return view('crm.trainer.exercises.index', compact('exercises'));
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
            'image_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'fields_config' => 'nullable|array'
        ]);

        $exercise = Exercise::create($request->all());

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

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Exercise::CATEGORIES)),
            'equipment' => 'required|string|in:' . implode(',', array_keys(Exercise::EQUIPMENT)),
            'instructions' => 'nullable|string',
            'muscle_groups' => 'nullable|array',
            'image_url' => 'nullable|url',
            'video_url' => 'nullable|url',
            'fields_config' => 'nullable|array'
        ]);

        $exercise->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Упражнение обновлено',
            'exercise' => $exercise
        ]);
    }

    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        
        // Проверяем использование упражнения в тренировках
        $workoutUsageCount = \DB::table('workout_exercise')
            ->where('exercise_id', $id)
            ->count();
            
        // Проверяем использование упражнения в шаблонах тренировок (включая неактивные)
        $templates = \App\Models\Trainer\WorkoutTemplate::all();
        $templateUsageCount = 0;
        
        foreach ($templates as $template) {
            if ($template->exercises && is_array($template->exercises)) {
                foreach ($template->exercises as $exercise) {
                    $exerciseId = $exercise['id'] ?? $exercise['exercise_id'] ?? null;
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
        $exercise->delete();

        return response()->json([
            'success' => true,
            'message' => 'Упражнение удалено из каталога'
        ]);
    }


    public function api()
    {
        $exercises = Exercise::active()->get();

        return response()->json([
            'success' => true,
            'exercises' => $exercises
        ]);
    }
}

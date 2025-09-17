<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;

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
        $exercise->update(['is_active' => false]);

        // Обновляем все шаблоны тренировок, удаляя это упражнение
        $this->removeExerciseFromTemplates($id);

        return response()->json([
            'success' => true,
            'message' => 'Упражнение удалено из каталога'
        ]);
    }

    /**
     * Удаляет упражнение из всех шаблонов тренировок
     */
    private function removeExerciseFromTemplates($exerciseId)
    {
        $templates = \App\Models\Trainer\WorkoutTemplate::where('is_active', true)->get();
        
        foreach ($templates as $template) {
            if (is_array($template->exercises)) {
                $updatedExercises = array_filter($template->exercises, function($exercise) use ($exerciseId) {
                    $exerciseIdFromTemplate = $exercise['id'] ?? $exercise['exercise_id'] ?? null;
                    return $exerciseIdFromTemplate != $exerciseId;
                });
                
                // Обновляем шаблон только если что-то изменилось
                if (count($updatedExercises) !== count($template->exercises)) {
                    $template->update(['exercises' => array_values($updatedExercises)]);
                }
            }
        }
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

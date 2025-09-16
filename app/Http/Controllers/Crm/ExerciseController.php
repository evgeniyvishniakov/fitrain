<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
use App\Models\Exercise;
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

        return view('crm.exercises.index', compact('exercises'));
    }

    public function create()
    {
        return view('crm.exercises.create');
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
            'image_url' => 'nullable|url'
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
        return view('crm.exercises.show', compact('exercise'));
    }

    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('crm.exercises.edit', compact('exercise'));
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
            'image_url' => 'nullable|url'
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

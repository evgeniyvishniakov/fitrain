<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends BaseController
{
    public function index(Request $request)
    {
        $query = Exercise::query()->orderBy('created_at', 'desc');
        
        // Фильтрация
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('equipment')) {
            $query->where('equipment', $request->equipment);
        }
        
        if ($request->filled('is_system')) {
            $query->where('is_system', $request->is_system === '1');
        }
        
        $exercises = $query->paginate(20)->appends($request->query());
        
        return view('admin.exercises.index', compact('exercises'));
    }
    
    public function create()
    {
        return view('admin.exercises.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_uk' => 'required|string|max:255',
            'description_ru' => 'nullable|string',
            'description_uk' => 'nullable|string',
            'instructions_ru' => 'nullable|string',
            'instructions_uk' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Exercise::CATEGORIES)),
            'equipment' => 'nullable|string|in:' . implode(',', array_keys(Exercise::EQUIPMENT)),
            'muscle_groups_ru' => 'nullable|array',
            'muscle_groups_uk' => 'nullable|array',
            'video_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'image_2' => 'nullable|image|max:10240',
            'fields_config' => 'nullable|array',
            'is_active' => 'nullable|in:0,1',
            'is_system' => 'nullable|in:0,1',
        ]);
        
        $data = [
            'name' => $request->name_ru,
            'description' => $request->description_ru,
            'instructions' => $request->instructions_ru,
            'category' => $request->category,
            'equipment' => $request->equipment ?: null,
            'muscle_groups' => $request->muscle_groups_ru ?? [],
            'video_url' => $request->video_url,
            'fields_config' => $request->fields_config ?? ['sets', 'reps', 'weight', 'rest'],
            'is_active' => $request->boolean('is_active', true),
            'is_system' => $request->boolean('is_system', true),
            'translations' => [
                'ru' => [
                    'name' => $request->name_ru,
                    'description' => $request->description_ru ?? '',
                    'instructions' => $request->instructions_ru ?? '',
                    'muscle_groups' => $request->muscle_groups_ru ?? [],
                ],
                'ua' => [
                    'name' => $request->name_uk,
                    'description' => $request->description_uk ?? '',
                    'instructions' => $request->instructions_uk ?? '',
                    'muscle_groups' => $request->muscle_groups_uk ?? [],
                ]
            ]
        ];
        
        // Загрузка изображений
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('exercises', 'public');
            $data['image_url'] = $path;
        }
        
        if ($request->hasFile('image_2')) {
            $path = $request->file('image_2')->store('exercises', 'public');
            $data['image_url_2'] = $path;
        }
        
        $exercise = Exercise::create($data);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'exercise' => $exercise]);
        }
        
        return redirect()->route('admin.exercises.index')
            ->with('success', 'Упражнение успешно создано');
    }
    
    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        
        if (request()->expectsJson()) {
            // Возвращаем данные с оригинальными атрибутами и translations
            return response()->json([
                'id' => $exercise->id,
                'name' => $exercise->getOriginal('name'),
                'description' => $exercise->getOriginal('description'),
                'instructions' => $exercise->getOriginal('instructions'),
                'category' => $exercise->category,
                'equipment' => $exercise->equipment,
                'muscle_groups' => $exercise->getOriginal('muscle_groups'),
                'video_url' => $exercise->video_url,
                'image_url' => $exercise->image_url,
                'image_url_2' => $exercise->image_url_2,
                'is_active' => $exercise->is_active,
                'is_system' => $exercise->is_system,
                'fields_config' => $exercise->fields_config,
                'translations' => $exercise->translations,
            ]);
        }
        
        return view('admin.exercises.edit', compact('exercise'));
    }
    
    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);
        
        $request->validate([
            'name_ru' => 'required|string|max:255',
            'name_uk' => 'required|string|max:255',
            'description_ru' => 'nullable|string',
            'description_uk' => 'nullable|string',
            'instructions_ru' => 'nullable|string',
            'instructions_uk' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(Exercise::CATEGORIES)),
            'equipment' => 'nullable|string|in:' . implode(',', array_keys(Exercise::EQUIPMENT)),
            'muscle_groups_ru' => 'nullable|array',
            'muscle_groups_uk' => 'nullable|array',
            'video_url' => 'nullable|url',
            'image' => 'nullable|image|max:10240',
            'image_2' => 'nullable|image|max:10240',
            'fields_config' => 'nullable|array',
            'is_active' => 'nullable|in:0,1',
            'is_system' => 'nullable|in:0,1',
        ]);
        
        $data = [
            'name' => $request->name_ru,
            'description' => $request->description_ru,
            'instructions' => $request->instructions_ru,
            'category' => $request->category,
            'equipment' => $request->equipment ?: null,
            'muscle_groups' => $request->muscle_groups_ru ?? [],
            'video_url' => $request->video_url,
            'fields_config' => $request->fields_config ?? ['sets', 'reps', 'weight', 'rest'],
            'is_active' => $request->boolean('is_active', true),
            'is_system' => $request->boolean('is_system', false),
            'translations' => [
                'ru' => [
                    'name' => $request->name_ru,
                    'description' => $request->description_ru ?? '',
                    'instructions' => $request->instructions_ru ?? '',
                    'muscle_groups' => $request->muscle_groups_ru ?? [],
                ],
                'ua' => [
                    'name' => $request->name_uk,
                    'description' => $request->description_uk ?? '',
                    'instructions' => $request->instructions_uk ?? '',
                    'muscle_groups' => $request->muscle_groups_uk ?? [],
                ]
            ]
        ];
        
        // Загрузка новых изображений
        if ($request->hasFile('image')) {
            // Удаляем старое
            if ($exercise->image_url && \Storage::disk('public')->exists($exercise->image_url)) {
                \Storage::disk('public')->delete($exercise->image_url);
            }
            $path = $request->file('image')->store('exercises', 'public');
            $data['image_url'] = $path;
        }
        
        if ($request->hasFile('image_2')) {
            // Удаляем старое
            if ($exercise->image_url_2 && \Storage::disk('public')->exists($exercise->image_url_2)) {
                \Storage::disk('public')->delete($exercise->image_url_2);
            }
            $path = $request->file('image_2')->store('exercises', 'public');
            $data['image_url_2'] = $path;
        }
        
        $exercise->update($data);
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'exercise' => $exercise]);
        }
        
        return redirect()->route('admin.exercises.index')
            ->with('success', 'Упражнение успешно обновлено');
    }
    
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        
        // Проверяем использование в тренировках
        $workoutUsageCount = \DB::table('workout_exercise')
            ->where('exercise_id', $id)
            ->count();
        
        if ($workoutUsageCount > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => "Нельзя удалить упражнение, которое используется в тренировках ({$workoutUsageCount})"
                ], 400);
            }
            return redirect()->back()
                ->with('error', "Нельзя удалить упражнение, которое используется в тренировках ({$workoutUsageCount})");
        }
        
        $exercise->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('admin.exercises.index')
            ->with('success', 'Упражнение успешно удалено');
    }
}

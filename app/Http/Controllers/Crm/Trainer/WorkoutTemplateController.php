<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\WorkoutTemplate;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;

class WorkoutTemplateController extends BaseController
{
    public function index(Request $request)
    {
        $query = WorkoutTemplate::active()->with('creator');

        // Фильтрация
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        $templates = $query->paginate(12);
        
        // Добавляем валидные упражнения для каждого шаблона
        foreach ($templates as $template) {
            $template->valid_exercises = $template->valid_exercises;
        }

        return view('crm.trainer.workout-templates.index', compact('templates'));
    }

    public function create()
    {
        $exercises = Exercise::active()->orderBy('name')->get();
        return view('crm.trainer.workout-templates.create', compact('exercises'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(WorkoutTemplate::CATEGORIES)),
            'difficulty' => 'required|string|in:' . implode(',', array_keys(WorkoutTemplate::DIFFICULTY_LEVELS)),
            'estimated_duration' => 'nullable|integer|min:1',
            'exercises' => 'nullable|array',
            'exercises.*.id' => 'required|exists:exercises,id',
            'exercises.*.name' => 'nullable|string',
            'exercises.*.category' => 'nullable|string',
            'exercises.*.equipment' => 'nullable|string'
        ]);

        $template = WorkoutTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'estimated_duration' => $request->estimated_duration,
            'exercises' => $request->exercises ?? [],
            'created_by' => auth()->id(),
            'is_public' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Шаблон тренировки создан',
            'template' => $template->load('creator')
        ]);
    }

    public function show($id)
    {
        $template = WorkoutTemplate::with('creator')->findOrFail($id);
        // Добавляем валидные упражнения в шаблон
        $template->valid_exercises = $template->valid_exercises;
        return view('crm.trainer.workout-templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = WorkoutTemplate::findOrFail($id);
        $exercises = Exercise::active()->orderBy('name')->get();
        // Добавляем валидные упражнения в шаблон
        $template->valid_exercises = $template->valid_exercises;
        return view('crm.trainer.workout-templates.edit', compact('template', 'exercises'));
    }

    public function update(Request $request, $id)
    {
        $template = WorkoutTemplate::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(WorkoutTemplate::CATEGORIES)),
            'difficulty' => 'required|string|in:' . implode(',', array_keys(WorkoutTemplate::DIFFICULTY_LEVELS)),
            'estimated_duration' => 'nullable|integer|min:1',
            'exercises' => 'nullable|array',
            'exercises.*.id' => 'required|exists:exercises,id',
            'exercises.*.name' => 'nullable|string',
            'exercises.*.category' => 'nullable|string',
            'exercises.*.equipment' => 'nullable|string'
        ]);

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'estimated_duration' => $request->estimated_duration,
            'exercises' => $request->exercises ?? [],
            'is_public' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Шаблон тренировки обновлен',
            'template' => $template->load('creator')
        ]);
    }

    public function destroy($id)
    {
        $template = WorkoutTemplate::findOrFail($id);
        $template->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Шаблон тренировки удален'
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'count' => 'nullable|integer|min:1|max:10',
            'category' => 'nullable|string|in:' . implode(',', array_keys(WorkoutTemplate::CATEGORIES)),
            'difficulty' => 'nullable|string|in:' . implode(',', array_keys(WorkoutTemplate::DIFFICULTY_LEVELS))
        ]);

        $count = $request->input('count', 3);
        $category = $request->input('category');
        $difficulty = $request->input('difficulty');

        // Получаем упражнения
        $exercises = Exercise::active()->get();
        if ($exercises->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Нет доступных упражнений для создания шаблонов'
            ], 400);
        }

        $templates = [];
        $categories = $category ? [$category] : ['strength', 'cardio', 'flexibility', 'mixed'];
        $difficulties = $difficulty ? [$difficulty] : ['beginner', 'intermediate', 'advanced'];

        for ($i = 0; $i < $count; $i++) {
            $selectedCategory = $categories[array_rand($categories)];
            $selectedDifficulty = $difficulties[array_rand($difficulties)];

            $templateData = $this->generateTemplateData($selectedCategory, $selectedDifficulty, $exercises);

            $template = WorkoutTemplate::create([
                'name' => $templateData['name'],
                'description' => $templateData['description'],
                'category' => $selectedCategory,
                'difficulty' => $selectedDifficulty,
                'estimated_duration' => $templateData['duration'],
                'exercises' => $templateData['exercises'],
                'created_by' => auth()->id(),
                'is_public' => rand(0, 1) == 1
            ]);

            $templates[] = $template->load('creator');
        }

        return response()->json([
            'success' => true,
            'message' => "Создано {$count} шаблонов тренировок",
            'templates' => $templates
        ]);
    }

    private function generateTemplateData($category, $difficulty, $exercises)
    {
        $templates = [
            'strength' => [
                'beginner' => ['Базовая силовая тренировка', 'Идеальная тренировка для начинающих в силовом спорте', 60, 4],
                'intermediate' => ['Прогрессивная силовая тренировка', 'Тренировка для атлетов среднего уровня подготовки', 75, 6],
                'advanced' => ['Интенсивная силовая тренировка', 'Сложная тренировка для опытных атлетов', 90, 8]
            ],
            'cardio' => [
                'beginner' => ['Легкое кардио', 'Низкоинтенсивная кардио тренировка', 30, 3],
                'intermediate' => ['Интервальное кардио', 'Высокоинтенсивная интервальная тренировка', 45, 5],
                'advanced' => ['Экстремальное кардио', 'Максимально интенсивная кардио тренировка', 60, 7]
            ],
            'flexibility' => [
                'beginner' => ['Базовая растяжка', 'Основные упражнения на гибкость', 30, 5],
                'intermediate' => ['Продвинутая растяжка', 'Упражнения для улучшения подвижности', 45, 7],
                'advanced' => ['Профессиональная растяжка', 'Сложные упражнения на гибкость', 60, 9]
            ],
            'mixed' => [
                'beginner' => ['Смешанная тренировка для начинающих', 'Комбинация силовых и кардио упражнений', 45, 5],
                'intermediate' => ['Функциональная тренировка', 'Разносторонняя тренировка всего тела', 60, 7],
                'advanced' => ['Кроссфит тренировка', 'Высокоинтенсивная тренировка всех систем', 75, 9]
            ]
        ];

        [$name, $description, $duration, $exercisesCount] = $templates[$category][$difficulty];

        // Выбираем упражнения
        $categoryExercises = $exercises->where('category', $category)->take($exercisesCount);
        if ($categoryExercises->count() < $exercisesCount) {
            $categoryExercises = $exercises->random($exercisesCount);
        }

        $exercisesData = [];
        foreach ($categoryExercises as $exercise) {
            $sets = rand(2, 4);
            $rest = $category === 'cardio' ? rand(30, 60) : rand(60, 180);
            
            $exercisesData[] = [
                'exercise_id' => $exercise->id,
                'sets' => $sets,
                'reps' => $this->generateReps($category, $difficulty),
                'weight' => $category === 'cardio' || $category === 'flexibility' ? null : $this->generateWeight($difficulty),
                'rest' => $rest
            ];
        }

        return [
            'name' => $name,
            'description' => $description,
            'duration' => $duration,
            'exercises' => $exercisesData
        ];
    }

    private function generateReps($category, $difficulty)
    {
        $reps = [
            'strength' => ['8-12', '10-15', '12-15'],
            'cardio' => ['30 сек', '45 сек', '1 мин'],
            'flexibility' => ['30 сек', '45 сек', '1 мин'],
            'mixed' => ['8-12', '30 сек', '10-15']
        ];

        return $reps[$category][array_rand($reps[$category])];
    }

    private function generateWeight($difficulty)
    {
        $weights = [
            'beginner' => ['50-60% от 1ПМ', '40-50% от 1ПМ', 'Собственный вес'],
            'intermediate' => ['60-70% от 1ПМ', '70-80% от 1ПМ', '50-60% от 1ПМ'],
            'advanced' => ['80-90% от 1ПМ', '70-85% от 1ПМ', '60-75% от 1ПМ']
        ];

        return $weights[$difficulty][array_rand($weights[$difficulty])];
    }
}
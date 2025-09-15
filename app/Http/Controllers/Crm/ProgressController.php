<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
use App\Models\Crm\Progress;
use App\Models\Crm\Workout;
use Illuminate\Http\Request;

class ProgressController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            $progress = Progress::with(['athlete', 'workout'])
                ->whereHas('athlete', function($query) {
                    $query->where('trainer_id', auth()->id());
                })
                ->paginate(10);
        } else {
            $progress = $user->progress()->with('workout')->paginate(10);
        }
        
        return view('crm.progress.index', compact('progress'));
    }
    
    public function create()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            $workouts = Workout::where('trainer_id', auth()->id())->get();
        } else {
            $workouts = $user->workouts()->get();
        }
        
        return view('crm.progress.create', compact('workouts'));
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'reps' => 'nullable|integer|min:0',
            'sets' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
        ]);
        
        // Проверяем доступ к тренировке
        $workout = Workout::findOrFail($request->workout_id);
        
        if ($user->hasRole('athlete') && $workout->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $workout->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        Progress::create([
            'athlete_id' => $workout->athlete_id,
            'workout_id' => $request->workout_id,
            'date' => $request->date,
            'weight' => $request->weight,
            'reps' => $request->reps,
            'sets' => $request->sets,
            'notes' => $request->notes,
            'photos' => $request->photos,
        ]);
        
        return redirect()->route('crm.progress.index')->with('success', 'Прогресс добавлен');
    }
    
    public function show($id)
    {
        $progress = Progress::with(['athlete', 'workout'])->findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $progress->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $progress->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        return view('crm.progress.show', compact('progress'));
    }
    
    public function edit($id)
    {
        $progress = Progress::findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $progress->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $progress->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer')) {
            $workouts = Workout::where('trainer_id', auth()->id())->get();
        } else {
            $workouts = $user->workouts()->get();
        }
        
        return view('crm.progress.edit', compact('progress', 'workouts'));
    }
    
    public function update(Request $request, $id)
    {
        $progress = Progress::findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $progress->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $progress->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'reps' => 'nullable|integer|min:0',
            'sets' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'photos' => 'nullable|array',
        ]);
        
        $progress->update($request->all());
        
        return redirect()->route('crm.progress.index')->with('success', 'Прогресс обновлен');
    }
}

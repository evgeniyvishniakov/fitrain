<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
use App\Models\Crm\Workout;
use App\Models\Crm\Athlete;
use Illuminate\Http\Request;

class WorkoutController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            $workouts = $user->workouts()->with('athlete')->paginate(4);
        } else {
            $workouts = $user->workouts()->with('trainer')->paginate(4);
        }
        
        return view('crm.workouts.index', compact('workouts'));
    }
    
    public function create()
    {
        if (!auth()->user()->hasRole('trainer')) {
            abort(403, 'Доступ запрещен');
        }
        
        $athletes = Athlete::where('trainer_id', auth()->id())->get();
        return view('crm.workouts.create', compact('athletes'));
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
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|in:planned,completed,cancelled',
        ]);
        
        $workout = Workout::create([
            'title' => $request->title,
            'description' => $request->description,
            'trainer_id' => auth()->id(),
            'athlete_id' => $request->athlete_id,
            'date' => $request->date,
            'duration' => $request->duration,
            'status' => $request->status ?? 'planned',
        ]);
        
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
        
        return view('crm.workouts.show', compact('workout'));
    }
    
    public function edit($id)
    {
        if (!auth()->user()->hasRole('trainer')) {
            abort(403, 'Доступ запрещен');
        }
        
        $workout = Workout::findOrFail($id);
        
        if ($workout->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $athletes = Athlete::where('trainer_id', auth()->id())->get();
        
        return view('crm.workouts.edit', compact('workout', 'athletes'));
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
            'duration' => 'nullable|integer|min:1',
            'status' => 'required|in:planned,completed,cancelled',
        ]);
        
        $workout->update($request->all());
        
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
        
        $workout->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Тренировка удалена'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Crm\SelfAthlete;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Workout;
use App\Models\Trainer\Exercise;
use Illuminate\Http\Request;

class SelfAthleteController extends BaseController
{
    /**
     * Дашборд Self-Athlete
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Получаем статистику
        $workouts = Workout::where('athlete_id', $user->id)->count();
        $exercises = Exercise::count(); // Все доступные упражнения
        $recentWorkouts = Workout::where('athlete_id', $user->id)
            ->with(['exercises'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('crm.self-athlete.dashboard', compact('workouts', 'exercises', 'recentWorkouts'));
    }
    
    // Профиль перенесен в настройки
}

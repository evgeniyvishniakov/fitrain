<?php

namespace App\Http\Controllers\Crm\Athlete;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Athlete\Athlete;
use App\Models\Trainer\Workout;
use App\Models\Athlete\Progress;
use App\Models\Athlete\Nutrition;
use Illuminate\Http\Request;

class AthleteController extends BaseController
{
    public function dashboard()
    {
        $athlete = auth()->user();
        $workouts = $athlete->athleteWorkouts()->count();
        $progress = $athlete->progress()->count();
        $recentWorkouts = $athlete->athleteWorkouts()->with('trainer')->latest()->take(5)->get();
        
        return view('crm.athlete.dashboard', compact('athlete', 'workouts', 'progress', 'recentWorkouts'));
    }
    
    public function profile()
    {
        $athlete = auth()->user();
        return view('crm.athlete.profile', compact('athlete'));
    }
    
    public function updateProfile(Request $request)
    {
        $athlete = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $athlete->id,
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);
        
        $athlete->update($request->all());
        
        return redirect()->route('crm.athlete.profile')->with('success', 'Профиль обновлен');
    }
    
    public function workouts()
    {
        $athlete = auth()->user();
        $workouts = $athlete->athleteWorkouts()->with('trainer')->paginate(10);
        
        return view('crm.athlete.workouts', compact('workouts'));
    }
    
    public function progress()
    {
        $athlete = auth()->user();
        $progress = $athlete->progress()->paginate(10);
        
        return view('crm.athlete.progress', compact('progress'));
    }
    
    public function nutrition()
    {
        $athlete = auth()->user();
        $nutrition = $athlete->nutrition()->paginate(10);
        
        return view('crm.athlete.nutrition', compact('nutrition'));
    }
}

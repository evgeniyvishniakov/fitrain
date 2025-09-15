<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Crm\BaseController;
use App\Models\Crm\Trainer;
use App\Models\Crm\Athlete;
use App\Models\Crm\Workout;
use Illuminate\Http\Request;

class TrainerController extends BaseController
{
    public function dashboard()
    {
        $trainer = auth()->user();
        $athletes = $trainer->athletes()->count();
        $workouts = $trainer->workouts()->count();
        $recentWorkouts = $trainer->workouts()->with('athlete')->latest()->take(5)->get();
        
        return view('crm.trainer.dashboard', compact('trainer', 'athletes', 'workouts', 'recentWorkouts'));
    }
    
    public function profile()
    {
        $trainer = auth()->user();
        return view('crm.trainer.profile', compact('trainer'));
    }
    
    public function updateProfile(Request $request)
    {
        $trainer = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $trainer->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
        ]);
        
        $trainer->update($request->all());
        
        return redirect()->route('crm.trainer.profile')->with('success', 'Профиль обновлен');
    }
    
    public function athletes()
    {
        $trainer = auth()->user();
        $athletes = $trainer->athletes()->paginate(10);
        
        return view('crm.trainer.athletes', compact('athletes'));
    }
    
    public function addAthlete()
    {
        return view('crm.trainer.add-athlete');
    }
    
    public function storeAthlete(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'age' => 'nullable|integer|min:1|max:120',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
        ]);
        
        $athlete = Athlete::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'age' => $request->age,
            'weight' => $request->weight,
            'height' => $request->height,
            'trainer_id' => auth()->id(),
            'role' => 'athlete',
        ]);
        
        $athlete->assignRole('athlete');
        
        return redirect()->route('crm.trainer.athletes')->with('success', 'Спортсмен добавлен');
    }
    
    public function removeAthlete($id)
    {
        $athlete = Athlete::findOrFail($id);
        
        if ($athlete->trainer_id !== auth()->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $athlete->delete();
        
        return redirect()->route('crm.trainer.athletes')->with('success', 'Спортсмен удален');
    }
}

<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Trainer;
use App\Models\Trainer\Athlete;
use App\Models\Trainer\Workout;
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
        $athletes = $trainer->athletes()->with(['workouts' => function($query) {
            $query->latest()->take(1);
        }])->paginate(12);
        
        return view('crm.trainer.athletes.index', compact('athletes'));
    }
    
    public function addAthlete()
    {
        return view('crm.trainer.athletes.create');
    }
    
    public function storeAthlete(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'sport_level' => 'nullable|in:beginner,intermediate,advanced',
            'goals' => 'nullable|array',
            'health_restrictions' => 'nullable|string',
        ]);
        
        // Вычисляем возраст из даты рождения
        $age = null;
        if ($request->birth_date) {
            $age = \Carbon\Carbon::parse($request->birth_date)->age;
        }
        
        $athlete = Athlete::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'age' => $age,
            'gender' => $request->gender,
            'weight' => $request->weight,
            'height' => $request->height,
            'sport_level' => $request->sport_level,
            'goals' => $request->goals ? json_encode($request->goals) : null,
            'health_restrictions' => $request->health_restrictions ? json_encode([['type' => 'Общие ограничения', 'description' => $request->health_restrictions]]) : null,
            'trainer_id' => auth()->id(),
        ]);
        
        $athlete->assignRole('athlete');
        
        return redirect()->route('crm.trainer.athletes')->with('success', 'Спортсмен добавлен');
    }
    
    public function showAthlete($id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        // Загружаем связанные данные
        $athlete->load([
            'workouts' => function($query) {
                $query->latest()->take(10);
            },
            'progress' => function($query) {
                $query->latest()->take(20);
            }
        ]);
        
        return view('crm.trainer.athletes.show', compact('athlete'));
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
    
    // Сохранение измерения
    public function storeMeasurement(Request $request, $id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'resting_heart_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $measurement = $athlete->measurements()->create([
            'measurement_date' => $request->measurement_date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'water_percentage' => $request->water_percentage,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hips' => $request->hips,
            'bicep' => $request->bicep,
            'thigh' => $request->thigh,
            'neck' => $request->neck,
            'resting_heart_rate' => $request->resting_heart_rate,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'notes' => $request->notes,
            'measured_by' => auth()->id(),
        ]);
        
        // Обновляем профиль спортсмена с новыми весом и ростом
        if ($request->weight) {
            $athlete->weight = $request->weight;
        }
        if ($request->height) {
            $athlete->height = $request->height;
        }
        $athlete->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно сохранено',
            'measurement' => $measurement
        ]);
    }
    
    // Получение измерений спортсмена
    public function getMeasurements($id)
    {
        $athlete = Athlete::where('id', $id)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurements = $athlete->measurements()
            ->orderBy('measurement_date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'measurements' => $measurements
        ]);
    }
    
    // Обновление измерения
    public function updateMeasurement(Request $request, $athleteId, $measurementId)
    {
        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurement = $athlete->measurements()
            ->where('id', $measurementId)
            ->firstOrFail();
        
        $request->validate([
            'measurement_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'body_fat_percentage' => 'nullable|numeric|min:0|max:100',
            'muscle_mass' => 'nullable|numeric|min:0',
            'water_percentage' => 'nullable|numeric|min:0|max:100',
            'chest' => 'nullable|numeric|min:0',
            'waist' => 'nullable|numeric|min:0',
            'hips' => 'nullable|numeric|min:0',
            'bicep' => 'nullable|numeric|min:0',
            'thigh' => 'nullable|numeric|min:0',
            'neck' => 'nullable|numeric|min:0',
            'resting_heart_rate' => 'nullable|integer|min:0',
            'blood_pressure_systolic' => 'nullable|integer|min:0',
            'blood_pressure_diastolic' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $measurement->update([
            'measurement_date' => $request->measurement_date,
            'weight' => $request->weight,
            'height' => $request->height,
            'body_fat_percentage' => $request->body_fat_percentage,
            'muscle_mass' => $request->muscle_mass,
            'water_percentage' => $request->water_percentage,
            'chest' => $request->chest,
            'waist' => $request->waist,
            'hips' => $request->hips,
            'bicep' => $request->bicep,
            'thigh' => $request->thigh,
            'neck' => $request->neck,
            'resting_heart_rate' => $request->resting_heart_rate,
            'blood_pressure_systolic' => $request->blood_pressure_systolic,
            'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
            'notes' => $request->notes,
        ]);
        
        // Обновляем профиль спортсмена с новыми весом и ростом
        if ($request->weight) {
            $athlete->weight = $request->weight;
        }
        if ($request->height) {
            $athlete->height = $request->height;
        }
        $athlete->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно обновлено',
            'measurement' => $measurement
        ]);
    }
    
    // Удаление измерения
    public function deleteMeasurement($athleteId, $measurementId)
    {
        $athlete = Athlete::where('id', $athleteId)
            ->where('trainer_id', auth()->id())
            ->firstOrFail();
        
        $measurement = $athlete->measurements()
            ->where('id', $measurementId)
            ->firstOrFail();
        
        $measurement->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Измерение успешно удалено'
        ]);
    }
}

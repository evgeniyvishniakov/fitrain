<?php

namespace App\Http\Controllers\Crm\Trainer;

use App\Http\Controllers\Crm\Shared\BaseController;
use App\Models\Trainer\Nutrition;
use Illuminate\Http\Request;

class NutritionController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('trainer')) {
            $nutrition = Nutrition::with('athlete')
                ->whereHas('athlete', function($query) {
                    $query->where('trainer_id', auth()->id());
                })
                ->paginate(10);
        } else {
            $nutrition = $user->nutrition()->paginate(10);
        }
        
        return view('crm.nutrition.index', compact('nutrition'));
    }
    
    public function create()
    {
        return view('crm.nutrition.create');
    }
    
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'food_name' => 'required|string|max:255',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        Nutrition::create([
            'athlete_id' => $user->id(),
            'date' => $request->date,
            'meal_type' => $request->meal_type,
            'food_name' => $request->food_name,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'carbs' => $request->carbs,
            'fat' => $request->fat,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('crm.nutrition.index')->with('success', 'Запись добавлена');
    }
    
    public function edit($id)
    {
        $nutrition = Nutrition::findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $nutrition->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $nutrition->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        return view('crm.nutrition.edit', compact('nutrition'));
    }
    
    public function update(Request $request, $id)
    {
        $nutrition = Nutrition::findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $nutrition->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $nutrition->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $request->validate([
            'date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'food_name' => 'required|string|max:255',
            'calories' => 'nullable|integer|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $nutrition->update($request->all());
        
        return redirect()->route('crm.nutrition.index')->with('success', 'Запись обновлена');
    }
    
    public function destroy($id)
    {
        $nutrition = Nutrition::findOrFail($id);
        $user = auth()->user();
        
        // Проверяем доступ
        if ($user->hasRole('athlete') && $nutrition->athlete_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        if ($user->hasRole('trainer') && $nutrition->athlete->trainer_id !== $user->id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $nutrition->delete();
        
        return redirect()->route('crm.nutrition.index')->with('success', 'Запись удалена');
    }
}

<?php

namespace App\Http\Controllers\Crm\Athlete;

use App\Http\Controllers\Controller;
use App\Models\NutritionPlan;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    /**
     * Показать страницу дневника питания
     */
    public function index()
    {
        return view('crm.athlete.nutrition');
    }
    
    /**
     * Получить планы питания спортсмена
     */
    public function getPlans(Request $request)
    {
        try {
            $athleteId = auth()->id();
            
            $plans = NutritionPlan::where('athlete_id', $athleteId)
                ->with('nutritionDays')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
            
            // Преобразуем nutritionDays в nutrition_days для совместимости с фронтендом
            $plans->transform(function ($plan) {
                $plan->nutrition_days = $plan->nutritionDays;
                unset($plan->nutritionDays);
                return $plan;
            });
            
            return response()->json($plans);
            
        } catch (\Exception $e) {
            \Log::error('Ошибка загрузки планов питания для спортсмена: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка загрузки планов питания'], 500);
        }
    }
}







